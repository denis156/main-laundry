{{-- Pending Confirmation Transactions --}}
<div class="space-y-3">
    @forelse ($this->pendingConfirmationTransactions as $transaction)
        <div class="card bg-base-300 shadow">
            <div class="card-body p-4">
                {{-- Header: Invoice & Status --}}
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-bold text-primary text-lg">
                            {{ $transaction->invoice_number }}
                        </h3>
                        <p class="text-xs text-base-content/60">
                            {{ $transaction->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                        </p>
                    </div>
                    <span class="badge badge-secondary gap-1">
                        Pending
                    </span>
                </div>

                <div class="divider my-2"></div>

                {{-- Customer Info --}}
                <div class="flex items-center gap-3 mb-2">
                    <div class="avatar">
                        <div class="ring-accent ring-offset-base-100 w-10 h-10 rounded-full ring-2 ring-offset-2">
                            <img src="{{ $transaction->customer?->getFilamentAvatarUrl() }}"
                                 alt="{{ $transaction->customer?->name ?? 'Customer' }}" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold">{{ $transaction->customer?->name ?? 'Customer tidak ditemukan' }}</p>
                        <p class="text-xs text-base-content/60">{{ $transaction->customer?->phone ?? '-' }}</p>
                    </div>
                </div>

                {{-- Order Info --}}
                @if (
                    $transaction->location_id ||
                        $transaction->weight ||
                        $transaction->payment_timing_text ||
                        $transaction->customer?->address)
                    <div class="bg-base-200 rounded-lg p-3 space-y-2">
                        {{-- Berat --}}
                        @if ($transaction->weight)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Berat</span>
                                <span class="font-semibold">{{ $transaction->weight }} kg</span>
                            </div>
                        @endif

                        {{-- Location (Pos/Resort) --}}
                        @if ($transaction->location_id)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Lokasi</span>
                                <span class="font-semibold">{{ $transaction->location?->name ?? 'N/A' }}</span>
                            </div>
                        @endif

                        {{-- Metode Pembayaran --}}
                        @if ($transaction->payment_timing_text)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Metode Pembayaran</span>
                                <span class="font-semibold">
                                    {{ $transaction->payment_timing_text }}
                                </span>
                            </div>
                        @endif

                        {{-- Alamat --}}
                        @if ($transaction->customer?->address)
                            @if ($transaction->location_id || $transaction->weight || $transaction->payment_timing_text)
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
        <div class="card bg-base-300 shadow">
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
