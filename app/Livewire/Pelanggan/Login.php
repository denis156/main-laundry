<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan;

use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

#[Title('Login Pelanggan')]
#[Layout('components.layouts.pelanggan-guest')]
class Login extends Component
{
    use Toast;

    public string $phone = '';
    public string $password = '';

    /**
     * Mount: Check splash screen status
     * Redirect ke splash screen jika belum pernah lihat
     */
    public function mount(): void
    {
        // Check via JavaScript apakah splash screen sudah completed
        $this->js(<<<'JS'
            const splashCompleted = localStorage.getItem('splash_screen_completed');

            // Jika belum pernah lihat splash screen, redirect ke splash screen page
            if (!splashCompleted || splashCompleted !== 'true') {
                window.location.href = '/pelanggan/splash-screen-pelanggan';
            }
        JS);
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'min:9', 'max:13'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    /**
     * Format phone number untuk database
     * Hilangkan "0" di depan jika dimulai dengan "0"
     */
    private function formatPhoneForDatabase(string $phone): string
    {
        // Hilangkan semua karakter non-numeric
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Jika dimulai dengan "0", hilangkan
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1);
        }

        return $cleanPhone;
    }

    public function login(): void
    {
        // Format phone number sebelum validasi
        $this->phone = $this->formatPhoneForDatabase($this->phone);

        $this->validate();

        // Throttle key berdasarkan phone dan IP
        $throttleKey = strtolower($this->phone) . '|' . request()->ip();

        // Cek apakah terlalu banyak percobaan login (hanya di production)
        if (!config('app.debug') && RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            $this->error(
                title: 'Terlalu Banyak Percobaan!',
                description: "Silakan coba lagi dalam {$minutes} menit.",
                position: 'toast-top toast-end',
                timeout: 5000
            );

            return;
        }

        // Attempt login menggunakan guard 'customer' dengan phone
        // Always remember user untuk PWA experience (auto-login)
        if (Auth::guard('customer')->attempt(
            ['phone' => $this->phone, 'password' => $this->password],
            true // Always remember untuk PWA
        )) {
            $customer = Auth::guard('customer')->user();

            // Clear throttle jika login berhasil (hanya di production)
            if (!config('app.debug')) {
                RateLimiter::clear($throttleKey);
            }

            session()->regenerate();

            // Cek apakah data pelanggan lengkap (district, village, dan detail address)
            $isProfileIncomplete = empty($customer->district_code) ||
                                   empty($customer->village_code) ||
                                   empty($customer->detail_address);

            if ($isProfileIncomplete) {
                // Set session flash untuk menampilkan modal lengkapi profil
                session()->flash('show_lengkapi_profil_modal', [
                    'customer_name' => $customer->name,
                    'redirect_from' => 'login'
                ]);

                $this->success(
                    title: 'Login Berhasil!',
                    description: 'Selamat datang ' . $customer->name,
                    position: 'toast-top toast-end',
                    timeout: 3000,
                    redirectTo: route('pelanggan.beranda')
                );
            } else {
                $this->success(
                    title: 'Login Berhasil!',
                    description: 'Selamat datang ' . $customer->name,
                    position: 'toast-top toast-end',
                    timeout: 3000,
                    redirectTo: route('pelanggan.beranda')
                );
            }
        } else {
            // Hit throttle untuk setiap percobaan gagal (hanya di production)
            if (!config('app.debug')) {
                RateLimiter::hit($throttleKey, 300); // 5 menit
            }

            $this->error(
                title: 'Login Gagal!',
                description: 'Nomor telepon atau password yang Anda masukkan salah.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
        }
    }

    public function render()
    {
        return view('livewire.pelanggan.login');
    }
}
