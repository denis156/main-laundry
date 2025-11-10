<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Exception;
use App\Models\Customer;
use Livewire\Component;
use App\Helper\Database\CustomerHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

/**
 * Google OAuth Authentication Component
 *
 * Menangani autentikasi customer menggunakan Google OAuth 2.0
 * - Auto-register customer baru jika belum ada di database
 * - Update data Google OAuth untuk customer yang sudah ada
 * - Menyimpan OAuth token untuk akses Google API di masa depan
 *
 * Component ini tidak memiliki view, hanya berisi logika seperti WebPushApi
 */
class GoogleAuth extends Component
{
    /**
     * Redirect ke Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google
     * Dipanggil dari route setelah user authorize di Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cari customer berdasarkan Google ID di JSONB atau email
            $customer = Customer::where('email', $googleUser->getEmail())
                ->orWhereRaw("data->>'google_oauth'->>'google_id' = ?", [$googleUser->getId()])
                ->first();

            if ($customer) {
                // Customer sudah ada, update data Google OAuth di JSONB
                $data = $customer->data ?? [];

                // Update Google OAuth data
                $data['google_oauth'] = [
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken, // Akan null jika tidak ada
                ];

                // Update email jika berubah
                if ($customer->email !== $googleUser->getEmail()) {
                    $customer->email = $googleUser->getEmail();
                }

                // Update name jika masih kosong
                if (empty($data['name'])) {
                    $data['name'] = $googleUser->getName();
                }

                // Update avatar_url jika masih kosong
                if (empty($data['avatar_url'])) {
                    $data['avatar_url'] = $googleUser->getAvatar();
                }

                $customer->data = $data;
                $customer->save();

                Log::info('Existing customer logged in via Google OAuth', [
                    'customer_id' => $customer->id,
                    'google_id' => $googleUser->getId(),
                    'email' => $googleUser->getEmail(),
                ]);
            } else {
                // Customer belum ada, buat account baru otomatis
                $customer = Customer::create([
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(uniqid()), // Random password karena login via Google
                    'data' => [
                        'name' => $googleUser->getName(),
                        'avatar_url' => $googleUser->getAvatar(),
                        'member' => false, // Default bukan member
                        'google_oauth' => [
                            'google_id' => $googleUser->getId(),
                            'google_token' => $googleUser->token,
                            'google_refresh_token' => $googleUser->refreshToken, // Akan null jika tidak ada
                        ],
                        'addresses' => [], // Empty addresses array
                    ],
                ]);

                Log::info('New customer registered via Google OAuth', [
                    'customer_id' => $customer->id,
                    'google_id' => $googleUser->getId(),
                    'email' => $googleUser->getEmail(),
                    'name' => $googleUser->getName(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            // Login customer dengan remember
            Auth::guard('customer')->login($customer, true);

            // Regenerate session untuk keamanan
            request()->session()->regenerate();

            // Cek apakah data pelanggan lengkap (harus punya minimal 1 alamat lengkap)
            $data = $customer->data ?? [];
            $addresses = $data['addresses'] ?? [];
            $isProfileIncomplete = empty($addresses) || !$this->hasCompleteAddress($addresses);

            // Get customer name dari JSONB
            $customerName = $data['name'] ?? 'Pelanggan';

            // Redirect ke intended atau beranda dengan flash message
            $redirectResponse = redirect()->intended(route('pelanggan.beranda'))
                ->with('success', 'Selamat datang kembali, ' . $customerName . '!');

            if ($isProfileIncomplete) {
                // Simpan data modal ke session untuk ditampilkan di halaman beranda
                session()->flash('show_lengkapi_profil_modal', [
                    'customer_name' => $customerName,
                    'redirect_from' => 'google-auth'
                ]);
            }

            return $redirectResponse;
        } catch (Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('pelanggan.login')
                ->with('error', 'Login dengan Google gagal: ' . $e->getMessage());
        }
    }

    /**
     * Check apakah customer punya minimal 1 alamat yang lengkap
     */
    private function hasCompleteAddress(array $addresses): bool
    {
        foreach ($addresses as $address) {
            // Cek apakah semua field penting terisi
            if (
                !empty($address['district_code']) &&
                !empty($address['village_code']) &&
                !empty($address['detail_address'])
            ) {
                return true;
            }
        }

        return false;
    }
}
