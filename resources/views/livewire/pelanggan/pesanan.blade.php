@php use App\Helper\StatusTransactionCustomerHelper; @endphp
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
                <x-menu-item title="Pending" icon="solar.clock-circle-bold-duotone"
                    wire:click="$set('filter', 'pending_confirmation')" />
                <x-menu-item title="Dikonfirmasi" icon="solar.check-circle-bold-duotone"
                    wire:click="$set('filter', 'confirmed')" />
                <x-menu-item title="Diproses" icon="solar.box-bold-duotone"
                    wire:click="$set('filter', 'picked_up')" />
                <x-menu-item title="Dicuci" icon="solar.washing-machine-bold-duotone"
                    wire:click="$set('filter', 'in_washing')" />
                <x-menu-item title="Diantar" icon="solar.delivery-bold-duotone"
                    wire:click="$set('filter', 'out_for_delivery')" />
                <x-menu-item title="Selesai" icon="solar.star-bold-duotone" wire:click="$set('filter', 'delivered')" />
                <x-menu-item title="Batal" icon="solar.close-circle-bold-duotone"
                    wire:click="$set('filter', 'cancelled')" />
            </x-dropdown>
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Stats Cards Component --}}
        <livewire:pelanggan.components.stats />

        {{-- Order Cards --}}
        <div class="space-y-3">
            @forelse ($this->transactions as $transaction)
                <div class="card bg-base-300 shadow">
                    <div class="card-body p-4">
                        {{-- Header: Invoice & Eye Button --}}
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="font-bold text-primary text-lg">{{ $transaction->invoice_number }}</h3>
                                <p class="text-xs text-base-content/60">{{ $transaction->formatted_order_date }}</p>
                            </div>
                            <x-button
                                icon="solar.eye-bold"
                                class="btn-circle btn-accent btn-md"
                                link="{{ route('pelanggan.pesanan.detail', $transaction->id) }}" />
                        </div>

                        <div class="divider my-2"></div>

                        {{-- Courier Info (show if confirmed and has courier) --}}
                        @if ($transaction->courierMotorcycle && $transaction->workflow_status !== 'pending_confirmation')
                            <div class="flex items-center gap-3 mb-2">
                                <div class="avatar">
                                    <div class="ring-accent ring-offset-base-100 w-10 h-10 rounded-full ring-2 ring-offset-2">
                                        <img src="{{ $transaction->courierMotorcycle->getFilamentAvatarUrl() }}"
                                             alt="{{ $transaction->courierMotorcycle->name ?? 'Kurir' }}" />
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold">{{ $transaction->courierMotorcycle->name }}</p>
                                    <p class="text-xs text-base-content/60">{{ $transaction->courierMotorcycle->phone }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Order Info --}}
                        <div class="bg-base-200 rounded-lg p-3 space-y-2">
                            {{-- Status --}}
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Status</span>
                                <span class="badge {{ StatusTransactionCustomerHelper::getStatusBadgeColor($transaction->workflow_status) }}">
                                    {{ StatusTransactionCustomerHelper::getStatusText($transaction->workflow_status) }}
                                </span>
                            </div>

                            @if ($transaction->service_id)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-base-content/70">Layanan</span>
                                    <span class="font-semibold">{{ $transaction->service?->name }}</span>
                                </div>
                            @endif

                            @if ($transaction->weight)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-base-content/70">Berat</span>
                                    <span class="font-semibold">{{ $transaction->weight }} kg</span>
                                </div>
                            @endif

                            @if ($transaction->price_per_kg)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-base-content/70">Harga/kg</span>
                                    <span class="font-semibold">{{ $transaction->formatted_price_per_kg }}</span>
                                </div>
                            @endif

                            @if ($transaction->payment_timing)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-base-content/70">Metode Pembayaran</span>
                                    <span class="font-semibold">{{ $transaction->payment_timing_text }}</span>
                                </div>
                            @endif

                            @if ($transaction->total_price)
                                <div class="divider my-1"></div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-base-content/70">Total</span>
                                    <span class="font-semibold text-right text-primary text-base">{{ $transaction->formatted_total_price }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Notes --}}
                        @if ($transaction->notes)
                            <div class="mt-2 p-3 bg-base-200 rounded-lg">
                                <p class="text-xs text-base-content/70 mb-1">Catatan</p>
                                <p class="text-sm">{{ $transaction->notes }}</p>
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="mt-3">
                            @if ($transaction->workflow_status === 'pending_confirmation')
                                {{-- Status: Pending - Show Cancel button --}}
                                <x-button wire:click="openCancelModal({{ $transaction->id }})"
                                    label="Batalkan Pesanan"
                                    icon="solar.close-circle-bold-duotone"
                                    class="btn-error btn-sm btn-block" />
                            @elseif (in_array($transaction->workflow_status, ['confirmed', 'picked_up', 'at_loading_post']))
                                {{-- Status: Diproses - Show WA to Kurir --}}
                                @if ($transaction->courierMotorcycle)
                                    <x-button link="{{ $this->getWhatsAppKurirUrl($transaction) }}"
                                        external
                                        label="Hubungi Kurir"
                                        icon="solar.chat-round-bold-duotone"
                                        class="btn-success btn-sm btn-block" />
                                @endif
                            @elseif (in_array($transaction->workflow_status, ['in_washing', 'washing_completed']))
                                {{-- Status: Dicuci - Show WA to Admin --}}
                                <x-button link="{{ $this->getWhatsAppAdminUrl($transaction) }}"
                                    external
                                    label="Hubungi Admin"
                                    icon="solar.chat-round-bold-duotone"
                                    class="btn-success btn-sm btn-block" />
                            @elseif ($transaction->workflow_status === 'out_for_delivery')
                                {{-- Status: Diantar - Show WA to Kurir --}}
                                @if ($transaction->courierMotorcycle)
                                    <x-button link="{{ $this->getWhatsAppKurirUrl($transaction) }}"
                                        external
                                        label="Hubungi Kurir"
                                        icon="solar.chat-round-bold-duotone"
                                        class="btn-success btn-sm btn-block" />
                                @endif
                            @endif
                            {{-- Status: Delivered & Cancelled - No button --}}
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="card bg-base-300 shadow">
                    <div class="card-body items-center text-center py-12">
                        <x-icon name="solar.inbox-bold-duotone" class="w-16 h-16 text-base-content/20 mb-4" />
                        <h3 class="font-bold text-lg">Belum Ada Pesanan</h3>
                        <p class="text-base-content/60 mb-4">Yuk mulai pesan layanan laundry sekarang!</p>
                        <x-button label="Pesan Sekarang" link="{{ route('pelanggan.buat-pesanan') }}"
                            icon="solar.add-circle-bold-duotone" class="btn-primary btn-sm" />
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination Buttons --}}
        <div class="flex justify-center gap-2 mt-4">
            @if ($this->canLoadLess && $this->hasMore)
                <x-button wire:click="loadLess" label="Tampilkan Lebih Sedikit"
                    icon="solar.minus-circle-bold-duotone" class="btn-secondary" />
                <x-button wire:click="loadMore" label="Tampilkan Lebih Banyak"
                    icon="solar.add-circle-bold-duotone" class="btn-accent" />
            @elseif ($this->canLoadLess && !$this->hasMore)
                <x-button wire:click="loadLess" label="Tampilkan Lebih Sedikit"
                    icon="solar.minus-circle-bold-duotone" class="btn-secondary btn-block" />
            @else
                <x-button wire:click="loadMore" label="Tampilkan Lebih Banyak"
                    icon="solar.add-circle-bold-duotone" class="btn-accent btn-block"
                    :disabled="!$this->hasMore" />
            @endif
        </div>
    </div>

    {{-- Modal Konfirmasi Batalkan Pesanan --}}
    <x-modal wire:model="showCancelModal" title="Batalkan Pesanan"
        subtitle="Apakah kamu yakin ingin membatalkan pesanan ini?" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Pesanan yang dibatalkan tidak dapat dikembalikan.</p>
        </div>
        <x-slot:actions>
            <x-button label="Tidak" wire:click="$set('showCancelModal', false)" />
            <x-button label="Ya, Batalkan" wire:click="cancelOrder" class="btn-error" />
        </x-slot:actions>
    </x-modal>
</section>
