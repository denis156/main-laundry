@php use App\Helper\StatusTransactionHelper; @endphp
<section class="bg-base-100 min-h-dvh w-full">
    {{-- Header --}}
    <x-header icon="solar.bill-list-bold-duotone" icon-classes="text-primary w-6 h-6" title="Pesanan Saya"
        subtitle="Lihat semua pesanan laundry kamu" separator progress-indicator>
        <x-slot:middle class="justify-end">
            <x-input wire:model.live.debounce.500ms="search" icon="solar.magnifer-bold-duotone"
                placeholder="Cari invoice..." clearable />
        </x-slot:middle>
        <x-slot:actions>
            <x-dropdown no-x-anchor right>
                <x-slot:trigger>
                    <x-button icon="solar.filter-bold-duotone" class="btn-circle btn-primary" />
                </x-slot:trigger>
                <x-menu-item title="Semua Status" icon="solar.widget-bold-duotone" wire:click="$set('filter', 'all')" />
                <x-menu-item title="Menunggu" icon="solar.clock-circle-bold-duotone"
                    wire:click="$set('filter', 'pending_confirmation')" />
                <x-menu-item title="Diproses" icon="solar.washing-machine-bold-duotone"
                    wire:click="$set('filter', 'in_washing')" />
                <x-menu-item title="Siap Antar" icon="solar.check-read-bold-duotone"
                    wire:click="$set('filter', 'washing_completed')" />
                <x-menu-item title="Diantar" icon="solar.delivery-bold-duotone"
                    wire:click="$set('filter', 'out_for_delivery')" />
                <x-menu-item title="Selesai" icon="solar.star-bold-duotone" wire:click="$set('filter', 'delivered')" />
                <x-menu-item title="Batal" icon="solar.close-circle-bold-duotone"
                    wire:click="$set('filter', 'cancelled')" />
            </x-dropdown>
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Stats Summary --}}
        <div class="stats stats-vertical lg:stats-horizontal shadow-lg w-full">
            <div class="stat bg-primary">
                <div class="stat-figure text-base-content">
                    <x-icon name="solar.clock-circle-bold-duotone" class="inline-block h-8 stroke-current" />
                </div>
                <div class="stat-title text-base-content">Aktif</div>
                <div class="stat-value text-base-content">3</div>
                <div class="stat-desc text-base-content">Sedang diproses</div>
            </div>
            <div class="stat bg-success">
                <div class="stat-figure text-base-content">
                    <x-icon name="solar.check-circle-bold-duotone" class="inline-block h-8 stroke-current" />
                </div>
                <div class="stat-title text-base-content">Selesai</div>
                <div class="stat-value text-base-content">12</div>
                <div class="stat-desc text-base-content">Total pesanan selesai</div>
            </div>
        </div>

        {{-- Order Cards --}}
        <div class="space-y-3">
            {{-- Sample Order 1 --}}
            <div class="card bg-base-300 shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body p-4">
                    {{-- Header: Invoice & Status --}}
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-primary text-lg">#ORD-001</h3>
                            <p class="text-xs text-base-content/60">20 Okt 2025, 14:30</p>
                        </div>
                        <span class="badge badge-warning gap-1">Sedang Dicuci</span>
                    </div>

                    <div class="divider my-2"></div>

                    {{-- Order Info --}}
                    <div class="bg-base-200 rounded-lg p-3 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Layanan</span>
                            <span class="font-semibold">Cuci Kering</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Berat</span>
                            <span class="font-semibold">5 kg</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Harga/kg</span>
                            <span class="font-semibold">Rp 7.000</span>
                        </div>
                        <div class="divider my-1"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Total</span>
                            <span class="font-semibold text-right text-primary text-base">Rp 35.000</span>
                        </div>
                    </div>

                    {{-- Action Button --}}
                    <x-button label="Detail Pesanan" link="{{ route('pelanggan.pesanan.detail', 1) }}"
                        icon="solar.bill-list-bold-duotone" class="btn-primary btn-sm btn-block mt-3" />
                </div>
            </div>

            {{-- Sample Order 2 --}}
            <div class="card bg-base-300 shadow-lg hover:shadow-xl transition-shadow">
                <div class="card-body p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-primary text-lg">#ORD-002</h3>
                            <p class="text-xs text-base-content/60">18 Okt 2025, 10:00</p>
                        </div>
                        <span class="badge badge-success gap-1">Selesai</span>
                    </div>

                    <div class="divider my-2"></div>

                    <div class="bg-base-200 rounded-lg p-3 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Layanan</span>
                            <span class="font-semibold">Cuci Setrika</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Berat</span>
                            <span class="font-semibold">3 kg</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Harga/kg</span>
                            <span class="font-semibold">Rp 9.000</span>
                        </div>
                        <div class="divider my-1"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Total</span>
                            <span class="font-semibold text-right text-primary text-base">Rp 27.000</span>
                        </div>
                    </div>

                    <x-button label="Detail Pesanan" link="{{ route('pelanggan.pesanan.detail', 2) }}"
                        icon="solar.bill-list-bold-duotone" class="btn-primary btn-sm btn-block mt-3" />
                </div>
            </div>
        </div>

        {{-- Empty State --}}
        {{-- <div class="card bg-base-300 shadow-lg">
            <div class="card-body items-center text-center py-12">
                <x-icon name="solar.inbox-bold-duotone" class="w-16 h-16 text-base-content/20 mb-4" />
                <h3 class="font-bold text-lg">Belum Ada Pesanan</h3>
                <p class="text-base-content/60 mb-4">Yuk mulai pesan layanan laundry sekarang!</p>
                <x-button label="Pesan Sekarang" link="{{ route('pelanggan.beranda') }}"
                    icon="solar.add-circle-bold-duotone" class="btn-primary btn-sm" />
            </div>
        </div> --}}
    </div>
</section>
