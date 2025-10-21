<section class="bg-base-100">
    <x-header icon="solar.home-bold-duotone" icon-classes="text-primary w-6 h-6" title="Beranda"
        subtitle="Dashboard Kurir {{ config('app.name') }}" separator />

    <div class="space-y-4">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="stats shadow truncate">
                <div class="stat bg-secondary">
                    <div class="stat-figure text-secondary-content">
                        <x-icon name="solar.add-circle-bold-duotone" class="inline-block h-8 stroke-current" />
                    </div>
                    <div class="stat-title text-secondary-content">Konfirmasi?</div>
                    <div class="stat-value text-secondary-content">{{ $this->pendingConfirmation }}</div>
                </div>
            </div>
            <div class="stats shadow truncate">
                <div class="stat bg-error">
                    <div class="stat-figure text-error-content">
                        <x-icon name="solar.map-point-wave-bold-duotone" class="inline-block h-8 stroke-current" />
                    </div>
                    <div class="stat-title text-error-content">Jemput!</div>
                    <div class="stat-value text-error-content">{{ $this->pendingPickup }}</div>
                </div>
            </div>
        </div>

        {{-- Welcome Card --}}
        <x-card class="bg-base-300" title="Hai {{ $this->greeting }}" subtitle="{{ $this->todayDate }}" shadow
            separator>
            <x-slot:menu>
                @if ($this->assignedPos)
                    <x-badge value="{{ $this->assignedPos->name }}" class="badge-primary badge-xs md:badge-sm" />
                @endif
            </x-slot:menu>
            <x-avatar :image="$this->courier->getFilamentAvatarUrl()" class="!w-22">
                <x-slot:title class="text-4xl text-base-content font-bold pl-2">
                    {{ $this->courier->name }}
                </x-slot:title>

                <x-slot:subtitle class="grid gap-1 mt-2 pl-2 text-md">
                    <x-icon name="solar.add-circle-linear" class="text-secondary"
                        label="{{ $this->pendingConfirmation }} menunggu konfirmasi" />
                    <x-icon name="solar.check-circle-linear" class="text-secondary"
                        label="{{ $this->completedTransactions }} transaksi selesai" />
                </x-slot:subtitle>

            </x-avatar>
        </x-card>

        {{-- Quick Actions --}}
        <x-card class="bg-base-300" body-class="grid grid-cols-2 gap-4" title="Aksi Cepat"
            subtitle="Navigasi cepat untuk tugas harian" shadow separator>
            <x-button link="{{ route('kurir.pesanan') }}" icon="solar.bill-list-bold-duotone" label="Lihat Pesanan"
                class="btn-primary btn-sm" />
            <x-button link="{{ route('kurir.pembayaran') }}" icon="solar.wallet-money-bold-duotone" label="Pembayaran"
                class="btn-secondary btn-sm" />
            <x-button link="{{ $this->getWhatsAppCSUrl() }}" target="_blank" icon="solar.chat-round-bold-duotone"
                label="Hubungi CS" class="btn-success btn-sm" external />
            <x-button link="{{ route('kurir.info') }}" icon="solar.info-circle-bold-duotone" label="Info & FAQ"
                class="btn-info btn-sm" />
        </x-card>

        {{-- Pending Confirmation Transactions --}}
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

                    {{-- Action Button --}}
                    <div class="mt-3">
                        <a href="{{ route('kurir.pesanan.detail', $transaction->id) }}" class="btn btn-primary btn-sm w-full">
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
</section>
