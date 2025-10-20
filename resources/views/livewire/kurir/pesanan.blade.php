<section class="bg-base-100 min-h-dvh w-full">
    {{-- Header --}}
    <x-header icon="solar.bill-list-bold-duotone" icon-classes="text-primary w-6 h-6" title="Pesanan" subtitle="Monitor & Proses Pesanan Pelanggan" separator progress-indicator>
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
        {{-- Stats Cards dengan Polling (Component Terpisah) --}}
        <livewire:kurir.components.pesanan-stats />

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

                        {{-- Service, Pos, Berat & Address Info --}}
                        @if ($transaction->service_id || $transaction->pos_id || $transaction->weight || $transaction->customer?->address)
                            <div class="mt-2 bg-base-200 rounded-lg p-3 space-y-2">
                                {{-- Layanan --}}
                                @if ($transaction->service_id)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-base-content/70">Layanan</span>
                                        <span class="font-semibold">{{ $transaction->service?->name ?? 'N/A' }}</span>
                                    </div>
                                @endif

                                {{-- Pos --}}
                                @if ($transaction->pos_id)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-base-content/70">Pos</span>
                                        <span class="font-semibold">{{ $transaction->pos?->name ?? 'N/A' }}</span>
                                    </div>
                                @endif

                                {{-- Berat --}}
                                @if ($transaction->weight)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-base-content/70">Berat</span>
                                        <span class="font-semibold">{{ $transaction->weight }} kg</span>
                                    </div>
                                @endif

                                {{-- Address --}}
                                @if ($transaction->customer?->address)
                                    @if ($transaction->service_id || $transaction->pos_id || $transaction->weight)
                                        <div class="divider my-1"></div>
                                    @endif
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-base-content/70">Alamat</span>
                                        <span class="font-semibold text-right text-primary text-sm">{{ $transaction->customer->address }}</span>
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

                        {{-- Action Buttons --}}
                        <div class="mt-3 space-y-2">
                            @if ($transaction->workflow_status === 'pending_confirmation')
                                {{-- Status: Pending Confirmation - Tampilkan Batalkan + Ambil Pesanan --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <button wire:click="cancelOrder({{ $transaction->id }})"
                                        class="btn btn-error btn-sm">
                                        <x-icon name="solar.close-circle-bold-duotone" class="w-4 h-4" />
                                        Batalkan Pesanan
                                    </button>

                                    <button wire:click="confirmOrder({{ $transaction->id }})"
                                        class="btn btn-accent btn-sm">
                                        <x-icon name="solar.check-circle-bold-duotone" class="w-4 h-4" />
                                        Ambil Pesanan
                                    </button>
                                </div>
                            @elseif ($transaction->workflow_status === 'confirmed')
                                {{-- Status: Confirmed - Tampilkan Input Berat + Upload Bukti (jika bayar saat jemput) + WhatsApp + Sudah Dijemput --}}
                                <div class="bg-base-200 rounded-lg p-3 mb-2 space-y-3">
                                    {{-- Input Berat --}}
                                    <x-input wire:model.blur="weights.{{ $transaction->id }}" type="number" step="0.01"
                                        min="0.01" label="Berat Cucian (kg)" placeholder="Contoh: 8.92"
                                        icon="solar.scale-bold-duotone"
                                        hint="Hint total harga akan muncul setelah input berat" />

                                    {{-- Upload Bukti Pembayaran - Hanya untuk bayar saat jemput --}}
                                    @if ($transaction->payment_timing === 'on_pickup')
                                        <x-file wire:model="paymentProofs.{{ $transaction->id }}"
                                            label="Bukti Pembayaran" hint="Upload foto/screenshot bukti pembayaran"
                                            accept="image/png, image/jpeg, image/jpg" />
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    @if ($transaction->customer?->phone && $transaction->customer?->name)
                                        <a href="{{ $this->getWhatsAppUrl($transaction->customer->phone, $transaction->customer->name, $transaction) }}"
                                            target="_blank" class="btn btn-success btn-sm">
                                            <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                                            WhatsApp
                                        </a>
                                    @endif

                                    <button wire:click="markAsPickedUp({{ $transaction->id }})"
                                        class="btn btn-warning btn-sm {{ $transaction->customer?->phone && $transaction->customer?->name ? '' : 'col-span-2' }}"
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
                                </div>
                            @elseif ($transaction->workflow_status === 'picked_up')
                                {{-- Status: Picked Up - Tampilkan Sudah di Pos + Detail --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <button wire:click="markAsAtLoadingPost({{ $transaction->id }})"
                                        class="btn btn-warning btn-sm">
                                        <x-icon name="solar.map-point-bold-duotone" class="w-4 h-4" />
                                        Sudah di Pos
                                    </button>

                                    <a href="{{ route('kurir.pesanan.detail', $transaction->id) }}" class="btn btn-primary btn-sm">
                                        <x-icon name="solar.bill-list-bold-duotone" class="w-4 h-4" />
                                        Detail Pesanan
                                    </a>
                                </div>
                            @elseif ($transaction->workflow_status === 'washing_completed')
                                {{-- Status: Washing Completed - Tampilkan WhatsApp + Dalam Pengiriman --}}
                                <div class="grid grid-cols-2 gap-2">
                                    @if ($transaction->customer?->phone && $transaction->customer?->name)
                                        <a href="{{ $this->getWhatsAppUrlForDelivery($transaction->customer->phone, $transaction->customer->name, $transaction) }}"
                                            target="_blank" class="btn btn-success btn-sm">
                                            <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                                            WhatsApp
                                        </a>
                                    @endif

                                    <button wire:click="markAsOutForDelivery({{ $transaction->id }})"
                                        class="btn btn-accent btn-sm {{ $transaction->customer?->phone && $transaction->customer?->name ? '' : 'col-span-2' }}">
                                        <x-icon name="solar.delivery-bold-duotone" class="w-4 h-4" />
                                        Dalam Pengiriman
                                    </button>
                                </div>
                            @elseif ($transaction->workflow_status === 'out_for_delivery')
                                {{-- Status: Out for Delivery - Tampilkan Upload Bukti (jika bayar saat antar DAN belum bayar) + Terkirim --}}
                                @if ($transaction->payment_timing === 'on_delivery' && $transaction->payment_status === 'unpaid')
                                    <div class="bg-base-200 rounded-lg p-3 mb-2">
                                        {{-- Upload Bukti Pembayaran - Hanya untuk bayar saat antar yang belum bayar --}}
                                        <x-file wire:model="paymentProofs.{{ $transaction->id }}"
                                            label="Bukti Pembayaran" hint="Upload foto/screenshot bukti pembayaran"
                                            accept="image/png, image/jpeg, image/jpg" />
                                    </div>
                                @endif

                                <button wire:click="markAsDelivered({{ $transaction->id }})"
                                    class="btn btn-success btn-sm w-full"
                                    @php
                                        $disabled = false;
                                        // Jika bayar saat antar DAN belum bayar, bukti pembayaran harus ada
                                        if ($transaction->payment_timing === 'on_delivery' && $transaction->payment_status === 'unpaid') {
                                            $disabled = empty($paymentProofs[$transaction->id]);
                                        }
                                    @endphp
                                    @if ($disabled) disabled @endif>
                                    <x-icon name="solar.check-circle-bold-duotone" class="w-4 h-4" />
                                    Terkirim
                                </button>
                            @else
                                {{-- Status lain (at_loading_post, in_washing, delivered, cancelled) - Hanya tampilkan Detail --}}
                                <a href="{{ route('kurir.pesanan.detail', $transaction->id) }}" class="btn btn-primary btn-sm w-full">
                                    <x-icon name="solar.bill-list-bold-duotone" class="w-4 h-4" />
                                    Detail Pesanan
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
