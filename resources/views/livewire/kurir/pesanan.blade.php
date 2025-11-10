@php use App\Helper\StatusTransactionHelper; @endphp
<section class="bg-base-100 min-h-dvh w-full">
    {{-- Header --}}
    <x-header icon="solar.bill-list-bold-duotone" icon-classes="text-primary w-6 h-6" title="Pesanan"
        subtitle="Monitor & Proses Pesanan Pelanggan" separator progress-indicator>
        <x-slot:middle class="justify-end">
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
                <div class="card bg-base-300 shadow">
                    <div class="card-body p-4">
                        {{-- Header: Invoice & Eye Button --}}
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="font-bold text-primary text-lg">
                                    {{ $transaction->invoice_number }}
                                </h3>
                                <p class="text-xs text-base-content/60">
                                    {{ $transaction->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                                </p>
                            </div>
                            <x-button icon="solar.eye-bold" class="btn-circle btn-accent btn-md"
                                link="{{ route('kurir.pesanan.detail', $transaction->id) }}" />
                        </div>

                        <div class="divider my-2"></div>

                        {{-- Customer Info --}}
                        <div class="flex items-center gap-3 mb-2">
                            <div class="avatar">
                                <div
                                    class="ring-accent ring-offset-base-100 w-10 h-10 rounded-full ring-2 ring-offset-2">
                                    <img src="{{ $transaction->customer?->getFilamentAvatarUrl() }}"
                                        alt="{{ $transaction->customer?->name ?? 'Customer' }}" />
                                </div>
                            </div>
                            @php
                                $customerName = $transaction->customer ? \App\Helper\Database\CustomerHelper::getName($transaction->customer) : 'Customer tidak ditemukan';
                            @endphp
                            <div class="flex-1">
                                <p class="font-semibold">{{ $customerName }}</p>
                                <p class="text-xs text-base-content/60">{{ $transaction->customer?->phone ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Order Info --}}
                        @php
                            $items = \App\Helper\Database\TransactionHelper::getItems($transaction);
                            $totalWeight = 0;
                            foreach ($items as $item) {
                                $totalWeight += $item['weight'] ?? $item['total_weight'] ?? 0;
                            }
                            $defaultAddress = $transaction->customer ? \App\Helper\Database\CustomerHelper::getDefaultAddress($transaction->customer) : null;
                            $addressString = $defaultAddress ? \App\Helper\Database\CustomerHelper::getFullAddressString($defaultAddress) : null;
                            $notes = \App\Helper\Database\TransactionHelper::getNotes($transaction);
                        @endphp

                        <div class="bg-base-200 rounded-lg p-3 space-y-2">
                            {{-- Status --}}
                            @php
                                $statusColor = StatusTransactionHelper::getStatusBadgeColor(
                                    $transaction->workflow_status,
                                );
                                $statusText = StatusTransactionHelper::getStatusText($transaction->workflow_status);
                            @endphp
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Status</span>
                                <span class="badge {{ $statusColor }} gap-1">{{ $statusText }}</span>
                            </div>

                            {{-- Metode Pembayaran --}}
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Metode Pembayaran</span>
                                <span class="font-semibold">
                                    {{ $transaction->payment_timing_text ?? 'N/A' }}
                                </span>
                            </div>

                            {{-- List Items/Layanan --}}
                            @if (count($items) > 0)
                                <div class="divider my-1"></div>
                                <div>
                                    <p class="text-xs font-semibold text-base-content/70 mb-2">Layanan yang Dipesan:</p>
                                    <div class="space-y-2">
                                        @foreach ($items as $item)
                                            @php
                                                $serviceName = $item['service_name'] ?? null;
                                                $pricingUnit = $item['pricing_unit'] ?? null;

                                                if ((!$serviceName || !$pricingUnit) && !empty($item['service_id'])) {
                                                    $service = \App\Models\Service::find($item['service_id']);
                                                    if ($service) {
                                                        $serviceName = $serviceName ?: $service->name;
                                                        $pricingUnit = $pricingUnit ?: ($service->data['pricing']['unit'] ?? 'per_kg');
                                                    }
                                                }

                                                $serviceName = $serviceName ?: 'N/A';
                                                $pricingUnit = $pricingUnit ?: 'per_kg';
                                            @endphp
                                            <div class="bg-base-100 rounded p-2">
                                                <div class="flex justify-between items-start mb-1">
                                                    <span class="text-sm font-semibold text-primary">{{ $serviceName }}</span>
                                                    <span class="text-xs badge badge-outline">
                                                        @if ($pricingUnit === 'per_kg')
                                                            {{ $item['total_weight'] ?? 0 }} kg
                                                        @else
                                                            {{ $item['quantity'] ?? 0 }} item
                                                        @endif
                                                    </span>
                                                </div>
                                                @if (!empty($item['subtotal']))
                                                    <div class="flex justify-between items-center text-xs">
                                                        <span class="text-base-content/60">Subtotal</span>
                                                        <span class="font-semibold">Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Alamat --}}
                            @if ($addressString && $addressString !== 'Belum ada alamat')
                                <div class="divider my-1"></div>
                                <div>
                                    <p class="text-xs text-base-content/70 mb-1">Alamat</p>
                                    <p class="text-sm font-semibold text-primary leading-relaxed">
                                        {{ $addressString }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Notes --}}
                        @if ($notes)
                            <div class="mt-2 p-3 bg-base-200 rounded-lg">
                                <p class="text-xs text-base-content/70 mb-1">Catatan</p>
                                <p class="text-sm">{{ $notes }}</p>
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="mt-3 space-y-2">
                            @if ($transaction->workflow_status === 'pending_confirmation')
                                {{-- Status: Pending Confirmation - Tampilkan Batalkan + Ambil Pesanan --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <x-button wire:click="openCancelModal({{ $transaction->id }})"
                                        label="Batalkan Pesanan" icon="solar.close-circle-bold-duotone"
                                        class="btn-error btn-sm" />

                                    <x-button wire:click="openConfirmModal({{ $transaction->id }})"
                                        label="Ambil Pesanan" icon="solar.check-circle-bold-duotone"
                                        class="btn-accent btn-sm" />
                                </div>
                            @elseif ($transaction->workflow_status === 'confirmed')
                                {{-- Status: Confirmed - Tampilkan tombol Detail --}}
                                <x-button label="Detail Pesanan" icon="solar.eye-bold"
                                    link="{{ route('kurir.pesanan.detail', $transaction->id) }}"
                                    class="btn-accent btn-sm btn-block" />
                            @elseif ($transaction->workflow_status === 'picked_up')
                                {{-- Status: Picked Up - Tampilkan Sudah di Pos --}}
                                <x-button wire:click="openAtLoadingPostModal({{ $transaction->id }})"
                                    label="Sudah di Pos" icon="solar.map-point-bold-duotone"
                                    class="btn-warning btn-sm btn-block" />
                            @elseif (in_array($transaction->workflow_status, ['at_loading_post', 'in_washing']))
                                {{-- Status: At Loading Post / In Washing - Tampilkan WhatsApp + Mengantar (disabled) --}}
                                <div class="grid grid-cols-2 gap-2">
                                    @if ($transaction->customer?->phone && $transaction->customer?->name)
                                        <x-button label="WhatsApp" icon="solar.chat-round-bold-duotone"
                                            class="btn-success btn-sm" disabled />
                                    @endif

                                    <x-button label="Mengantar" icon="solar.delivery-bold-duotone"
                                        class="btn-accent btn-sm {{ $transaction->customer?->phone && $transaction->customer?->name ? '' : 'col-span-2' }}"
                                        disabled />
                                </div>
                            @elseif ($transaction->workflow_status === 'washing_completed')
                                {{-- Status: Washing Completed - Tampilkan WhatsApp + Mengantar --}}
                                <div class="grid grid-cols-2 gap-2">
                                    @if ($transaction->customer?->phone && $customerName !== 'Customer tidak ditemukan')
                                        <x-button label="WhatsApp" icon="solar.chat-round-bold-duotone"
                                            link="{{ $this->getWhatsAppUrlForDelivery($transaction->customer->phone, $customerName, $transaction) }}"
                                            external class="btn-success btn-sm" />
                                    @endif

                                    <x-button wire:click="openOutForDeliveryModal({{ $transaction->id }})"
                                        label="Mengantar" icon="solar.delivery-bold-duotone"
                                        class="btn-accent btn-sm {{ $transaction->customer?->phone && $transaction->customer?->name ? '' : 'col-span-2' }}" />
                                </div>
                            @elseif ($transaction->workflow_status === 'out_for_delivery')
                                {{-- Status: Out for Delivery - Tampilkan Terkirim --}}
                                <x-button wire:click="openDeliveredModal({{ $transaction->id }})" label="Terkirim"
                                    icon="solar.check-circle-bold-duotone" class="btn-success btn-sm btn-block"
                                    :disabled="$transaction->payment_status !== 'paid'" />
                            @elseif (in_array($transaction->workflow_status, ['delivered', 'cancelled']))
                                {{-- Status: Delivered / Cancelled - Tampilkan Detail Pesanan --}}
                                <x-button label="Detail Pesanan" icon="solar.eye-bold"
                                    link="{{ route('kurir.pesanan.detail', $transaction->id) }}"
                                    class="btn-accent btn-sm btn-block" />
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="card bg-base-300 shadow">
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

        {{-- Pagination Buttons --}}
        <div class="flex justify-center gap-2 mt-4">
            @if ($canLoadLess && $hasMore)
                {{-- Tampilkan kedua tombol jika bukan di halaman pertama dan masih ada data --}}
                <x-button wire:click="loadLess" label="Tampilkan Lebih Sedikit"
                    icon="solar.minus-circle-bold-duotone" class="btn-secondary" />
                <x-button wire:click="loadMore" label="Tampilkan Lebih Banyak" icon="solar.add-circle-bold-duotone"
                    class="btn-accent" />
            @elseif ($canLoadLess && !$hasMore)
                {{-- Hanya tombol "Lebih Sedikit" jika sudah di akhir --}}
                <x-button wire:click="loadLess" label="Tampilkan Lebih Sedikit"
                    icon="solar.minus-circle-bold-duotone" class="btn-secondary btn-block" />
            @else
                {{-- Tombol "Lebih Banyak" jika di halaman pertama (disabled jika data <= 5) --}}
                <x-button wire:click="loadMore" label="Tampilkan Lebih Banyak" icon="solar.add-circle-bold-duotone"
                    class="btn-accent btn-block" :disabled="!$hasMore" />
            @endif
        </div>
    </div>

    {{-- Modal Konfirmasi Batalkan Pesanan --}}
    <x-modal wire:model="showCancelModal" @close="$wire.set('selectedTransactionId', null)" title="Batalkan Pesanan"
        subtitle="Apakah Anda yakin ingin membatalkan pesanan ini?" class="modal-bottom sm:modal-middle" persistent
        separator>
        <div class="py-4">
            <p class="text-base-content/70">Pesanan yang dibatalkan tidak dapat dikembalikan.</p>
        </div>
        <x-slot:actions>
            <x-button label="Batal" @click="$wire.showCancelModal = false" />
            <x-button label="Ya, Batalkan" wire:click="cancelOrder" class="btn-error" spinner="cancelOrder" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Konfirmasi Ambil Pesanan --}}
    <x-modal wire:model="showConfirmModal" @close="$wire.set('selectedTransactionId', null)" title="Ambil Pesanan" subtitle="Konfirmasi untuk mengambil pesanan ini?"
        class="modal-bottom sm:modal-middle" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Anda akan bertanggung jawab untuk menjemput dan mengantar pesanan ini.</p>
        </div>
        <x-slot:actions>
            <x-button label="Batal" @click="$wire.showConfirmModal = false" />
            <x-button label="Ya, Ambil Pesanan" wire:click="confirmOrder" class="btn-accent" spinner="confirmOrder" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Konfirmasi Sudah di Pos --}}
    <x-modal wire:model="showAtLoadingPostModal" @close="$wire.set('selectedTransactionId', null)" title="Tandai Sudah di Pos"
        subtitle="Konfirmasi pesanan sudah tiba di pos?" class="modal-bottom sm:modal-middle" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Pesanan akan ditandai sudah berada di pos loading.</p>
        </div>
        <x-slot:actions>
            <x-button label="Batal" @click="$wire.showAtLoadingPostModal = false" />
            <x-button label="Ya, Sudah di Pos" wire:click="markAsAtLoadingPost" class="btn-warning" spinner="markAsAtLoadingPost" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Konfirmasi Mengantar --}}
    <x-modal wire:model="showOutForDeliveryModal" @close="$wire.set('selectedTransactionId', null)" title="Tandai Mengantar"
        subtitle="Konfirmasi akan mengantar pesanan ini?" class="modal-bottom sm:modal-middle" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Pesanan akan ditandai sedang dalam pengiriman.</p>
        </div>
        <x-slot:actions>
            <x-button label="Batal" @click="$wire.showOutForDeliveryModal = false" />
            <x-button label="Ya, Mengantar" wire:click="markAsOutForDelivery" class="btn-accent" spinner="markAsOutForDelivery" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Konfirmasi Terkirim --}}
    <x-modal wire:model="showDeliveredModal" @close="$wire.set('selectedTransactionId', null)" title="Tandai Terkirim" subtitle="Konfirmasi pesanan sudah terkirim?"
        class="modal-bottom sm:modal-middle" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Pastikan pesanan sudah diterima oleh customer dan pembayaran sudah
                dilakukan jika ada.</p>
        </div>
        <x-slot:actions>
            <x-button label="Batal" @click="$wire.showDeliveredModal = false" />
            <x-button label="Ya, Sudah Terkirim" wire:click="markAsDelivered" class="btn-success" spinner="markAsDelivered" />
        </x-slot:actions>
    </x-modal>
</section>
