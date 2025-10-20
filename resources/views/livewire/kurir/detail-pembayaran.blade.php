<div class="bg-base-100 min-h-dvh w-full" wire:poll.25s.visible>
    {{-- Header --}}
    <x-header title="Detail Pembayaran" subtitle="{{ $transaction->invoice_number }}" separator progress-indicator>
        <x-slot:actions>
            <a href="{{ route('kurir.pembayaran') }}" class="btn btn-circle btn-ghost">
                <x-icon name="solar.undo-left-linear" class="w-6 h-6" />
            </a>
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Card Informasi Transaksi & Pembayaran --}}
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
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Berat</span>
                        <span class="font-semibold">{{ $transaction->weight }} kg</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Harga/kg</span>
                        <span class="font-semibold">Rp {{ number_format($transaction->price_per_kg, 0, ',', '.') }}</span>
                    </div>
                    <div class="divider my-1"></div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-base">Total</span>
                        <span class="font-bold text-primary text-lg">
                            Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="divider my-2"></div>

                {{-- Payment Info --}}
                <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                    <x-icon name="solar.wallet-bold-duotone" class="w-4 h-4 text-primary" />
                    Informasi Pembayaran
                </h3>

                <div class="bg-base-200 rounded-lg p-3 space-y-2 mb-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Metode Pembayaran</span>
                        <span class="font-semibold">
                            {{ $transaction->payment_timing === 'on_pickup' ? 'Bayar Saat Jemput' : 'Bayar Saat Antar' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Status Pembayaran</span>
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
                    @if ($transaction->paid_at)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-base-content/70">Tanggal Bayar</span>
                            <span class="font-semibold">{{ $transaction->paid_at->format('d M Y, H:i') }}</span>
                        </div>
                    @endif
                </div>

                {{-- Payment Proof Image --}}
                @if ($transaction->payment_proof_url)
                    <div class="divider my-2"></div>

                    <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                        <x-icon name="solar.gallery-bold-duotone" class="w-4 h-4 text-primary" />
                        Bukti Pembayaran
                    </h3>

                    <div class="bg-base-200 rounded-lg p-3">
                        <img src="{{ asset('storage/' . $transaction->payment_proof_url) }}"
                            alt="Bukti Pembayaran"
                            class="w-full rounded-lg shadow-lg cursor-pointer hover:shadow-xl transition-shadow"
                            onclick="document.getElementById('payment_proof_modal').showModal()" />
                    </div>

                    {{-- Modal untuk preview image --}}
                    <dialog id="payment_proof_modal" class="modal">
                        <div class="modal-box max-w-4xl">
                            <form method="dialog">
                                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                            </form>
                            <h3 class="font-bold text-lg mb-4">Bukti Pembayaran</h3>
                            <img src="{{ asset('storage/' . $transaction->payment_proof_url) }}"
                                alt="Bukti Pembayaran"
                                class="w-full rounded-lg" />
                        </div>
                        <form method="dialog" class="modal-backdrop">
                            <button>close</button>
                        </form>
                    </dialog>
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

                {{-- Upload Payment Proof Section (hanya jika belum bayar) --}}
                @if ($transaction->payment_status === 'unpaid')
                    <div class="divider my-2"></div>

                    <h3 class="font-bold text-base mb-2 flex items-center gap-2">
                        <x-icon name="solar.upload-bold-duotone" class="w-4 h-4 text-primary" />
                        Upload Bukti Pembayaran
                    </h3>

                    <div class="bg-base-200 rounded-lg p-3 space-y-3">
                        {{-- Upload File --}}
                        <x-file wire:model="paymentProof"
                            label="Bukti Pembayaran"
                            hint="Upload foto/screenshot bukti pembayaran"
                            accept="image/png, image/jpeg, image/jpg" />

                        {{-- Upload Button --}}
                        <button wire:click="uploadPaymentProof"
                            class="btn btn-success btn-sm w-full"
                            @if (empty($paymentProof)) disabled @endif>
                            <x-icon name="solar.upload-bold-duotone" class="w-4 h-4" />
                            Upload Bukti Pembayaran
                        </button>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="mt-3">
                    @if ($transaction->payment_status === 'unpaid' && $transaction->customer?->phone && $transaction->customer?->name)
                        {{-- Grid 2 Kolom untuk WhatsApp dan Back --}}
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ $this->getWhatsAppReminderUrl() }}"
                                target="_blank"
                                class="btn btn-warning btn-sm">
                                <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                                WhatsApp
                            </a>
                            <a href="{{ route('kurir.pembayaran') }}" class="btn btn-primary btn-sm">
                                <x-icon name="solar.undo-left-linear" class="w-4 h-4" />
                                Kembali
                            </a>
                        </div>
                    @else
                        {{-- Back Button Full Width --}}
                        <a href="{{ route('kurir.pembayaran') }}" class="btn btn-primary btn-sm w-full">
                            <x-icon name="solar.undo-left-linear" class="w-4 h-4" />
                            Kembali
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
