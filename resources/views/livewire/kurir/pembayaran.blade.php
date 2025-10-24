@php use App\Helper\StatusTransactionHelper; @endphp
<section class="bg-base-100 min-h-dvh w-full" wire:poll.25s.visible>
    {{-- Header --}}
    <x-header icon="solar.wallet-money-bold-duotone" icon-classes="text-primary w-6 h-6" title="Pembayaran"
        subtitle="Verifikasi & Konfirmasi Pembayaran" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input wire:model.live.debounce.500ms="search" icon="solar.magnifer-bold-duotone"
                placeholder="Cari invoice atau customer..." clearable />
        </x-slot:middle>
        <x-slot:actions>
            <x-dropdown no-x-anchor right>
                <x-slot:trigger>
                    <x-button icon="solar.filter-bold-duotone" class="btn-circle btn-primary" />
                </x-slot:trigger>
                <x-menu-item title="Belum Ada Bukti" icon="solar.close-circle-bold-duotone"
                    wire:click="$set('filter', 'without_proof')" />
                <x-menu-item title="Sudah Ada Bukti" icon="solar.check-circle-bold-duotone"
                    wire:click="$set('filter', 'with_proof')" />
                <x-menu-item title="Semua" icon="solar.widget-bold-duotone" wire:click="$set('filter', 'all')" />
            </x-dropdown>
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Stats Cards Component --}}
        <livewire:kurir.components.stats-pembayaran />

        {{-- Payment Cards --}}
        <div class="space-y-3">
            @forelse ($this->payments as $payment)
                <div class="card bg-base-300 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="card-body p-4">
                        {{-- Header: Invoice & Tanggal Payment --}}
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="font-bold text-primary text-lg">
                                    {{ $payment->transaction->invoice_number }}
                                </h3>
                                <p class="text-xs text-base-content/60">
                                    Dibayar: {{ $payment->formatted_payment_date }}
                                </p>
                            </div>
                            @if ($payment->transaction->payment_status === 'paid')
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
                        <div class="flex items-center gap-3 mb-2">
                            <div class="avatar avatar-placeholder">
                                <div class="bg-primary text-primary-content w-10 rounded-full">
                                    <span class="text-sm">{{ $payment->transaction->customer?->getInitials() ?? 'NA' }}</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold">
                                    {{ $payment->transaction->customer?->name ?? 'Customer tidak ditemukan' }}</p>
                                <p class="text-xs text-base-content/60">
                                    {{ $payment->transaction->customer?->phone ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Payment Info --}}
                        <div class="bg-base-200 rounded-lg p-3 space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Layanan</span>
                                <span class="font-semibold">{{ $payment->transaction->service?->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Berat</span>
                                <span class="font-semibold">{{ $payment->transaction->weight }} kg</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Harga/kg</span>
                                <span class="font-semibold">{{ $payment->transaction->formatted_price_per_kg }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Metode Pembayaran</span>
                                <span class="font-semibold">
                                    {{ $payment->transaction->payment_timing_text }}
                                </span>
                            </div>
                            <div class="divider my-1"></div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-base-content/70">Jumlah Bayar</span>
                                <span class="font-semibold text-right text-primary text-base">
                                    {{ $payment->formatted_amount }}
                                </span>
                            </div>
                        </div>

                        {{-- Upload Payment Proof Section (if no proof yet) --}}
                        @if (!$payment->payment_proof_url)
                            <div class="mt-3 bg-base-200 rounded-lg p-3">
                                <x-file wire:model="paymentProofs.{{ $payment->id }}" label="Bukti Pembayaran"
                                    hint="Upload foto/screenshot bukti pembayaran"
                                    accept="image/png, image/jpeg, image/jpg" />
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="mt-3">
                            @if (!$payment->payment_proof_url)
                                {{-- Tampilkan Upload + Detail jika belum ada bukti --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <button wire:click="uploadPaymentProof({{ $payment->id }})"
                                        class="btn btn-success btn-sm"
                                        @if (empty($paymentProofs[$payment->id])) disabled @endif>
                                        <x-icon name="solar.upload-bold-duotone" class="w-4 h-4" />
                                        Upload Bukti
                                    </button>
                                    <a href="{{ route('kurir.pembayaran.detail', $payment->transaction->id) }}"
                                        class="btn btn-primary btn-sm">
                                        <x-icon name="solar.wallet-bold-duotone" class="w-4 h-4" />
                                        Detail
                                    </a>
                                </div>
                            @else
                                {{-- Tampilkan hanya Detail jika sudah ada bukti --}}
                                <a href="{{ route('kurir.pembayaran.detail', $payment->transaction->id) }}"
                                    class="btn btn-primary btn-sm w-full">
                                    <x-icon name="solar.wallet-bold-duotone" class="w-4 h-4" />
                                    Detail Pembayaran
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="card bg-base-300 shadow-lg">
                    <div class="card-body items-center text-center py-12">
                        <x-icon name="solar.inbox-bold-duotone" class="w-16 h-16 text-base-content/20 mb-4" />
                        <h3 class="font-bold text-lg">Tidak Ada Pembayaran</h3>
                        <p class="text-base-content/60">
                            @if ($filter === 'without_proof')
                                Belum ada pembayaran yang perlu diupload buktinya.
                            @elseif ($filter === 'with_proof')
                                Belum ada pembayaran yang sudah ada buktinya.
                            @else
                                Belum ada data pembayaran.
                            @endif
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
