@php
use App\Helper\StatusTransactionHelper;
use App\Helper\QrisHelper;
@endphp
<section class="bg-base-100" wire:poll.25s.visible>
    {{-- Header --}}
    <x-header icon="solar.wallet-money-bold-duotone" icon-classes="text-primary w-6 h-6" title="Detail Pembayaran"
        subtitle="Pelanggan {{ $transaction->customer?->name ?? 'Customer tidak ditemukan' }}" separator
        progress-indicator>
        <x-slot:actions>
            <x-button icon="solar.undo-left-linear" link="{{ route('kurir.pembayaran') }}"
                class="btn-circle btn-secondary" />
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Card Informasi Transaksi & Pembayaran --}}
        <div class="card bg-base-300 shadow">
            <div class="card-body p-4">
                {{-- Invoice & Payment Status Badge --}}
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-bold text-lg text-primary">{{ $transaction->invoice_number }}</p>
                        @php
                            $paymentDate = \App\Helper\Database\PaymentHelper::getFormattedPaymentDate($payment);
                        @endphp
                        @if ($paymentDate !== '-')
                            <p class="text-xs text-base-content/60">
                                Dibayar: {{ $paymentDate }}
                            </p>
                        @endif
                    </div>
                    @if ($transaction->payment_status === 'paid')
                        <span class="badge badge-success">
                            Lunas
                        </span>
                    @else
                        <span class="badge badge-error">
                            Belum Bayar
                        </span>
                    @endif
                </div>

                <div class="divider my-2"></div>

                {{-- Customer Info --}}
                <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                    <x-icon name="solar.user-bold-duotone" class="w-4 h-4 text-primary" />
                    Informasi Pelanggan
                </h3>

                @php
                    $customerName = $transaction->customer ? \App\Helper\Database\CustomerHelper::getName($transaction->customer) : 'N/A';
                    $customerPhone = $transaction->customer?->phone ?? '-';
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
                        <span class="font-semibold">{{ $customerPhone }}</span>
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

                {{-- Payment Summary --}}
                <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                    <x-icon name="solar.wallet-bold-duotone" class="w-4 h-4 text-primary" />
                    Ringkasan Pembayaran
                </h3>

                @php
                    $items = \App\Helper\Database\TransactionHelper::getItems($transaction);
                    $totalPrice = \App\Helper\Database\TransactionHelper::getTotalPrice($transaction);
                    $paymentDateSummary = \App\Helper\Database\PaymentHelper::getFormattedPaymentDate($payment);
                @endphp

                <div class="bg-base-200 rounded-lg p-3 space-y-2 mb-3">
                    {{-- List Items/Layanan --}}
                    <div>
                        <p class="text-xs font-semibold text-base-content/70 mb-2">Layanan yang Dipesan:</p>
                        <div class="space-y-2">
                            @foreach ($items as $index => $item)
                                @php
                                    // Get service info from service_name/pricing_unit or fallback to service_id lookup
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
                                        <span class="text-sm font-semibold text-primary">
                                            {{ $serviceName }}
                                        </span>
                                        <span class="text-xs badge badge-outline">
                                            @if ($pricingUnit === 'per_kg')
                                                {{ $item['total_weight'] ?? 0 }} kg
                                            @else
                                                {{ $item['quantity'] ?? 0 }} item
                                            @endif
                                        </span>
                                    </div>

                                    {{-- Clothing Items (jika ada) --}}
                                    @if (!empty($item['clothing_items']))
                                        <div class="mt-2 space-y-1">
                                            <p class="text-xs text-base-content/60">Detail Pakaian:</p>
                                            @foreach ($item['clothing_items'] as $clothing)
                                                @php
                                                    // Get clothing type name or fallback to clothing_type_id lookup
                                                    $clothingTypeName = $clothing['clothing_type_name'] ?? null;
                                                    if (!$clothingTypeName && !empty($clothing['clothing_type_id'])) {
                                                        $clothingType = \App\Models\ClothingType::find($clothing['clothing_type_id']);
                                                        $clothingTypeName = $clothingType?->name ?? 'N/A';
                                                    }
                                                    $clothingTypeName = $clothingTypeName ?: 'N/A';
                                                @endphp
                                                <div class="flex justify-between text-xs pl-2">
                                                    <span class="text-base-content/70">• {{ $clothingTypeName }}</span>
                                                    <span class="text-base-content/60">{{ $clothing['quantity'] ?? 0 }} pcs</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="flex justify-between items-center text-xs mt-2 pt-2 border-t border-base-200">
                                        <span class="text-base-content/60">Subtotal</span>
                                        <span class="font-semibold">Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="divider my-1"></div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Metode Pembayaran</span>
                        <span class="font-semibold">
                            {{ $transaction->payment_timing_text ?? 'N/A' }}
                        </span>
                    </div>
                    @if ($paymentDateSummary !== '-')
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Tanggal Bayar</span>
                            <span class="font-semibold">{{ $paymentDateSummary }}</span>
                        </div>
                    @endif
                    <div class="divider my-1"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Total Pembayaran</span>
                        <span class="font-semibold text-right text-primary text-base">
                            Rp {{ number_format($totalPrice, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Payment Proof Image --}}
                @if ($this->hasPaymentProof)
                    <div class="divider my-2"></div>

                    <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                        <x-icon name="solar.gallery-bold-duotone" class="w-4 h-4 text-primary" />
                        Bukti Pembayaran
                    </h3>

                    <img src="{{ asset('storage/' . $this->paymentProofUrl) }}" alt="Bukti Pembayaran"
                        class="w-full rounded-lg shadow cursor-pointer"
                        onclick="document.getElementById('payment_proof_modal').showModal()" />

                    {{-- Modal untuk preview image --}}
                    <dialog id="payment_proof_modal" class="modal">
                        <div class="modal-box max-w-4xl">
                            <form method="dialog">
                                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                            </form>
                            <h3 class="font-bold text-lg mb-4">Bukti Pembayaran</h3>
                            <img src="{{ asset('storage/' . $this->paymentProofUrl) }}"
                                alt="Bukti Pembayaran" class="w-full rounded-lg" />
                        </div>
                        <form method="dialog" class="modal-backdrop">
                            <button>close</button>
                        </form>
                    </dialog>
                @endif

                {{-- Upload Payment Proof Section (hanya jika Payment ada tapi belum ada bukti) --}}
                @if (!$this->hasPaymentProof)
                    <div class="divider my-2"></div>

                    <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                        <x-icon name="solar.upload-bold-duotone" class="w-4 h-4 text-primary" />
                        Pilih Bukti Pembayaran
                    </h3>

                    <div class="bg-base-200 rounded-lg p-3">
                        {{-- Upload File --}}
                        <x-file wire:model="paymentProof" label="Bukti Pembayaran"
                            hint="Upload foto/screenshot bukti pembayaran" accept="image/png, image/jpeg, image/jpg" />
                    </div>
                @endif

                {{-- Notes --}}
                @php
                    $notes = \App\Helper\Database\TransactionHelper::getNotes($transaction);
                @endphp
                @if ($notes)
                    <div class="divider my-2"></div>

                    <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                        <x-icon name="solar.document-text-bold-duotone" class="w-4 h-4 text-primary" />
                        Catatan Transaksi
                    </h3>

                    <div class="bg-base-200 rounded-lg p-3">
                        <p class="text-sm">{{ $notes }}</p>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="mt-3">
                    @if ($transaction->payment_status === 'unpaid')
                        <div class="grid grid-cols-2 gap-2">
                            {{-- QR Code Button --}}
                            <x-button wire:click="generateQrCode" label="Generate QR" icon="solar.qr-code-bold-duotone"
                                class="btn-primary btn-sm" />

                            {{-- Upload Bukti Button --}}
                            @if (!$this->hasPaymentProof)
                                <x-button wire:click="openUploadModal" label="Upload Bukti" icon="solar.upload-bold-duotone"
                                    class="btn-success btn-sm" :disabled="empty($paymentProof)" />
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal QR Code Pembayaran --}}
    <x-modal wire:model="showQrModal" title="QR Code Pembayaran"
        subtitle="Scan QR Code untuk pembayaran" class="modal-bottom sm:modal-middle" persistent>
        <div class="py-4">
            @if (!empty($qrSvg))
                <div class="text-center space-y-4">
                    {{-- QR Code SVG --}}
                    <div class="flex justify-center">
                        <div class="p-4 bg-white rounded-lg shadow">
                            <div class="w-64 h-64 flex items-center justify-center">
                                {!! $qrSvg !!}
                            </div>
                        </div>
                    </div>

                    {{-- Payment Info --}}
                    <div class="bg-base-200 rounded-lg p-4">
                        <h4 class="font-bold text-lg text-primary mb-2">
                            {{ QrisHelper::formatAmount($qrAmount) }}
                        </h4>
                        <p class="text-sm text-base-content/70">
                            Invoice: {{ $transaction->invoice_number }}
                        </p>
                        <p class="text-sm text-base-content/70">
                            Pelanggan: {{ $transaction->customer?->name ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
        <x-slot:actions>
            <x-button label="Download QR" wire:click="downloadQrCode" icon="solar.download-bold-duotone"
                class="btn-primary" :disabled="empty($qrCodeUrl)" />
            <x-button label="Tutup" wire:click="closeQrModal" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Konfirmasi Upload Bukti Pembayaran --}}
    <x-modal wire:model="showUploadModal" title="Upload Bukti Pembayaran"
        subtitle="Konfirmasi untuk mengupload bukti pembayaran?" class="modal-bottom sm:modal-middle" persistent
        separator>
        <div class="py-4">
            <p class="text-base-content/70">Pastikan file yang diupload adalah bukti pembayaran yang valid dan jelas
                terbaca.</p>
            <div class="mt-3 p-3 bg-warning/10 rounded-lg border border-warning/20">
                <p class="text-sm text-warning">
                    <strong>Perhatian:</strong> Setelah upload, status pembayaran akan otomatis berubah menjadi "Lunas".
                </p>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Batal" wire:click="$set('showUploadModal', false)" />
            <x-button label="Ya, Upload" wire:click="uploadPaymentProof" class="btn-success" />
        </x-slot:actions>
    </x-modal>
</section>
