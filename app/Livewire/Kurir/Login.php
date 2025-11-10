<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Helper\Database\CourierHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

#[Title('Login Kurir')]
#[Layout('components.layouts.kurir-guest')]
class Login extends Component
{
    use Toast;

    public string $email = '';
    public string $password = '';

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function login(): void
    {
        $this->validate();

        // Throttle key berdasarkan email dan IP
        $throttleKey = strtolower($this->email) . '|' . request()->ip();

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

        // Attempt login menggunakan guard 'courier'
        // Always remember user untuk PWA experience (auto-login)
        if (Auth::guard('courier')->attempt(
            ['email' => $this->email, 'password' => $this->password],
            true // Always remember untuk PWA
        )) {
            $courier = Auth::guard('courier')->user();

            // Cek apakah kurir aktif menggunakan CourierHelper
            if (!CourierHelper::isActive($courier)) {
                Auth::guard('courier')->logout();

                $this->error(
                    title: 'Akun Tidak Aktif!',
                    description: 'Akun Anda tidak aktif. Silakan hubungi admin.',
                    position: 'toast-top toast-end',
                    timeout: 4000
                );

                // Hit throttle untuk akun tidak aktif (hanya di production)
                if (!config('app.debug')) {
                    RateLimiter::hit($throttleKey, 300); // 5 menit
                }

                return;
            }

            // Clear throttle jika login berhasil (hanya di production)
            if (!config('app.debug')) {
                RateLimiter::clear($throttleKey);
            }

            session()->regenerate();

            // Get nama courier dari JSONB menggunakan CourierHelper
            $courierName = CourierHelper::getName($courier);

            $this->success(
                title: 'Login Berhasil!',
                description: 'Selamat datang ' . $courierName,
                position: 'toast-top toast-end',
                timeout: 3000,
                redirectTo: route('kurir.beranda')
            );
        } else {
            // Hit throttle untuk setiap percobaan gagal (hanya di production)
            if (!config('app.debug')) {
                RateLimiter::hit($throttleKey, 300); // 5 menit
            }

            $this->error(
                title: 'Login Gagal!',
                description: 'Email atau password yang Anda masukkan salah.',
                position: 'toast-top toast-end',
                timeout: 3000
            );
        }
    }

    public function render()
    {
        return view('livewire.kurir.login');
    }
}
