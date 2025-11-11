<section class="bg-base-100">
    <x-header icon="solar.home-bold-duotone" icon-classes="text-primary w-6 h-6" title="Beranda"
        subtitle="Selamat datang di {{ config('app.name') }}" separator />

    <div class="space-y-4">

        {{-- Welcome Card --}}
        <x-card class="bg-base-300 shadow" title="Hai {{ $this->greeting }}" subtitle="{{ $this->todayDate }}" separator>
            <x-slot:menu>
                <x-badge
                    value="{{ App\Helper\Database\CustomerHelper::isMember($this->customer) ? 'Member' : 'Non-Member' }}"
                    class="{{ App\Helper\Database\CustomerHelper::isMember($this->customer) ? 'badge-primary' : 'badge-neutral' }} badge-xs md:badge-sm" />
            </x-slot:menu>
            <x-avatar image="{{ $this->customer->getFilamentAvatarUrl() }}" class="w-24">
                <x-slot:title class="text-xl text-base-content font-bold pl-2">
                    {{ App\Helper\Database\CustomerHelper::getName($this->customer) }}
                </x-slot:title>

                <x-slot:subtitle class="grid gap-0 mt-2 pl-2 text-xs md:text-sm">
                    <p class="text-secondary font-mono">{{ $this->totalOrdersCount }} Total Pesanan</p>
                    <p class="text-secondary font-mono">{{ $this->customer->email ?? 'Email tidak tersedia' }}</p>
                    <p class="text-secondary font-mono">{{ $this->customer->phone ?? 'Telepon tidak tersedia' }}</p>
                </x-slot:subtitle>
            </x-avatar>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-2 gap-4 mt-4">
                <x-button link="{{ route('pelanggan.buat-pesanan') }}" icon="solar.add-circle-bold-duotone"
                    label="Pesan Sekarang" class="btn-accent btn-lg btn-block col-span-2" />
                <x-button link="{{ route('pelanggan.pesanan') }}" icon="solar.bill-list-bold-duotone" label="Pesanan"
                    class="btn-primary btn-md" />
                <x-button link="{{ $this->getWhatsAppCSUrl() }}" target="_blank" icon="solar.chat-round-bold-duotone"
                    label="Hubungi CS" class="btn-success btn-md" external />
            </div>
        </x-card>

        {{-- Service Cards Component --}}
        <livewire:pelanggan.components.service-card />

        <div class="divider divider-accent mt-8 font-bold text-accent">Pesanan Aktif</div>

        {{-- Active Order Component --}}
        <livewire:pelanggan.components.active-order />
    </div>

</section>
