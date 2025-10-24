@php use App\Helper\StatusTransactionHelper; @endphp
<section class="bg-base-100 min-h-dvh w-full">
    {{-- Header --}}
    <x-header icon="solar.bill-list-bold-duotone" icon-classes="text-primary w-6 h-6" title="Pesanan"
        subtitle="Monitor & Proses Pesanan Pelanggan" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input wire:model.live.debounce.500ms="search" icon="solar.magnifer-bold-duotone"
                placeholder="Cari invoice atau customer..." clearable />
        </x-slot:middle>
        <x-slot:actions>
            <x-dropdown no-x-anchor right>
                <x-slot:trigger>
                    <x-button icon="solar.filter-bold-duotone" class="btn-circle btn-primary" />
                </x-slot:trigger>
                <x-menu-item title="Semua Status" icon="solar.widget-bold-duotone" wire:click="$set('filter', 'all')" />
                <x-menu-item title="Konfirmasi?" icon="solar.clock-circle-bold-duotone"
                    wire:click="$set('filter', 'pending_confirmation')" />
                <x-menu-item title="Terkonfirmasi" icon="solar.check-circle-bold-duotone"
                    wire:click="$set('filter', 'confirmed')" />
                <x-menu-item title="Dijemput" icon="solar.box-bold-duotone" wire:click="$set('filter', 'picked_up')" />
                <x-menu-item title="Di Pos" icon="solar.map-point-bold-duotone"
                    wire:click="$set('filter', 'at_loading_post')" />
                <x-menu-item title="Dicuci" icon="solar.washing-machine-bold-duotone"
                    wire:click="$set('filter', 'in_washing')" />
                <x-menu-item title="Siap Antar" icon="solar.check-read-bold-duotone"
                    wire:click="$set('filter', 'washing_completed')" />
                <x-menu-item title="Mengantar" icon="solar.delivery-bold-duotone"
                    wire:click="$set('filter', 'out_for_delivery')" />
                <x-menu-item title="Selesai" icon="solar.star-bold-duotone" wire:click="$set('filter', 'delivered')" />
                <x-menu-item title="Batal" icon="solar.close-circle-bold-duotone"
                    wire:click="$set('filter', 'cancelled')" />
            </x-dropdown>
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Stats Cards Component --}}
        <livewire:kurir.components.stats-pesanan />

        {{-- Transaction Cards --}}
        <div class="space-y-3">
            @forelse ($this->transactions as $transaction)
                <div class="card bg-base-300 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body p-4">
                        {{-- Header: Invoice & Status --}}
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="font-bold text-primary text-lg">
                                    {{ $transaction->invoice_number }}
                                </h3>
                                <p class="text-xs text-base-content/60">
                                    {{ $transaction->formatted_order_date }}
                                </p>
                            </div>
                            @php
                                $statusColor = StatusTransactionHelper::getStatusBadgeColor(
                                    $transaction->workflow_status,
                                );
                                $statusText = StatusTransactionHelper::getStatusText($transaction->workflow_status);
                            @endphp
                            <span class="badge {{ $statusColor }} gap-1">
                                {{ $statusText }}
                            </span>
                        </div>

                        <div class="divider my-2"></div>

                        {{-- Customer Info --}}
                        <div class="flex items-center gap-3 mb-2">
                            <div class="avatar avatar-placeholder">
                                <div class="bg-primary text-primary-content w-10 rounded-full">
                                    <span class="text-sm">{{ $transaction->customer?->getInitials() ?? 'NA' }}</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold">
                                    {{ $transaction->customer?->name ?? 'Customer tidak ditemukan' }}</p>
                                <p class="text-xs text-base-content/60">{{ $transaction->customer?->phone ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Order Info --}}
                        @if (
                            $transaction->service_id ||
                                $transaction->pos_id ||
                                $transaction->weight ||
                                $transaction->payment_timing ||
                                $transaction->customer?->address)
                            <div class="bg-base-200 rounded-lg p-3 space-y-2">
                                {{-- Layanan --}}
                                @if ($transaction->service_id)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-base-content/70">Layanan</span>
                                        <span class="font-semibold">{{ $transaction->service?->name ?? 'N/A' }}</span>
                                    </div>
                                @endif

                                {{-- Berat --}}
                                @if ($transaction->weight)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-base-content/70">Berat</span>
                                        <span class="font-semibold">{{ $transaction->weight }} kg</span>
                                    </div>
                                @endif

                                {{-- Pos --}}
                                @if ($transaction->pos_id)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-base-content/70">Pos</span>
                                        <span class="font-semibold">{{ $transaction->pos?->name ?? 'N/A' }}</span>
                                    </div>
                                @endif

                                {{-- Metode Pembayaran --}}
                                @if ($transaction->payment_timing)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-base-content/70">Metode Pembayaran</span>
                                        <span class="font-semibold">
                                            {{ $transaction->payment_timing_text }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Alamat --}}
                                @if ($transaction->customer?->address)
                                    @if ($transaction->service_id || $transaction->pos_id || $transaction->weight || $transaction->payment_timing)
                                        <div class="divider my-1"></div>
                                    @endif
                                    <div>
                                        <p class="text-xs text-base-content/70 mb-1">Alamat</p>
                                        <p class="text-sm font-semibold text-primary leading-relaxed">
                                            {{ $transaction->customer->address }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Notes --}}
                        @if ($transaction->notes)
                            <div class="mt-2 p-3 bg-base-200 rounded-lg">
                                <p class="text-xs text-base-content/70 mb-1">Catatan</p>
                                <p class="text-sm">{{ $transaction->notes }}</p>
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="mt-3 space-y-2">
                            @if ($transaction->workflow_status === 'pending_confirmation')
                                {{-- Status: Pending Confirmation - Tampilkan Batalkan + Ambil Pesanan --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <button wire:click="cancelOrder({{ $transaction->id }})"
                                        class="btn btn-error btn-sm">
                                        <x-icon name="solar.close-circle-bold-duotone" class="w-4 h-4" />
                                        Batalkan Pesanan
                                    </button>

                                    <button wire:click="confirmOrder({{ $transaction->id }})"
                                        class="btn btn-accent btn-sm">
                                        <x-icon name="solar.check-circle-bold-duotone" class="w-4 h-4" />
                                        Ambil Pesanan
                                    </button>
                                </div>
                            @elseif ($transaction->workflow_status === 'confirmed')
                                {{-- Status: Confirmed - Tampilkan Input Berat + WhatsApp + Dijemput --}}
                                <div class="bg-base-200 rounded-lg p-3 mb-2">
                                    {{-- Input Berat --}}
                                    <x-input wire:model.blur="weights.{{ $transaction->id }}" type="number"
                                        step="0.01" min="0.01" label="Berat Cucian (kg)"
                                        placeholder="Contoh: 8.92" icon="solar.scale-bold-duotone"
                                        hint="Total harga akan muncul setelah input berat" />
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    @if ($transaction->customer?->phone && $transaction->customer?->name)
                                        <a href="{{ $this->getWhatsAppUrl($transaction->customer->phone, $transaction->customer->name, $transaction) }}"
                                            target="_blank" class="btn btn-success btn-sm">
                                            <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                                            WhatsApp
                                        </a>
                                    @endif

                                    <button wire:click="markAsPickedUp({{ $transaction->id }})"
                                        class="btn btn-warning btn-sm {{ $transaction->customer?->phone && $transaction->customer?->name ? '' : 'col-span-2' }}"
                                        @if (empty($weights[$transaction->id]) || $weights[$transaction->id] <= 0) disabled @endif>
                                        <x-icon name="solar.box-bold-duotone" class="w-4 h-4" />
                                        Dijemput
                                    </button>
                                </div>
                            @elseif ($transaction->workflow_status === 'picked_up')
                                {{-- Status: Picked Up - Tampilkan Sudah di Pos + Detail --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <button wire:click="markAsAtLoadingPost({{ $transaction->id }})"
                                        class="btn btn-warning btn-sm">
                                        <x-icon name="solar.map-point-bold-duotone" class="w-4 h-4" />
                                        Sudah di Pos
                                    </button>

                                    <a href="{{ route('kurir.pesanan.detail', $transaction->id) }}"
                                        class="btn btn-primary btn-sm">
                                        <x-icon name="solar.bill-list-bold-duotone" class="w-4 h-4" />
                                        Detail
                                    </a>
                                </div>
                            @elseif ($transaction->workflow_status === 'washing_completed')
                                {{-- Status: Washing Completed - Tampilkan WhatsApp + Mengantar --}}
                                <div class="grid grid-cols-2 gap-2">
                                    @if ($transaction->customer?->phone && $transaction->customer?->name)
                                        <a href="{{ $this->getWhatsAppUrlForDelivery($transaction->customer->phone, $transaction->customer->name, $transaction) }}"
                                            target="_blank" class="btn btn-success btn-sm">
                                            <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                                            WhatsApp
                                        </a>
                                    @endif

                                    <button wire:click="markAsOutForDelivery({{ $transaction->id }})"
                                        class="btn btn-accent btn-sm {{ $transaction->customer?->phone && $transaction->customer?->name ? '' : 'col-span-2' }}">
                                        <x-icon name="solar.delivery-bold-duotone" class="w-4 h-4" />
                                        Mengantar
                                    </button>
                                </div>
                            @elseif ($transaction->workflow_status === 'out_for_delivery')
                                {{-- Status: Out for Delivery - Tampilkan Terkirim + Detail --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <button wire:click="markAsDelivered({{ $transaction->id }})"
                                        class="btn btn-success btn-sm"
                                        @if ($transaction->payment_status !== 'paid') disabled @endif>
                                        <x-icon name="solar.check-circle-bold-duotone" class="w-4 h-4" />
                                        Terkirim
                                    </button>
                                    <a href="{{ route('kurir.pesanan.detail', $transaction->id) }}"
                                        class="btn btn-primary btn-sm">
                                        <x-icon name="solar.bill-list-bold-duotone" class="w-4 h-4" />
                                        Detail
                                    </a>
                                </div>
                            @else
                                {{-- Status lain (at_loading_post, in_washing, delivered, cancelled) - Hanya tampilkan Detail --}}
                                <a href="{{ route('kurir.pesanan.detail', $transaction->id) }}"
                                    class="btn btn-primary btn-sm w-full">
                                    <x-icon name="solar.bill-list-bold-duotone" class="w-4 h-4" />
                                    Detail Pesanan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="card bg-base-300 shadow-lg">
                    <div class="card-body items-center text-center py-12">
                        <x-icon name="solar.inbox-bold-duotone" class="w-16 h-16 text-base-content/20 mb-4" />
                        <h3 class="font-bold text-lg">Tidak Ada Pesanan</h3>
                        <p class="text-base-content/60">
                            @if ($filter === 'all')
                                Belum ada pesanan untuk ditampilkan.
                            @else
                                Tidak ada pesanan dengan status ini.
                            @endif
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
