<div class="bg-base-100 min-h-dvh w-full">
    {{-- Header --}}
    <x-header title="Detail Pesanan" subtitle="{{ $transaction->invoice_number }}" separator progress-indicator>
        <x-slot:actions>
            <a href="{{ route('kurir.pesanan') }}" class="btn btn-circle btn-ghost">
                <x-icon name="solar.undo-left-linear" class="w-6 h-6" />
            </a>
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Single Card with All Data --}}
        <div class="card bg-base-300 shadow-lg">
            <div class="card-body p-4">
                {{-- Invoice & Status Badge --}}
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs text-base-content/60">Invoice</p>
                        <p class="font-bold text-lg text-primary">{{ $transaction->invoice_number }}</p>
                    </div>
                    @php
                        $statusColor = match ($transaction->workflow_status) {
                            'pending_confirmation' => 'badge-secondary',
                            'confirmed' => 'badge-info',
                            'picked_up' => 'badge-warning',
                            'at_loading_post' => 'badge-warning',
                            'in_washing' => 'badge-primary',
                            'washing_completed' => 'badge-success',
                            'out_for_delivery' => 'badge-warning',
                            'delivered' => 'badge-success',
                            'cancelled' => 'badge-error',
                            default => 'badge-secondary',
                        };

                        $statusText = match ($transaction->workflow_status) {
                            'pending_confirmation' => 'Menunggu Konfirmasi',
                            'confirmed' => 'Terkonfirmasi',
                            'picked_up' => 'Sudah Dijemput',
                            'at_loading_post' => 'Di Pos',
                            'in_washing' => 'Sedang Dicuci',
                            'washing_completed' => 'Cucian Selesai',
                            'out_for_delivery' => 'Dalam Pengiriman',
                            'delivered' => 'Terkirim',
                            'cancelled' => 'Dibatalkan',
                            default => $transaction->workflow_status,
                        };
                    @endphp
                    <span class="badge {{ $statusColor }} gap-1">
                        {{ $statusText }}
                    </span>
                </div>

                <div class="divider my-2"></div>

                {{-- Customer Info --}}
                <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                    <x-icon name="solar.user-bold-duotone" class="w-4 h-4 text-primary" />
                    Informasi Customer
                </h3>

                <div class="bg-base-200 rounded-lg p-3 space-y-2 mb-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Nama</span>
                        <span class="font-semibold">{{ $transaction->customer?->name ?? 'N/A' }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Phone</span>
                        <span class="font-semibold">{{ $transaction->customer?->phone ?? '-' }}</span>
                    </div>

                    @if ($transaction->customer?->address)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Alamat</span>
                            <span class="font-semibold text-right text-sm">{{ $transaction->customer->address }}</span>
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
                        @if ($transaction->pos?->address)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Alamat Pos</span>
                                <span class="font-semibold text-right text-sm">{{ $transaction->pos->address }}</span>
                            </div>
                        @endif
                        @if ($transaction->pos?->pic_name)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Penanggung Jawab</span>
                                <span class="font-semibold">{{ $transaction->pos->pic_name }}</span>
                            </div>
                        @endif
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
                            <span class="font-semibold">Rp {{ number_format($transaction->price_per_kg, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($transaction->total_price)
                        <div class="divider my-1"></div>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-base">Total</span>
                            <span class="font-bold text-primary text-lg">
                                Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
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
            </div>
        </div>
    </div>
</div>
