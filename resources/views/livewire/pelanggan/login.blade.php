<div class="w-full">
    {{-- Login Card --}}
    <x-card class="card bg-base-300 shadow-2xl" data-aos="fade-up">
        <x-slot:figure>
            <img src="{{ asset('image/logo.png') }}" alt="Main Laundry Logo"
                class="w-full h-48 p-4 object-contain bg-accent/18" />
        </x-slot:figure>

        {{-- Login dengan Google --}}
        <a href="{{ route('pelanggan.auth.google') }}" class="btn btn-outline text-base-content btn-lg btn-block gap-2 my-4">
            <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                    fill="#4285F4" />
                <path
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                    fill="#34A853" />
                <path
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                    fill="#FBBC05" />
                <path
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                    fill="#EA4335" />
            </svg>
            Login dengan Google
        </a>
        <div class="divider text-sm opacity-50">Atau masuk dengan</div>

        <x-form wire:submit="login" no-separator>
            {{-- Phone Field --}}
            <x-input label="Nomor Telepon" wire:model="phone" icon="o-phone" placeholder="081234567890"
                hint="Bisa tulis dengan 08 atau langsung 8" autofocus />

            {{-- Password Field --}}
            <x-password label="Password" wire:model="password" hint="Password default: pelanggan_main" clearable />

            {{-- Remember Me --}}
            <x-checkbox label="Ingat saya" wire:model="remember" />

            {{-- Login Button --}}
            <x-slot:actions>
                <x-button label="Masuk" type="submit" icon="solar.login-linear" class="btn-primary btn-sm btn-block"
                    spinner="login" />
            </x-slot:actions>
        </x-form>

        <div class="text-center text-sm text-base-content/60 mt-4">
            <p>Belum punya akun? Buat pesanan pertama kali di halaman depan untuk bisa login.</p>
        </div>
    </x-card>
</div>
