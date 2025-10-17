<div class="bg-base-100 min-h-dvh w-full">
    {{-- Header --}}
    <x-header title="Pembayaran" subtitle="Kelola Pembayaran Transaksi" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input wire:model.live.debounce.500ms="search" icon="solar.magnifer-bold-duotone"
                placeholder="Cari invoice atau customer..." clearable />
        </x-slot:middle>
        <x-slot:actions>
            <x-dropdown no-x-anchor right>
                <x-slot:trigger>
                    <x-button icon="solar.filter-bold-duotone" class="btn-circle btn-primary" />
                </x-slot:trigger>
                <x-menu-item title="Belum Bayar" icon="solar.close-circle-bold-duotone"
                    wire:click="$set('filter', 'unpaid')" />
                <x-menu-item title="Sudah Bayar" icon="solar.check-circle-bold-duotone"
                    wire:click="$set('filter', 'paid')" />
                <x-menu-item title="Semua" icon="solar.widget-bold-duotone"
                    wire:click="$set('filter', 'all')" />
            </x-dropdown>
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 gap-4">
            {{-- Belum Dibayar --}}
            <div class="card bg-error text-error-content shadow-lg">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs opacity-80">Belum Bayar</p>
                            <p class="text-2xl font-bold">{{ $this->stats['unpaid_count'] }}</p>
                        </div>
                        <x-icon name="solar.close-circle-bold-duotone" class="w-10 h-10 opacity-50" />
                    </div>
                    <div class="text-xs mt-2 font-semibold">
                        Rp {{ number_format($this->stats['unpaid_total'], 0, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- Sudah Dibayar --}}
            <div class="card bg-success text-success-content shadow-lg">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs opacity-80">Sudah Bayar</p>
                            <p class="text-2xl font-bold">{{ $this->stats['paid_count'] }}</p>
                        </div>
                        <x-icon name="solar.check-circle-bold-duotone" class="w-10 h-10 opacity-50" />
                    </div>
                    <div class="text-xs mt-2 font-semibold">
                        Transaksi Lunas
                    </div>
                </div>
            </div>
        </div>

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
                                    {{ $transaction->order_date->format('d M Y, H:i') }}
                                </p>
                            </div>
                            @if ($transaction->payment_status === 'paid')
                                <span class="badge badge-success gap-1">
                                    <x-icon name="solar.check-circle-bold-duotone" class="w-3 h-3" />
                                    Lunas
                                </span>
                            @else
                                <span class="badge badge-error gap-1">
                                    <x-icon name="solar.close-circle-bold-duotone" class="w-3 h-3" />
                                    Belum Bayar
                                </span>
                            @endif
                        </div>

                        <div class="divider my-2"></div>

                        {{-- Customer Info --}}
                        <div class="flex items-center gap-3 mb-2">
                            <div class="avatar avatar-placeholder">
                                <div class="bg-primary text-primary-content w-10 rounded-full">
                                    <span
                                        class="text-sm">{{ substr($transaction->customer?->name ?? 'N/A', 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold">
                                    {{ $transaction->customer?->name ?? 'Customer tidak ditemukan' }}</p>
                                <p class="text-xs text-base-content/60">{{ $transaction->customer?->phone ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Service & Payment Info --}}
                        <div class="bg-base-200 rounded-lg p-3 space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Layanan</span>
                                <span class="font-semibold">{{ $transaction->service?->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Berat</span>
                                <span class="font-semibold">{{ $transaction->weight }} kg</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Harga/kg</span>
                                <span class="font-semibold">Rp
                                    {{ number_format($transaction->price_per_kg, 0, ',', '.') }}</span>
                            </div>
                            <div class="divider my-1"></div>
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-base">Total</span>
                                <span class="font-bold text-primary text-lg">
                                    Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Payment Timing Badge --}}
                        <div class="mt-3">
                            @if ($transaction->payment_timing === 'on_pickup')
                                <div class="badge badge-info gap-1">
                                    <x-icon name="solar.upload-bold-duotone" class="w-3 h-3" />
                                    Bayar Saat Pickup
                                </div>
                            @else
                                <div class="badge badge-warning gap-1">
                                    <x-icon name="solar.download-bold-duotone" class="w-3 h-3" />
                                    Bayar Saat Delivery
                                </div>
                            @endif

                            {{-- Workflow Status --}}
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
                            <div class="badge {{ $statusColor }} gap-1 ml-2">
                                {{ $statusText }}
                            </div>
                        </div>

                        {{-- Payment Proof --}}
                        @if ($transaction->payment_status === 'paid' && $transaction->payment_proof_url)
                            <div class="mt-3 p-3 bg-success/10 rounded-lg border border-success/20">
                                <div class="flex items-center gap-2 text-success">
                                    <x-icon name="solar.shield-check-bold-duotone" class="w-5 h-5" />
                                    <span class="text-sm font-semibold">Pembayaran Terkonfirmasi</span>
                                </div>
                                @if ($transaction->paid_at)
                                    <p class="text-xs text-base-content/60 mt-1">
                                        {{ $transaction->paid_at->format('d M Y, H:i') }}
                                    </p>
                                @endif
                                <button class="btn btn-success btn-sm btn-outline mt-2 w-full">
                                    <x-icon name="solar.eye-bold-duotone" class="w-4 h-4" />
                                    Lihat Bukti Pembayaran
                                </button>
                            </div>
                        @else
                            {{-- Action Button --}}
                            <div class="card-actions mt-3">
                                <button class="btn btn-primary btn-block">
                                    <x-icon name="solar.camera-bold-duotone" class="w-5 h-5" />
                                    Upload Bukti Pembayaran
                                </button>
                            </div>
                        @endif

                        {{-- Notes --}}
                        @if ($transaction->notes)
                            <div class="mt-3 p-3 bg-base-200 rounded-lg">
                                <p class="text-xs text-base-content/70 mb-1">Catatan:</p>
                                <p class="text-sm">{{ $transaction->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="card bg-base-300 shadow-lg">
                    <div class="card-body items-center text-center py-12">
                        <x-icon name="solar.inbox-bold-duotone" class="w-16 h-16 text-base-content/20 mb-4" />
                        <h3 class="font-bold text-lg">Tidak Ada Transaksi</h3>
                        <p class="text-base-content/60">
                            @if ($filter === 'unpaid')
                                Belum ada transaksi yang perlu pembayaran.
                            @elseif ($filter === 'paid')
                                Belum ada transaksi yang sudah lunas.
                            @else
                                Belum ada transaksi pembayaran.
                            @endif
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
