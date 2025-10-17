<div
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary/10 via-accent/5 to-base-200 px-4">
    <div class="w-full max-w-md">
        {{-- Login Card --}}
        <x-card class="card bg-base-100 shadow-2xl" data-aos="fade-up">
            <x-slot:figure>
                <img src="{{ asset('image/logo.png') }}" alt="Main Laundry Logo"
                    class="w-full h-48 object-contain bg-primary/5" />
            </x-slot:figure>
            <x-form wire:submit="login" no-separator>
                {{-- Email Field --}}
                <x-input label="Email" wire:model="email" icon="o-envelope" placeholder="kurir@example.com"
                    hint="Email yang terdaftar" autofocus />

                {{-- Password Field --}}
                <x-password label="Password" wire:model="password" hint="Minimal 6 karakter" clearable />

                {{-- Remember Me --}}
                <x-checkbox label="Ingat saya" wire:model="remember" />

                {{-- Login Button --}}
                <x-slot:actions>
                    <x-button label="Masuk" type="submit" icon="o-arrow-right-end-on-rectangle"
                        class="btn-primary btn-block" spinner="login" />
                </x-slot:actions>
            </x-form>

            {{-- Info Text --}}
            <div class="divider text-sm opacity-50">Untuk Kurir Main Laundry</div>

            <div class="text-center text-sm text-base-content/60">
                <p>Belum punya akun? Hubungi admin untuk pendaftaran.</p>
            </div>
        </x-card>

        {{-- Footer --}}
        <div class="text-center mt-8 text-sm text-base-content/50" data-aos="fade-up" data-aos-delay="200">
            <p class="font-semibold text-secondary">Powered by <span class="font-bold text-accent">MAIN GROUP</span></p>
        </div>
    </div>
</div>
