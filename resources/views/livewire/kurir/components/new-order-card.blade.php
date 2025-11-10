{{-- Pending Confirmation Transactions --}}
<div class="space-y-3">
    @forelse ($this->pendingConfirmationTransactions as $transaction)
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
                @php
                    $customerName = $transaction->customer ? \App\Helper\Database\CustomerHelper::getName($transaction->customer) : 'Customer tidak ditemukan';
                @endphp
                <div class="flex items-center gap-3 mb-2">
                    <div class="avatar">
                        <div class="ring-accent ring-offset-base-100 w-10 h-10 rounded-full ring-2 ring-offset-2">
                            <img src="{{ $transaction->customer?->getFilamentAvatarUrl() }}"
                                 alt="{{ $customerName }}" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold">{{ $customerName }}</p>
                        <p class="text-xs text-base-content/60">{{ $transaction->customer?->phone ?? '-' }}</p>
                    </div>
                </div>

                {{-- Order Info --}}
                @php
                    $items = \App\Helper\Database\TransactionHelper::getItems($transaction);
                    $defaultAddress = $transaction->customer ? \App\Helper\Database\CustomerHelper::getDefaultAddress($transaction->customer) : null;
                    $addressString = $defaultAddress ? \App\Helper\Database\CustomerHelper::getFullAddressString($defaultAddress) : null;
                    $notes = \App\Helper\Database\TransactionHelper::getNotes($transaction);
                @endphp

                <div class="bg-base-200 rounded-lg p-3 space-y-2">
                    {{-- Status --}}
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Status</span>
                        <span class="badge badge-secondary gap-1">Pending</span>
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
                                        $serviceName = $item['service_name'] ?? 'N/A';
                                        $pricingUnit = $item['pricing_unit'] ?? 'per_kg';
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

                {{-- Action Button --}}
                <div class="mt-3">
                    <x-button label="Detail Pesanan" icon="solar.eye-bold"
                        link="{{ route('kurir.pesanan.detail', $transaction->id) }}"
                        class="btn-accent btn-sm btn-block" />
                </div>
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
