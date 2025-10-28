@php use App\Helper\StatusTransactionHelper; @endphp
<section class="bg-base-100" wire:poll.25s.visible>
    {{-- Header --}}
    <x-header icon="solar.bill-list-bold-duotone" icon-classes="text-primary w-6 h-6" title="Detail Pesanan"
        subtitle="Pelanggan atas nama {{ $transaction->customer?->name ?? 'Customer tidak ditemukan' }}" separator progress-indicator>
        <x-slot:actions>
            <x-button icon="solar.undo-left-linear" link="{{ route('kurir.pesanan') }}" class="btn-circle btn-secondary" />
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Single Card with All Data --}}
        <div class="card bg-base-300 shadow-lg">
            <div class="card-body p-4">
                {{-- Invoice & Status Badge --}}
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-bold text-lg text-primary">{{ $transaction->invoice_number }}</p>
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

                <div class="bg-base-200 rounded-lg p-3 space-y-2 mb-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Nama</span>
                        <span class="font-semibold">{{ $transaction->customer?->name ?? 'N/A' }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">No. Telepon</span>
                        <span class="font-semibold">{{ $transaction->customer?->phone ?? '-' }}</span>
                    </div>

                    @if ($transaction->customer?->address)
                        <div class="divider my-1"></div>
                        <div>
                            <p class="text-xs text-base-content/70 mb-1">Alamat</p>
                            <p class="text-sm font-semibold text-primary leading-relaxed">{{ $transaction->customer->address }}</p>
                        </div>
                    @endif
                </div>

                <div class="divider my-2"></div>

                {{-- Transaction Info --}}
                <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                    <x-icon name="solar.bill-list-bold-duotone" class="w-4 h-4 text-primary" />
                    Informasi Pesanan
                </h3>

                <div class="bg-base-200 rounded-lg p-3 space-y-2 mb-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Tanggal Order</span>
                        <span class="font-semibold">{{ $transaction->order_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Jam Order</span>
                        <span class="font-semibold">{{ $transaction->order_date->format('H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Layanan</span>
                        <span class="font-semibold">{{ $transaction->service?->name ?? 'N/A' }}</span>
                    </div>
                    @if ($transaction->pos_id)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Pos</span>
                            <span class="font-semibold">{{ $transaction->pos?->name ?? 'N/A' }}</span>
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
                    @if ($transaction->total_price)
                        <div class="divider my-1"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Total</span>
                            <span class="font-semibold text-right text-primary text-base">
                                {{ $transaction->formatted_total_price }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Notes --}}
                @if ($transaction->notes)
                    <div class="divider my-2"></div>

                    <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                        <x-icon name="solar.document-text-bold-duotone" class="w-4 h-4 text-primary" />
                        Catatan
                    </h3>

                    <div class="bg-base-200 rounded-lg p-3">
                        <p class="text-sm">{{ $transaction->notes }}</p>
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
                        {{-- Status: Confirmed - Tampilkan Input Berat + WhatsApp + Sudah Dijemput --}}
                        <div class="bg-base-200 rounded-lg p-3 mb-2">
                            {{-- Input Berat --}}
                            <x-input wire:model.blur="weight" type="number" step="0.01" min="0.01"
                                label="Berat Cucian (kg)" placeholder="Contoh: 8.92" icon="solar.scale-bold-duotone"
                                hint="{{ $this->getTotalPriceHint() }}" />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            @if ($transaction->customer?->phone && $transaction->customer?->name)
                                <x-button label="WhatsApp" icon="solar.chat-round-bold-duotone"
                                    link="{{ $this->getWhatsAppUrl() }}" external class="btn-success btn-sm" />
                            @endif

                            <x-button wire:click="openPickedUpModal" label="Sudah Dijemput"
                                icon="solar.box-bold-duotone"
                                class="btn-warning btn-sm {{ $transaction->customer?->phone && $transaction->customer?->name ? '' : 'col-span-2' }}"
                                :disabled="empty($weight) || $weight <= 0" />
                        </div>
                    @elseif ($transaction->workflow_status === 'picked_up')
                        {{-- Status: Picked Up - Tampilkan Sudah di Pos --}}
                        <x-button wire:click="openAtLoadingPostModal" label="Sudah di Pos"
                            icon="solar.map-point-bold-duotone" class="btn-warning btn-sm btn-block" />
                    @elseif (in_array($transaction->workflow_status, ['at_loading_post', 'in_washing']))
                        {{-- Status: At Loading Post / In Washing - Tampilkan WhatsApp + Mengantar (disabled) --}}
                        <div class="grid grid-cols-2 gap-2">
                            @if ($transaction->customer?->phone && $transaction->customer?->name)
                                <x-button
                                    label="WhatsApp"
                                    icon="solar.chat-round-bold-duotone"
                                    class="btn-success btn-sm"
                                    disabled />
                            @endif

                            <x-button
                                label="Mengantar"
                                icon="solar.delivery-bold-duotone"
                                class="btn-accent btn-sm {{ $transaction->customer?->phone && $transaction->customer?->name ? '' : 'col-span-2' }}"
                                disabled />
                        </div>
                    @elseif ($transaction->workflow_status === 'washing_completed')
                        {{-- Status: Washing Completed - Tampilkan WhatsApp + Dalam Pengiriman --}}
                        <div class="grid grid-cols-2 gap-2">
                            @if ($transaction->customer?->phone && $transaction->customer?->name)
                                <x-button label="WhatsApp" icon="solar.chat-round-bold-duotone"
                                    link="{{ $this->getWhatsAppUrlForDelivery() }}" external class="btn-success btn-sm" />
                            @endif

                            <x-button wire:click="openOutForDeliveryModal" label="Dalam Pengiriman"
                                icon="solar.delivery-bold-duotone"
                                class="btn-accent btn-sm {{ $transaction->customer?->phone && $transaction->customer?->name ? '' : 'col-span-2' }}" />
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
    <x-modal wire:model="showCancelModal" title="Batalkan Pesanan" subtitle="Apakah Anda yakin ingin membatalkan pesanan ini?" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Pesanan yang dibatalkan tidak dapat dikembalikan.</p>
        </div>
        <x-slot:actions>
            <x-button label="Batal" wire:click="$set('showCancelModal', false)" />
            <x-button label="Ya, Batalkan" wire:click="cancelOrder" class="btn-error" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Konfirmasi Ambil Pesanan --}}
    <x-modal wire:model="showConfirmModal" title="Ambil Pesanan" subtitle="Konfirmasi untuk mengambil pesanan ini?" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Anda akan bertanggung jawab untuk menjemput dan mengantar pesanan ini.</p>
        </div>
        <x-slot:actions>
            <x-button label="Batal" wire:click="$set('showConfirmModal', false)" />
            <x-button label="Ya, Ambil Pesanan" wire:click="confirmOrder" class="btn-accent" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Konfirmasi Dijemput --}}
    <x-modal wire:model="showPickedUpModal" title="Tandai Dijemput" subtitle="Konfirmasi pesanan sudah dijemput?" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Pastikan berat cucian sudah diisi dengan benar sebelum melanjutkan.</p>
        </div>
        <x-slot:actions>
            <x-button label="Batal" wire:click="$set('showPickedUpModal', false)" />
            <x-button label="Ya, Sudah Dijemput" wire:click="markAsPickedUp" class="btn-warning" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Konfirmasi Sudah di Pos --}}
    <x-modal wire:model="showAtLoadingPostModal" title="Tandai Sudah di Pos" subtitle="Konfirmasi pesanan sudah tiba di pos?" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Pesanan akan ditandai sudah berada di pos loading.</p>
        </div>
        <x-slot:actions>
            <x-button label="Batal" wire:click="$set('showAtLoadingPostModal', false)" />
            <x-button label="Ya, Sudah di Pos" wire:click="markAsAtLoadingPost" class="btn-warning" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Konfirmasi Mengantar --}}
    <x-modal wire:model="showOutForDeliveryModal" title="Tandai Mengantar" subtitle="Konfirmasi akan mengantar pesanan ini?" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Pesanan akan ditandai sedang dalam pengiriman.</p>
        </div>
        <x-slot:actions>
            <x-button label="Batal" wire:click="$set('showOutForDeliveryModal', false)" />
            <x-button label="Ya, Mengantar" wire:click="markAsOutForDelivery" class="btn-accent" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Konfirmasi Terkirim --}}
    <x-modal wire:model="showDeliveredModal" title="Tandai Terkirim" subtitle="Konfirmasi pesanan sudah terkirim?" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Pastikan pesanan sudah diterima oleh customer dan pembayaran sudah dilakukan jika ada.</p>
        </div>
        <x-slot:actions>
            <x-button label="Batal" wire:click="$set('showDeliveredModal', false)" />
            <x-button label="Ya, Sudah Terkirim" wire:click="markAsDelivered" class="btn-success" />
        </x-slot:actions>
    </x-modal>
</section>
