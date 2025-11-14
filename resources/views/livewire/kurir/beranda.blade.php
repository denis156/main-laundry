<section class="bg-base-100 relative overflow-hidden">
    <!-- Background Decorative Capsules -->
    <div class="fixed inset-0 z-0 h-[56dvh] flex items-center justify-center bg-primary/68 rounded-b-2xl"></div>

    <div class="space-y-4">

        {{-- Welcome Card --}}
        <x-card class="bg-base-300 shadow" title="Hai {{ $this->greeting }}" subtitle="{{ $this->todayDate }}" separator>
            <x-slot:menu>
                @if ($this->assignedPos)
                    <x-badge value="{{ $this->assignedPos->name }}" class="badge-primary badge-xs md:badge-sm" />
                @endif
            </x-slot:menu>
            @php
                $courierName = \App\Helper\Database\CourierHelper::getName($this->courier);
                $courierVehicle = \App\Helper\Database\CourierHelper::getVehicleNumber($this->courier);
                $courierPhone = \App\Helper\Database\CourierHelper::getPhone($this->courier);
            @endphp
            <x-avatar :image="$this->courier->getFilamentAvatarUrl()" class="w-24">
                <x-slot:title class="text-lg text-base-content font-bold pl-2">
                    {{ $courierName }}
                </x-slot:title>

                <x-slot:subtitle class="grid gap-0 mt-2 pl-2 text-xs md:text-sm">
                    @if ($courierVehicle)
                        <p class="text-secondary font-mono">{{ $courierVehicle }}</p>
                    @endif
                    <p class="text-secondary font-mono">{{ $this->courier->email }}</p>
                    @if ($courierPhone)
                        <p class="text-secondary font-mono">+62 {{ $courierPhone }}</p>
                    @endif
                </x-slot:subtitle>

            </x-avatar>
        </x-card>

        {{-- Stats Cards Component --}}
        <livewire:kurir.components.stats-beranda />

        {{-- Quick Actions --}}
        <x-card class="bg-base-300 shadow" body-class="grid grid-cols-2 gap-4" title="Aksi Cepat"
            subtitle="Pilih aksi yang ingin kamu lakukan sekarang" separator>
            <x-button link="{{ $this->getWhatsAppCSUrl() }}" target="_blank" icon="solar.chat-round-bold-duotone"
                label="Hubungi CS" class="btn-success btn-lg btn-block col-span-2" external />
            <x-button link="{{ route('kurir.pesanan') }}" icon="solar.bill-list-bold-duotone" label="Pesanan"
                class="btn-accent btn-md" />
            <x-button link="{{ route('kurir.pembayaran') }}" icon="solar.wallet-money-bold-duotone" label="Pembayaran"
                class="btn-accent btn-md" />
        </x-card>

        {{-- New Order Cards Component --}}
        <livewire:kurir.components.new-order-card />

        {{-- Leaderboard Card Component --}}
        <x-card class="bg-base-300 shadow" title="Kurir Terbaik Bulan Ini"
            subtitle="Top 5 kurir dengan pesanan terkirim terbanyak di {{ $this->currentMonth }}" separator>
            <x-table :headers="$headers" :rows="$this->leaders" no-hover striped show-empty-text class="text-center">
                {{-- Custom Rank Cell with Medal/Trophy --}}
                @scope('cell_rank', $leader)
                    @if ($leader->rank === 1)
                        <span class="flex items-center justify-center gap-1">
                            <x-icon name="solar.medal-star-bold-duotone" class="h-8 text-warning" label="1" />
                        </span>
                    @elseif ($leader->rank === 2)
                        <span class="flex items-center justify-center gap-1">
                            <x-icon name="solar.medal-star-bold-duotone" class="h-8 text-base-content/50" label="2" />
                        </span>
                    @elseif ($leader->rank === 3)
                        <span class="flex items-center justify-center gap-1">
                            <x-icon name="solar.medal-star-bold-duotone" class="h-8 text-orange-600" label="3" />
                        </span>
                    @else
                        <span class="text-base-content/50">{{ $leader->rank }}</span>
                    @endif
                @endscope

                {{-- Center align name column --}}
                @scope('cell_name', $leader)
                    <span
                        class="text-center whitespace-nowrap">{{ $leader->display_name ?? \App\Helper\Database\CourierHelper::getName($leader) }}</span>
                @endscope

                {{-- Center align transactions count --}}
                @scope('cell_transactions_count', $leader)
                    <span class="text-center">{{ $leader->transactions_count }}</span>
                @endscope

                {{-- Highlight Current User Row --}}
                @scope('actions', $leader)
                    @if ($leader->id === $this->courier->id)
                        <span class="badge badge-primary badge-xs">Kamu</span>
                    @endif
                @endscope
            </x-table>
        </x-card>
    </div>
</section>
