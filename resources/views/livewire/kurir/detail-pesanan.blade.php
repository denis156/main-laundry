@php use App\Helper\StatusTransactionHelper; @endphp
<section class="bg-base-100" wire:poll.25s.visible>
    {{-- Header --}}
    <x-header icon="solar.bill-list-bold-duotone" icon-classes="text-primary w-6 h-6" title="Detail Pesanan"
        subtitle="Pelanggan {{ $transaction->customer?->name ?? 'Customer tidak ditemukan' }}" separator
        progress-indicator>
        <x-slot:actions>
            <x-button icon="solar.undo-left-linear" link="{{ route('kurir.pesanan') }}" class="btn-circle btn-secondary" />
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Single Card with All Data --}}
        <div class="card bg-base-300 shadow">
            <div class="card-body p-4">
                {{-- Invoice & Status Badge --}}
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-bold text-lg text-primary">{{ $transaction->invoice_number }}</p>
                        <p class="text-xs text-base-content/60">
                            {{ $transaction->formatted_order_date }}
                        </p>
                    </div>
                    @php
                        $statusColor = StatusTransactionHelper::getStatusBadgeColor($transaction->workflow_status);
                        $statusText = StatusTransactionHelper::getStatusText($transaction->workflow_status);
                    @endphp
                    <span class="badge {{ $statusColor }} gap-1">
                        {{ $statusText }}
                    </span>
                </div>

                <div class="divider my-2"></div>

                {{-- Customer Info --}}
                <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                    <x-icon name="solar.user-bold-duotone" class="w-4 h-4 text-primary" />
                    Informasi Pelanggan
                </h3>

                @php
                    $customerName = $transaction->customer ? \App\Helper\Database\CustomerHelper::getName($transaction->customer) : 'N/A';
                    $defaultAddress = $transaction->customer ? \App\Helper\Database\CustomerHelper::getDefaultAddress($transaction->customer) : null;
                    $addressString = $defaultAddress ? \App\Helper\Database\CustomerHelper::getFullAddressString($defaultAddress) : null;
                @endphp

                <div class="bg-base-200 rounded-lg p-3 space-y-2 mb-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Nama</span>
                        <span class="font-semibold">{{ $customerName }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">No. Telepon</span>
                        <span class="font-semibold">{{ $transaction->customer?->phone ?? '-' }}</span>
                    </div>

                    @if ($addressString && $addressString !== 'Belum ada alamat')
                        <div class="divider my-1"></div>
                        <div>
                            <p class="text-xs text-base-content/70 mb-1">Alamat</p>
                            <p class="text-sm font-semibold text-primary leading-relaxed">
                                {{ $addressString }}</p>
                        </div>
                    @endif
                </div>

                <div class="divider my-2"></div>

                {{-- Transaction Info --}}
                <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                    <x-icon name="solar.bill-list-bold-duotone" class="w-4 h-4 text-primary" />
                    Informasi Pesanan
                </h3>

                @php
                    $items = \App\Helper\Database\TransactionHelper::getItems($transaction);
                    $totalPrice = \App\Helper\Database\TransactionHelper::getTotalPrice($transaction);
                @endphp

                <div class="bg-base-200 rounded-lg p-3 space-y-2 mb-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Tanggal Order</span>
                        <span class="font-semibold">{{ $transaction->created_at?->format('d M Y') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Jam Order</span>
                        <span class="font-semibold">{{ $transaction->created_at?->format('H:i') ?? '-' }}</span>
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

                    {{-- Metode Pembayaran --}}
                    <div class="divider my-1"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Metode Pembayaran</span>
                        <span class="font-semibold">
                            {{ $transaction->payment_timing_text ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                {{-- Notes --}}
                @php
                    $notes = \App\Helper\Database\TransactionHelper::getNotes($transaction);
                @endphp
                @if ($notes)
                    <div class="divider my-2"></div>

                    <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                        <x-icon name="solar.document-text-bold-duotone" class="w-4 h-4 text-primary" />
                        Catatan
                    </h3>

                    <div class="bg-base-200 rounded-lg p-3">
                        <p class="text-sm">{{ $notes }}</p>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="mt-3 space-y-2">
                    @if ($transaction->workflow_status === 'pending_confirmation')
                        {{-- Status: Pending Confirmation - Tampilkan Batalkan + Ambil Pesanan --}}
                        <div class="grid grid-cols-2 gap-2">
                            <x-button wire:click="openCancelModal" label="Batalkan Pesanan"
                                icon="solar.close-circle-bold-duotone" class="btn-error btn-sm" />

                            <x-button wire:click="openConfirmModal" label="Ambil Pesanan"
                                icon="solar.check-circle-bold-duotone" class="btn-accent btn-sm" />
                        </div>
                    @elseif ($transaction->workflow_status === 'confirmed')
                        {{-- Status: Confirmed - Tampilkan Input Per Layanan + WhatsApp + Sudah Dijemput --}}
                        <div class="space-y-3 mb-3">
                            @foreach ($items as $index => $item)
                                @php
                                    $serviceId = $item['service_id'] ?? $index;
                                    $serviceName = $item['service_name'] ?? 'N/A';
                                    $pricingUnit = $item['pricing_unit'] ?? 'per_kg';
                                    $pricePerKg = $item['price_per_kg'] ?? null;
                                    $pricePerItem = $item['price_per_item'] ?? null;
                                @endphp

                                <div class="bg-base-200 rounded-lg p-3">
                                    <h4 class="font-bold text-sm mb-2 text-primary">{{ $serviceName }}</h4>

                                    @if ($pricingUnit === 'per_kg')
                                        {{-- Detail Pakaian (hanya untuk per_kg) - DI ATAS --}}
                                        <div class="mb-3">
                                            <label class="label">
                                                <span class="label-text font-semibold">Detail Pakaian</span>
                                            </label>

                                            @php
                                                $clothingItems = $itemInputs[$serviceId]['clothing_items'] ?? [];
                                            @endphp

                                            <div class="space-y-2 mb-2">
                                                @foreach ($clothingItems as $clothingIndex => $clothingItem)
                                                    <x-select
                                                        wire:model.live="itemInputs.{{ $serviceId }}.clothing_items.{{ $clothingIndex }}.clothing_type_id"
                                                        :options="$this->getAvailableClothingTypes($serviceId, $clothingIndex)" placeholder="Pilih pakaian...">
                                                        <x-slot:prepend>
                                                            <x-button icon="o-trash"
                                                                wire:click="removeClothingItem({{ $serviceId }}, {{ $clothingIndex }})"
                                                                class="btn-error join-item" />
                                                        </x-slot:prepend>
                                                        <x-slot:append>
                                                            <x-input
                                                                wire:model.live="itemInputs.{{ $serviceId }}.clothing_items.{{ $clothingIndex }}.quantity"
                                                                type="number" min="1" placeholder="Qty"
                                                                class="join-item" />
                                                        </x-slot:append>
                                                    </x-select>
                                                @endforeach
                                            </div>

                                            <x-button label="Tambah Pakaian" icon="o-plus"
                                                wire:click="addClothingItem({{ $serviceId }})"
                                                class="btn-primary btn-sm btn-block" />
                                        </div>

                                        {{-- Input Berat untuk layanan per_kg - DI BAWAH --}}
                                        <x-input wire:model.live="itemInputs.{{ $serviceId }}.total_weight" type="number"
                                            step="0.01" min="0.01" label="Berat (kg)"
                                            placeholder="Contoh: 8.5" icon="solar.scale-bold-duotone"
                                            hint="Harga: Rp {{ number_format($pricePerKg ?? 0, 0, ',', '.') }}/kg" />
                                    @else
                                        {{-- Input Quantity untuk layanan per_item (tidak perlu detail pakaian manual) --}}
                                        <x-input wire:model.live="itemInputs.{{ $serviceId }}.quantity" type="number"
                                            min="1" label="Jumlah Item" placeholder="Contoh: 3"
                                            icon="solar.box-bold-duotone"
                                            hint="Harga: Rp {{ number_format($pricePerItem ?? 0, 0, ',', '.') }}/item" />
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            @if ($transaction->customer?->phone && $customerName !== 'N/A')
                                <x-button label="WhatsApp" icon="solar.chat-round-bold-duotone"
                                    link="{{ $this->getWhatsAppUrl() }}" external class="btn-success btn-sm" />
                            @endif

                            <x-button wire:click="openPickedUpModal" label="Sudah Dijemput"
                                icon="solar.box-bold-duotone"
                                class="btn-warning btn-sm {{ $transaction->customer?->phone && $customerName !== 'N/A' ? '' : 'col-span-2' }}"
                                :disabled="$this->isPickedUpButtonDisabled()" />
                        </div>
                    @elseif ($transaction->workflow_status === 'picked_up')
                        {{-- Status: Picked Up - Tampilkan Sudah di Pos --}}
                        <x-button wire:click="openAtLoadingPostModal" label="Sudah di Pos"
                            icon="solar.map-point-bold-duotone" class="btn-warning btn-sm btn-block" />
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
                        {{-- Status: Washing Completed - Tampilkan WhatsApp + Dalam Pengiriman --}}
                        <div class="grid grid-cols-2 gap-2">
                            @if ($transaction->customer?->phone && $customerName !== 'N/A')
                                <x-button label="WhatsApp" icon="solar.chat-round-bold-duotone"
                                    link="{{ $this->getWhatsAppUrlForDelivery() }}" external
                                    class="btn-success btn-sm" />
                            @endif

                            <x-button wire:click="openOutForDeliveryModal" label="Dalam Pengiriman"
                                icon="solar.delivery-bold-duotone"
                                class="btn-accent btn-sm {{ $transaction->customer?->phone && $customerName !== 'N/A' ? '' : 'col-span-2' }}" />
                        </div>
                    @elseif ($transaction->workflow_status === 'out_for_delivery')
                        {{-- Status: Out for Delivery - Tampilkan Terkirim --}}
                        <x-button wire:click="openDeliveredModal" label="Terkirim"
                            icon="solar.check-circle-bold-duotone" class="btn-success btn-sm btn-block"
                            :disabled="$transaction->payment_status !== 'paid'" />
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Batalkan Pesanan --}}
    <x-modal wire:model="showCancelModal" @close="$wire.showCancelModal = false" title="Batalkan Pesanan"
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
    <x-modal wire:model="showConfirmModal" @close="$wire.showConfirmModal = false" title="Ambil Pesanan" subtitle="Konfirmasi untuk mengambil pesanan ini?"
        class="modal-bottom sm:modal-middle" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Anda akan bertanggung jawab untuk menjemput dan mengantar pesanan ini.</p>
        </div>
        <x-slot:actions>
            <x-button label="Batal" @click="$wire.showConfirmModal = false" />
            <x-button label="Ya, Ambil Pesanan" wire:click="confirmOrder" class="btn-accent" spinner="confirmOrder" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Konfirmasi Dijemput --}}
    <x-modal wire:model="showPickedUpModal" @close="$wire.showPickedUpModal = false" title="Tandai Dijemput" subtitle="Konfirmasi pesanan sudah dijemput?"
        class="modal-bottom sm:modal-middle" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Pastikan berat cucian sudah diisi dengan benar sebelum melanjutkan.</p>
        </div>
        <x-slot:actions>
            <x-button label="Batal" @click="$wire.showPickedUpModal = false" />
            <x-button label="Ya, Sudah Dijemput" wire:click="markAsPickedUp" class="btn-warning" spinner="markAsPickedUp" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Konfirmasi Sudah di Pos --}}
    <x-modal wire:model="showAtLoadingPostModal" @close="$wire.showAtLoadingPostModal = false" title="Tandai Sudah di Pos"
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
    <x-modal wire:model="showOutForDeliveryModal" @close="$wire.showOutForDeliveryModal = false" title="Tandai Mengantar"
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
    <x-modal wire:model="showDeliveredModal" @close="$wire.showDeliveredModal = false" title="Tandai Terkirim" subtitle="Konfirmasi pesanan sudah terkirim?"
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
