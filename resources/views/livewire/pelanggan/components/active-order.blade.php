{{-- Active Transactions --}}
<div class="space-y-3">
    @forelse ($this->activeTransactions as $transaction)
        <div class="card bg-base-300 shadow">
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
                    <span class="badge {{ App\Helper\StatusTransactionCustomerHelper::getStatusBadgeColor($transaction->workflow_status) }}">
                        {{ App\Helper\StatusTransactionCustomerHelper::getStatusText($transaction->workflow_status) }}
                    </span>
                </div>

                <div class="divider my-2"></div>

                {{-- Courier Info (hanya tampil jika sudah di-assign & confirmed) --}}
                @if ($transaction->courierMotorcycle && $transaction->workflow_status !== 'pending_confirmation')
                    <div class="flex items-center gap-3 mb-2">
                        <div class="avatar">
                            <div class="ring-accent ring-offset-base-100 w-10 h-10 rounded-full ring-2 ring-offset-2">
                                <img src="{{ $transaction->courierMotorcycle->getFilamentAvatarUrl() }}"
                                     alt="{{ $transaction->courierMotorcycle->name ?? 'Kurir' }}" />
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold">{{ $transaction->courierMotorcycle->name ?? 'Kurir tidak ditemukan' }}</p>
                            <p class="text-xs text-base-content/60">{{ $transaction->courierMotorcycle->phone ?? '-' }}</p>
                        </div>
                    </div>
                @endif

                {{-- Order Info --}}
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

                    {{-- Harga per kg --}}
                    @if ($transaction->price_per_kg)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Harga/kg</span>
                            <span class="font-semibold">{{ $transaction->formatted_price_per_kg }}</span>
                        </div>
                    @endif

                    {{-- Metode Pembayaran --}}
                    @if ($transaction->payment_timing)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Metode Pembayaran</span>
                            <span class="font-semibold">{{ $transaction->payment_timing_text }}</span>
                        </div>
                    @endif

                    {{-- Total Price --}}
                    @if ($transaction->total_price)
                        <div class="divider my-1"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Jumlah Bayar</span>
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

                {{-- Action Button --}}
                <x-button label="Detail Pesanan" link="{{ route('pelanggan.pesanan.detail', $transaction->id) }}"
                    icon="solar.bill-list-bold-duotone" class="btn-primary btn-sm btn-block mt-3" />
            </div>
        </div>
    @empty
        {{-- Empty State --}}
        <div class="card bg-base-300 shadow">
            <div class="card-body items-center text-center py-8">
                <x-icon name="solar.inbox-bold-duotone" class="w-12 h-12 text-base-content/20 mb-3" />
                <h3 class="font-bold text-md">Belum Ada Pesanan</h3>
                <p class="text-xs text-base-content/60 mb-4">
                    Yuk mulai buat pesanan laundry kamu sekarang!
                </p>
                <x-button link="{{ route('pelanggan.buat-pesanan') }}" icon="solar.add-circle-bold-duotone"
                    label="Buat Pesanan" class="btn-primary btn-sm" />
            </div>
        </div>
    @endforelse
</div>
