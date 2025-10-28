{{-- Pending Confirmation Transactions --}}
<div class="space-y-3">
    @forelse ($this->pendingConfirmationTransactions as $transaction)
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
                    <span class="badge badge-secondary gap-1">
                        Pending
                    </span>
                </div>

                <div class="divider my-2"></div>

                {{-- Customer Info --}}
                <div class="flex items-center gap-3 mb-2">
                    <div class="avatar avatar-placeholder">
                        <div
                            class="bg-primary text-primary-content w-10 h-10 flex items-center justify-center rounded-full">
                            <span
                                class="text-sm font-semibold">{{ $transaction->customer?->getInitials() ?? 'NA' }}</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold">{{ $transaction->customer?->name ?? 'Customer tidak ditemukan' }}</p>
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

                {{-- Action Button --}}
                <x-button label="Detail Pesanan" link="{{ route('kurir.pesanan.detail', $transaction->id) }}"
                    class="btn-primary btn-sm btn-block mt-3" icon="solar.bill-list-bold-duotone" />
            </div>
        </div>
    @empty
        {{-- Empty State --}}
        <div class="card bg-base-300 shadow-lg">
            <div class="card-body items-center text-center py-8">
                <x-icon name="solar.inbox-bold-duotone" class="w-12 h-12 text-base-content/20 mb-3" />
                <h3 class="font-bold text-md">Tidak Ada Pesanan Baru</h3>
                <p class="text-xs text-base-content/60">
                    Belum ada pesanan yang menunggu konfirmasi.
                </p>
            </div>
        </div>
    @endforelse
</div>
