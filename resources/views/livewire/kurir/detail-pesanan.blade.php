@php use App\Helper\StatusTransactionHelper; @endphp
<section class="bg-base-100" wire:poll.25s.visible>
    {{-- Header --}}
    <x-header icon="solar.bill-list-bold-duotone" icon-classes="text-primary w-6 h-6" title="Detail Pesanan"
        subtitle="{{ $transaction->invoice_number }}" separator progress-indicator>
        <x-slot:actions>
            <x-button icon="solar.undo-left-linear" link="{{ route('kurir.pesanan') }}" class="btn-circle btn-secondary" />
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Single Card with All Data --}}
        <div class="card bg-base-300 shadow-lg">
            <div class="card-body p-4">
                {{-- Invoice & Status Badge --}}
                <div class="flex items-start justify-between mb-3">
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
                            <button wire:click="cancelOrder" class="btn btn-error btn-sm">
                                <x-icon name="solar.close-circle-bold-duotone" class="w-4 h-4" />
                                Batalkan Pesanan
                            </button>

                            <button wire:click="confirmOrder" class="btn btn-accent btn-sm">
                                <x-icon name="solar.check-circle-bold-duotone" class="w-4 h-4" />
                                Ambil Pesanan
                            </button>
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
                                <a href="{{ $this->getWhatsAppUrl() }}" target="_blank"
                                    class="btn btn-success btn-sm">
                                    <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                                    WhatsApp
                                </a>
                            @endif

                            <button wire:click="markAsPickedUp"
                                class="btn btn-warning btn-sm {{ $transaction->customer?->phone && $transaction->customer?->name ? '' : 'col-span-2' }}"
                                @if (empty($weight) || $weight <= 0) disabled @endif>
                                <x-icon name="solar.box-bold-duotone" class="w-4 h-4" />
                                Sudah Dijemput
                            </button>
                        </div>
                    @elseif ($transaction->workflow_status === 'picked_up')
                        {{-- Status: Picked Up - Tampilkan Sudah di Pos + Kembali --}}
                        <div class="grid grid-cols-2 gap-2">
                            <button wire:click="markAsAtLoadingPost" class="btn btn-warning btn-sm">
                                <x-icon name="solar.map-point-bold-duotone" class="w-4 h-4" />
                                Sudah di Pos
                            </button>

                            <a href="{{ route('kurir.pesanan') }}" class="btn btn-primary btn-sm">
                                <x-icon name="solar.undo-left-linear" class="w-4 h-4" />
                                Kembali
                            </a>
                        </div>
                    @elseif ($transaction->workflow_status === 'washing_completed')
                        {{-- Status: Washing Completed - Tampilkan WhatsApp + Dalam Pengiriman --}}
                        <div class="grid grid-cols-2 gap-2">
                            @if ($transaction->customer?->phone && $transaction->customer?->name)
                                <a href="{{ $this->getWhatsAppUrlForDelivery() }}" target="_blank"
                                    class="btn btn-success btn-sm">
                                    <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                                    WhatsApp
                                </a>
                            @endif

                            <button wire:click="markAsOutForDelivery"
                                class="btn btn-accent btn-sm {{ $transaction->customer?->phone && $transaction->customer?->name ? '' : 'col-span-2' }}">
                                <x-icon name="solar.delivery-bold-duotone" class="w-4 h-4" />
                                Dalam Pengiriman
                            </button>
                        </div>
                    @elseif ($transaction->workflow_status === 'out_for_delivery')
                        {{-- Status: Out for Delivery - Tampilkan Terkirim --}}
                        <button wire:click="markAsDelivered" class="btn btn-success btn-sm w-full"
                            @if ($transaction->payment_status !== 'paid') disabled @endif>
                            <x-icon name="solar.check-circle-bold-duotone" class="w-4 h-4" />
                            Terkirim
                        </button>
                    @else
                        {{-- Status lain (at_loading_post, in_washing, delivered, cancelled) - Hanya tampilkan Kembali --}}
                        <a href="{{ route('kurir.pesanan') }}" class="btn btn-primary btn-sm w-full">
                            <x-icon name="solar.undo-left-linear" class="w-4 h-4" />
                            Kembali
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
