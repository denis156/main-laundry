<div class="bg-base-100 min-h-dvh w-full" wire:poll.30s.visible>
    {{-- Header --}}
    <x-header title="Pesanan" subtitle="Kelola Pesanan Transaksi" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input wire:model.live.debounce.500ms="search" icon="solar.magnifer-bold-duotone"
                placeholder="Cari invoice atau customer..." clearable />
        </x-slot:middle>
        <x-slot:actions>
            <x-dropdown no-x-anchor right>
                <x-slot:trigger>
                    <x-button icon="solar.filter-bold-duotone" class="btn-circle btn-primary" />
                </x-slot:trigger>
                <x-menu-item title="Semua Status" icon="solar.widget-bold-duotone"
                    wire:click="$set('filter', 'all')" />
                <x-menu-item title="Menunggu Konfirmasi" icon="solar.clock-circle-bold-duotone"
                    wire:click="$set('filter', 'pending_confirmation')" />
                <x-menu-item title="Terkonfirmasi" icon="solar.check-circle-bold-duotone"
                    wire:click="$set('filter', 'confirmed')" />
                <x-menu-item title="Sudah Dijemput" icon="solar.box-bold-duotone"
                    wire:click="$set('filter', 'picked_up')" />
                <x-menu-item title="Di Pos" icon="solar.map-point-bold-duotone"
                    wire:click="$set('filter', 'at_loading_post')" />
                <x-menu-item title="Sedang Dicuci" icon="solar.washing-machine-bold-duotone"
                    wire:click="$set('filter', 'in_washing')" />
                <x-menu-item title="Cucian Selesai" icon="solar.check-read-bold-duotone"
                    wire:click="$set('filter', 'washing_completed')" />
                <x-menu-item title="Dalam Pengiriman" icon="solar.delivery-bold-duotone"
                    wire:click="$set('filter', 'out_for_delivery')" />
                <x-menu-item title="Terkirim" icon="solar.star-bold-duotone"
                    wire:click="$set('filter', 'delivered')" />
                <x-menu-item title="Dibatalkan" icon="solar.close-circle-bold-duotone"
                    wire:click="$set('filter', 'cancelled')" />
            </x-dropdown>
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 gap-4">
            {{-- Pending --}}
            <div class="card bg-secondary text-secondary-content shadow-lg">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs opacity-80">Pending</p>
                            <p class="text-2xl font-bold">{{ $this->stats['pending_count'] }}</p>
                        </div>
                        <x-icon name="solar.clock-circle-bold-duotone" class="w-10 h-10 opacity-50" />
                    </div>
                    <div class="text-xs mt-2 font-semibold">
                        Menunggu Konfirmasi
                    </div>
                </div>
            </div>

            {{-- Active --}}
            <div class="card bg-primary text-primary-content shadow-lg">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs opacity-80">Aktif</p>
                            <p class="text-2xl font-bold">{{ $this->stats['active_count'] }}</p>
                        </div>
                        <x-icon name="solar.rocket-bold-duotone" class="w-10 h-10 opacity-50" />
                    </div>
                    <div class="text-xs mt-2 font-semibold">
                        Dalam Proses
                    </div>
                </div>
            </div>

            {{-- Delivered --}}
            <div class="card bg-success text-success-content shadow-lg">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs opacity-80">Selesai</p>
                            <p class="text-2xl font-bold">{{ $this->stats['delivered_count'] }}</p>
                        </div>
                        <x-icon name="solar.star-bold-duotone" class="w-10 h-10 opacity-50" />
                    </div>
                    <div class="text-xs mt-2 font-semibold">
                        Terkirim
                    </div>
                </div>
            </div>

            {{-- Cancelled --}}
            <div class="card bg-error text-error-content shadow-lg">
                <div class="card-body p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs opacity-80">Batal</p>
                            <p class="text-2xl font-bold">{{ $this->stats['cancelled_count'] }}</p>
                        </div>
                        <x-icon name="solar.close-circle-bold-duotone" class="w-10 h-10 opacity-50" />
                    </div>
                    <div class="text-xs mt-2 font-semibold">
                        Dibatalkan
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

                        {{-- Customer Address & Notes --}}
                        @if ($transaction->customer?->address || $transaction->notes)
                            <div class="mt-2 bg-base-200 rounded-lg p-3 space-y-3">
                                {{-- Address --}}
                                @if ($transaction->customer?->address)
                                    <div class="flex items-start gap-2">
                                        <x-icon name="solar.map-point-bold-duotone"
                                            class="w-5 h-5 text-base-content/70 mt-0.5" />
                                        <div class="flex-1">
                                            <p class="text-xs text-base-content/70 font-semibold mb-1">Alamat Pickup:</p>
                                            <p class="text-sm">{{ $transaction->customer->address }}</p>
                                        </div>
                                    </div>
                                @endif

                                {{-- Notes --}}
                                @if ($transaction->notes)
                                    @if ($transaction->customer?->address)
                                        <div class="divider my-0"></div>
                                    @endif
                                    <div class="flex items-start gap-2">
                                        <x-icon name="solar.document-text-bold-duotone"
                                            class="w-5 h-5 text-base-content/70 mt-0.5" />
                                        <div class="flex-1">
                                            <p class="text-xs text-base-content/70 font-semibold mb-1">Catatan:</p>
                                            <p class="text-sm">{{ $transaction->notes }}</p>
                                        </div>
                                    </div>
                                @endif
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

                        {{-- Action Buttons --}}
                        <div class="mt-3 space-y-2">
                            @if ($transaction->workflow_status === 'pending_confirmation')
                                {{-- Status: Pending Confirmation - Tampilkan WhatsApp + Ambil Pesanan --}}
                                <div class="grid grid-cols-2 gap-2">
                                    @if ($transaction->customer?->phone && $transaction->customer?->name)
                                        <a href="{{ $this->getWhatsAppUrl($transaction->customer->phone, $transaction->customer->name) }}"
                                            target="_blank" class="btn btn-success btn-sm">
                                            <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                                            WhatsApp
                                        </a>
                                    @endif

                                    <button wire:click="confirmOrder({{ $transaction->id }})"
                                        class="btn btn-accent btn-sm {{ $transaction->customer?->phone && $transaction->customer?->name ? '' : 'col-span-2' }}">
                                        <x-icon name="solar.check-circle-bold-duotone" class="w-4 h-4" />
                                        Ambil Pesanan
                                    </button>
                                </div>
                            @elseif ($transaction->workflow_status === 'confirmed')
                                {{-- Status: Confirmed - Tampilkan Input Berat + Upload Bukti (jika bayar saat jemput) + Sudah Dijemput + Detail --}}
                                <div class="bg-base-200 rounded-lg p-3 mb-2 space-y-3">
                                    {{-- Input Berat --}}
                                    <x-input wire:model.live="weights.{{ $transaction->id }}" type="number" step="0.01"
                                        min="0" label="Berat Cucian (kg)" placeholder="Masukkan berat cucian..."
                                        icon="solar.scale-bold-duotone"
                                        hint="{{ $this->getTotalPriceHint($transaction) }}" />

                                    {{-- Upload Bukti Pembayaran - Hanya untuk bayar saat jemput --}}
                                    @if ($transaction->payment_timing === 'on_pickup')
                                        <x-file wire:model="paymentProofs.{{ $transaction->id }}"
                                            label="Bukti Pembayaran" hint="Upload foto/screenshot bukti pembayaran"
                                            accept="image/png, image/jpeg, image/jpg" />
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <button wire:click="markAsPickedUp({{ $transaction->id }})"
                                        class="btn btn-warning btn-sm"
                                        @php
                                            $disabled = empty($weights[$transaction->id]) || $weights[$transaction->id] <= 0;
                                            // Jika bayar saat jemput, bukti pembayaran harus ada
                                            if ($transaction->payment_timing === 'on_pickup') {
                                                $disabled = $disabled || empty($paymentProofs[$transaction->id]);
                                            }
                                        @endphp
                                        @if ($disabled) disabled @endif>
                                        <x-icon name="solar.box-bold-duotone" class="w-4 h-4" />
                                        Sudah Dijemput
                                    </button>

                                    <button class="btn btn-primary btn-sm">
                                        <x-icon name="solar.eye-bold-duotone" class="w-4 h-4" />
                                        Lihat Detail
                                    </button>
                                </div>
                            @elseif ($transaction->workflow_status === 'picked_up')
                                {{-- Status: Picked Up - Tampilkan Sudah di Pos + Detail --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <button wire:click="markAsAtLoadingPost({{ $transaction->id }})"
                                        class="btn btn-warning btn-sm">
                                        <x-icon name="solar.map-point-bold-duotone" class="w-4 h-4" />
                                        Sudah di Pos
                                    </button>

                                    <button class="btn btn-primary btn-sm">
                                        <x-icon name="solar.eye-bold-duotone" class="w-4 h-4" />
                                        Lihat Detail
                                    </button>
                                </div>
                            @else
                                {{-- Status lain - Hanya tampilkan Detail --}}
                                <button class="btn btn-primary btn-sm w-full">
                                    <x-icon name="solar.eye-bold-duotone" class="w-4 h-4" />
                                    Lihat Detail
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="card bg-base-300 shadow-lg">
                    <div class="card-body items-center text-center py-12">
                        <x-icon name="solar.inbox-bold-duotone" class="w-16 h-16 text-base-content/20 mb-4" />
                        <h3 class="font-bold text-lg">Tidak Ada Pesanan</h3>
                        <p class="text-base-content/60">
                            @if ($filter === 'all')
                                Belum ada pesanan untuk ditampilkan.
                            @else
                                Tidak ada pesanan dengan status ini.
                            @endif
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
