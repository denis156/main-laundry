<div class="w-full">
    {{-- Login Card --}}
    <x-card class="card bg-base-300 shadow-2xl" data-aos="fade-up">
        <x-slot:figure>
            <img src="{{ asset('image/logo.png') }}" alt="Main Laundry Logo"
                class="w-full h-48 p-4 object-contain bg-accent/18" />
        </x-slot:figure>
        <x-form wire:submit="login" no-separator>
            {{-- Phone Field --}}
            <x-input label="Nomor Telepon" wire:model="phone" icon="o-phone" placeholder="08123456789"
                hint="Nomor telepon yang terdaftar" autofocus />

            {{-- Password Field --}}
            <x-password label="Password" wire:model="password" hint="Minimal 6 karakter" clearable />

            {{-- Remember Me --}}
            <x-checkbox label="Ingat saya" wire:model="remember" />

            {{-- Login Button --}}
            <x-slot:actions>
                <x-button label="Masuk" type="submit" icon="solar.login-linear"
                    class="btn-primary btn-block" spinner="login" />
            </x-slot:actions>
        </x-form>

        {{-- Info Text --}}
        <div class="divider text-sm opacity-50">Untuk Customer Main Laundry</div>

        <div class="text-center text-sm text-base-content/60">
            <p>Belum punya akun? Hubungi admin untuk pendaftaran.</p>
        </div>
    </x-card>
</div>
