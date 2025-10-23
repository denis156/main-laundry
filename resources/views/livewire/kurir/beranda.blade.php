<section class="bg-base-100">
    <x-header icon="solar.home-bold-duotone" icon-classes="text-primary w-6 h-6" title="Beranda"
        subtitle="Dashboard Kurir {{ config('app.name') }}" separator />

    <div class="space-y-4">
        {{-- Stats Cards --}}
        <div class="stats stats-vertical lg:stats-horizontal shadow-lg hover:shadow-xl transition-shadow w-full">
            <div class="stat bg-success">
                <div class="stat-figure text-base-content">
                    <x-icon name="solar.bill-check-bold-duotone" class="inline-block h-8 stroke-current" />
                </div>
                <div class="stat-title text-base-content">Selesai</div>
                <div class="stat-value text-base-content">{{ $this->completedTransactions }}</div>
                <div class="stat-desc text-base-content">Sudah dikirim semua</div>
            </div>
            <div class="stat bg-info">
                <div class="stat-figure text-base-content">
                    <x-icon name="solar.chat-round-check-bold-duotone" class="inline-block h-8 stroke-current" />
                </div>
                <div class="stat-title text-base-content">Terkonfirmasi</div>
                <div class="stat-value text-base-content">{{ $this->confirmedTransactions }}</div>
                <div class="stat-desc text-base-content">Siap dikerjakan segera</div>
            </div>
        </div>

        {{-- Welcome Card --}}
        <x-card class="bg-base-300 shadow-lg hover:shadow-xl transition-shadow" title="Hai {{ $this->greeting }}" subtitle="{{ $this->todayDate }}" shadow separator>
            <x-slot:menu>
                @if ($this->assignedPos)
                    <x-badge value="{{ $this->assignedPos->name }}" class="badge-primary badge-xs md:badge-sm" />
                @endif
            </x-slot:menu>
            <x-avatar :image="$this->courier->getFilamentAvatarUrl()" class="w-24">
                <x-slot:title class="text-xl text-base-content font-bold pl-2">
                    {{ $this->courier->name }}
                </x-slot:title>

                <x-slot:subtitle class="grid gap-0 mt-2 pl-2 text-xs md:text-sm">
                    <p class="text-secondary font-mono">{{ $this->courier->vehicle_number }}</p>
                    <p class="text-secondary font-mono">{{ $this->courier->email }}</p>
                    <p class="text-secondary font-mono">{{ $this->courier->phone }}</p>
                </x-slot:subtitle>

            </x-avatar>
        </x-card>

        {{-- Quick Actions --}}
        <x-card class="bg-base-300 shadow-lg hover:shadow-xl transition-shadow" body-class="grid grid-cols-2 gap-4" title="Aksi Cepat"
            subtitle="Navigasi cepat untuk tugas harian" shadow separator>
            <x-button link="{{ route('kurir.pesanan') }}" icon="solar.bill-list-bold-duotone" label="Lihat Pesanan"
                class="btn-primary btn-sm" />
            <x-button link="{{ route('kurir.pembayaran') }}" icon="solar.wallet-money-bold-duotone" label="Pembayaran"
                class="btn-secondary btn-sm" />
            <x-button link="{{ $this->getWhatsAppCSUrl() }}" target="_blank" icon="solar.chat-round-bold-duotone"
                label="Hubungi CS" class="btn-success btn-sm" external />
            <x-button link="{{ route('kurir.info') }}" icon="solar.info-circle-bold-duotone" label="Info & FAQ"
                class="btn-info btn-sm" />
        </x-card>

        {{-- New Order Cards Component --}}
        <livewire:kurir.components.new-order-card />
    </div>
</section>
