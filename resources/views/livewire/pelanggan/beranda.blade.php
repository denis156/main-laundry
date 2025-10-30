<section class="bg-base-100">
    <x-header icon="solar.home-bold-duotone" icon-classes="text-primary w-6 h-6" title="Beranda"
        subtitle="Selamat datang di {{ config('app.name') }}" separator />

    <div class="space-y-4">
        {{-- Stats Cards Component --}}
        <livewire:pelanggan.components.stats />

        {{-- Welcome Card --}}
        <x-card class="bg-base-300 shadow" title="Hai {{ $this->greeting }}" subtitle="{{ $this->todayDate }}" separator>
            <x-slot:menu>
                <x-badge value="{{ $this->customer->member ? 'Member' : 'Non-Member' }}"
                    class="{{ $this->customer->member ? 'badge-primary' : 'badge-neutral' }} badge-xs md:badge-sm" />
            </x-slot:menu>
            <x-avatar image="{{ $this->customer->getFilamentAvatarUrl() }}" class="w-24">
                <x-slot:title class="text-xl text-base-content font-bold pl-2">
                    {{ $this->customer->name }}
                </x-slot:title>

                <x-slot:subtitle class="grid gap-0 mt-2 pl-2 text-xs md:text-sm">
                    <p class="text-secondary font-mono">{{ $this->totalOrdersCount }} Total Pesanan</p>
                    <p class="text-secondary font-mono">{{ $this->customer->email ?? 'Email tidak tersedia' }}</p>
                    <p class="text-secondary font-mono">{{ $this->customer->phone ?? 'Telepon tidak tersedia' }}</p>
                </x-slot:subtitle>
            </x-avatar>
        </x-card>

        {{-- Quick Actions --}}
        <x-card class="bg-base-300 shadow" body-class="grid grid-cols-2 gap-4" title="Aksi Cepat"
            subtitle="Pilih aksi yang ingin kamu lakukan sekarang" separator>
            <x-button link="{{ route('pelanggan.buat-pesanan') }}" icon="solar.add-circle-bold-duotone"
                label="Pesan Sekarang" class="btn-accent btn-lg btn-block col-span-2" />
            <x-button link="{{ route('pelanggan.pesanan') }}" icon="solar.bill-list-bold-duotone" label="Pesanan"
                class="btn-primary btn-md" />
            <x-button link="{{ $this->getWhatsAppCSUrl() }}" target="_blank" icon="solar.chat-round-bold-duotone"
                label="Hubungi CS" class="btn-success btn-md" external />
        </x-card>

        {{-- Service Cards Component --}}
        <x-card class="bg-base-300 shadow" title="Layanan Kami" subtitle="Pilih layanan sesuai kebutuhan kamu"
            separator>
            <livewire:pelanggan.components.service-card />
        </x-card>

        <div class="divider divider-accent text-accent font-bold">Pesanan Aktif</div>

        {{-- Active Order Component --}}
        <livewire:pelanggan.components.active-order />
    </div>
</section>
