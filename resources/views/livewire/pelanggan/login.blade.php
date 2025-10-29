<div class="w-full">
    {{-- Login Card --}}
    <x-card class="card bg-base-300 shadow-2xl" data-aos="fade-up">
        <x-slot:figure>
            <img src="{{ asset('image/logo.png') }}" alt="Main Laundry Logo"
                class="w-full h-48 p-4 object-contain bg-accent/18" />
        </x-slot:figure>

        {{-- Login dengan Google --}}
        <a href="{{ route('pelanggan.auth.google') }}" class="btn btn-soft border-2 btn-lg btn-block gap-2 my-4">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100"
                viewBox="0 0 48 48">
                <path fill="#FFC107"
                    d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z">
                </path>
                <path fill="#FF3D00"
                    d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z">
                </path>
                <path fill="#4CAF50"
                    d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z">
                </path>
                <path fill="#1976D2"
                    d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z">
                </path>
            </svg>
            Masuk dengan Google
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
