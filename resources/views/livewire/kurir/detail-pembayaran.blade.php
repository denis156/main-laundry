@php use App\Helper\StatusTransactionHelper; @endphp
<section class="bg-base-100" wire:poll.25s.visible>
    {{-- Header --}}
    <x-header icon="solar.wallet-money-bold-duotone" icon-classes="text-primary w-6 h-6" title="Detail Pembayaran"
        subtitle="Pelanggan atas nama {{ $transaction->customer?->name ?? 'Customer tidak ditemukan' }}" separator progress-indicator>
        <x-slot:actions>
            <x-button icon="solar.undo-left-linear" link="{{ route('kurir.pembayaran') }}" class="btn-circle btn-secondary" />
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
                        @if ($this->payment?->payment_date)
                            <p class="text-xs text-base-content/60">
                                Dibayar: {{ $this->payment->formatted_payment_date }}
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

                {{-- Payment Summary --}}
                <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                    <x-icon name="solar.wallet-bold-duotone" class="w-4 h-4 text-primary" />
                    Ringkasan Pembayaran
                </h3>

                <div class="bg-base-200 rounded-lg p-3 space-y-2 mb-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Layanan</span>
                        <span class="font-semibold">{{ $transaction->service?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Berat</span>
                        <span class="font-semibold">{{ $transaction->weight }} kg</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Metode Pembayaran</span>
                        <span class="font-semibold">
                            {{ $transaction->payment_timing_text }}
                        </span>
                    </div>
                    @if ($this->payment?->payment_date)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Tanggal Bayar</span>
                            <span class="font-semibold">{{ $this->payment->formatted_payment_date }}</span>
                        </div>
                    @endif
                    <div class="divider my-1"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Jumlah Bayar</span>
                        <span class="font-semibold text-right text-primary text-base">
                            {{ $transaction->formatted_total_price }}
                        </span>
                    </div>
                </div>

                {{-- Payment Proof Image --}}
                @if ($this->payment?->payment_proof_url)
                    <div class="divider my-2"></div>

                    <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                        <x-icon name="solar.gallery-bold-duotone" class="w-4 h-4 text-primary" />
                        Bukti Pembayaran
                    </h3>

                    <img src="{{ asset('storage/' . $this->payment->payment_proof_url) }}" alt="Bukti Pembayaran"
                        class="w-full rounded-lg shadow cursor-pointer"
                        onclick="document.getElementById('payment_proof_modal').showModal()" />

                    {{-- Modal untuk preview image --}}
                    <dialog id="payment_proof_modal" class="modal">
                        <div class="modal-box max-w-4xl">
                            <form method="dialog">
                                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                            </form>
                            <h3 class="font-bold text-lg mb-4">Bukti Pembayaran</h3>
                            <img src="{{ asset('storage/' . $this->payment->payment_proof_url) }}" alt="Bukti Pembayaran"
                                class="w-full rounded-lg" />
                        </div>
                        <form method="dialog" class="modal-backdrop">
                            <button>close</button>
                        </form>
                    </dialog>
                @endif

                {{-- Upload Payment Proof Section (hanya jika Payment ada tapi belum ada bukti) --}}
                @if ($this->payment && !$this->payment->payment_proof_url)
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
                @elseif (!$this->payment)
                    {{-- Info jika Payment belum dibuat --}}
                    <div class="divider my-2"></div>

                    <div class="alert alert-info">
                        <x-icon name="solar.info-circle-bold-duotone" class="w-5 h-5" />
                        <span class="text-sm">
                            Record pembayaran akan otomatis dibuat saat pesanan sudah dijemput atau siap diantar.
                        </span>
                    </div>
                @endif

                {{-- Notes --}}
                @if ($transaction->notes)
                    <div class="divider my-2"></div>

                    <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                        <x-icon name="solar.document-text-bold-duotone" class="w-4 h-4 text-primary" />
                        Catatan Transaksi
                    </h3>

                    <div class="bg-base-200 rounded-lg p-3">
                        <p class="text-sm">{{ $transaction->notes }}</p>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="mt-3">
                    @php
                        $hasPayment = $this->payment !== null;
                        $hasBukti = $this->payment?->payment_proof_url !== null;
                    @endphp

                    @if ($hasPayment && !$hasBukti)
                        {{-- Ada Payment tapi belum ada bukti - Tampilkan hanya Upload --}}
                        <x-button wire:click="openUploadModal" label="Upload Bukti"
                            icon="solar.upload-bold-duotone" class="btn-success btn-sm btn-block"
                            :disabled="empty($paymentProof)" />
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Upload Bukti Pembayaran --}}
    <x-modal wire:model="showUploadModal" title="Upload Bukti Pembayaran" subtitle="Konfirmasi untuk mengupload bukti pembayaran?" class="modal-bottom sm:modal-middle" persistent separator>
        <div class="py-4">
            <p class="text-base-content/70">Pastikan file yang diupload adalah bukti pembayaran yang valid dan jelas terbaca.</p>
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
