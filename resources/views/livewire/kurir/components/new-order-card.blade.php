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
                            {{ $transaction->order_date->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <span class="badge badge-secondary gap-1">
                        Konfirmasi?
                    </span>
                </div>

                <div class="divider my-2"></div>

                {{-- Customer Info --}}
                <div class="flex items-center gap-3 mb-2">
                    <div class="avatar avatar-placeholder">
                        <div class="bg-primary text-primary-content w-10 rounded-full">
                            <span class="text-sm">{{ substr($transaction->customer?->name ?? 'N/A', 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold">{{ $transaction->customer?->name ?? 'Customer tidak ditemukan' }}</p>
                        <p class="text-xs text-base-content/60">{{ $transaction->customer?->phone ?? '-' }}</p>
                    </div>
                </div>

                {{-- Service & Info --}}
                @if ($transaction->service_id || $transaction->customer?->address)
                    <div class="mt-2 bg-base-200 rounded-lg p-3 space-y-2">
                        @if ($transaction->service_id)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Layanan</span>
                                <span class="font-semibold">{{ $transaction->service?->name ?? 'N/A' }}</span>
                            </div>
                        @endif

                        @if ($transaction->customer?->address)
                            @if ($transaction->service_id)
                                <div class="divider my-1"></div>
                            @endif
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Alamat</span>
                                <span
                                    class="font-semibold text-right text-primary text-sm">{{ $transaction->customer->address }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Notes --}}
                @if ($transaction->notes)
                    <div class="mt-2 p-3 bg-base-200 rounded-lg">
                        <p class="text-xs text-base-content/70 mb-1">Catatan:</p>
                        <p class="text-sm">{{ $transaction->notes }}</p>
                    </div>
                @endif

                {{-- Payment & Timing Info --}}
                <div class="mt-3 flex gap-2 flex-wrap justify-center">
                    {{-- Payment Status --}}
                    @if ($transaction->payment_status === 'paid')
                        <div class="badge badge-success gap-1">
                            <x-icon name="solar.check-circle-bold-duotone" class="w-3 h-3" />
                            Lunas
                        </div>
                    @else
                        <div class="badge badge-error gap-1">
                            <x-icon name="solar.close-circle-bold-duotone" class="w-3 h-3" />
                            Belum Bayar
                        </div>
                    @endif

                    {{-- Payment Timing --}}
                    @if ($transaction->payment_timing === 'on_pickup')
                        <div class="badge badge-info gap-1">
                            <x-icon name="solar.upload-bold-duotone" class="w-3 h-3" />
                            Bayar Saat Jemput
                        </div>
                    @else
                        <div class="badge badge-warning gap-1">
                            <x-icon name="solar.download-bold-duotone" class="w-3 h-3" />
                            Bayar Saat Antar
                        </div>
                    @endif
                </div>

                {{-- Action Button --}}
                <div class="mt-3">
                    <a href="{{ route('kurir.pesanan.detail', $transaction->id) }}"
                        class="btn btn-primary btn-sm w-full">
                        <x-icon name="solar.eye-linear" class="w-4 h-4" />
                        Detail Pesanan
                    </a>
                </div>
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
