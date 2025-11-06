<div class="w-full">
    {{-- Login Card --}}
    <x-card class="card bg-base-300 shadow" data-aos="fade-up">
        <x-slot:figure>
            <img src="{{ asset('image/logo.png') }}" alt="Main Laundry Logo"
                class="w-full h-48 p-4 object-contain bg-accent/18" />
        </x-slot:figure>
        <x-form wire:submit="login" no-separator>
            {{-- Email Field --}}
            <x-input label="Email" wire:model="email" icon="o-envelope" placeholder="kurir@example.com"
                hint="Email yang terdaftar" autofocus />

            {{-- Password Field --}}
            <x-password label="Password" wire:model="password" hint="Minimal 6 karakter" clearable />

            {{-- Login Button --}}
            <x-slot:actions>
                <x-button label="Masuk" type="submit" icon="solar.login-linear" class="btn-primary btn-block"
                    spinner="login" />
            </x-slot:actions>
        </x-form>

        {{-- Info Text --}}
        <div class="divider text-sm opacity-50">Untuk Kurir Main Laundry</div>

        <div class="text-center text-sm text-base-content/60">
            <p>Belum punya akun? Hubungi admin untuk pendaftaran.</p>
        </div>
    </x-card>

    {{-- FAB Install App --}}
    <livewire:components.fab-install-app />
</div>
