<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Exception;
use App\Models\Customer;
use Livewire\Component;
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

            // Cari customer berdasarkan Google ID atau email
            $customer = Customer::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($customer) {
                // Customer sudah ada, update data Google OAuth
                $customer->update([
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken, // Akan null jika tidak ada
                    'email' => $googleUser->getEmail(),
                    'name' => $customer->name ?: $googleUser->getName(),
                    'avatar_url' => $customer->avatar_url ?: $googleUser->getAvatar(),
                ]);

                Log::info('Existing customer logged in via Google OAuth', [
                    'customer_id' => $customer->id,
                    'google_id' => $googleUser->getId(),
                    'email' => $googleUser->getEmail(),
                ]);
            } else {
                // Customer belum ada, buat account baru otomatis
                $customer = Customer::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken, // Akan null jika tidak ada
                    'avatar_url' => $googleUser->getAvatar(),
                    'password' => bcrypt(uniqid()), // Random password karena login via Google
                    'member' => false, // Default bukan member
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

            // Cek apakah data pelanggan lengkap (minimal ada alamat)
            $isProfileIncomplete = empty($customer->district_code) ||
                                   empty($customer->village_code) ||
                                   empty($customer->detail_address);

            if ($isProfileIncomplete) {
                // Redirect ke profil dengan flash message
                return redirect()->route('pelanggan.profil')
                    ->with('warning', 'Silakan lengkapi data profil Anda terlebih dahulu, ' . $customer->name . '!');
            }

            // Redirect ke intended atau beranda dengan flash message
            return redirect()->intended(route('pelanggan.beranda'))
                ->with('success', 'Selamat datang kembali, ' . $customer->name . '!');
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
     * Component ini tidak memiliki view
     * Hanya berisi logika untuk handle OAuth, seperti WebPushApi
     */
    public function render()
    {
        // Tidak perlu render view, component ini hanya untuk logic
        return null;
    }
}
