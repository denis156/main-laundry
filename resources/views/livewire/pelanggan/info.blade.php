<section class="bg-base-100 min-h-dvh w-full">
    {{-- Header --}}
    <x-header icon="solar.info-circle-bold-duotone" icon-classes="text-primary w-6 h-6" title="Pusat Bantuan"
        subtitle="Informasi lengkap layanan {{ config('app.name') }}" separator />

    <div class="space-y-4">
        {{-- Quick Links --}}
        <div class="card bg-linear-to-r from-primary/10 to-accent/10 shadow">
            <div class="card-body p-4">
                <h3 class="font-bold text-base mb-3 text-center">Butuh Bantuan Cepat?</h3>
                <div class="grid grid-cols-2 gap-2">
                    <a href="#kontak-cs" class="btn btn-primary btn-sm">
                        <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                        CS
                    </a>
                    <a href="#faq" class="btn btn-accent btn-sm">
                        <x-icon name="solar.question-circle-bold-duotone" class="w-4 h-4" />
                        FAQ
                    </a>
                </div>
            </div>
        </div>

        {{-- 1. Kontak Customer Service --}}
        <div id="kontak-cs" class="card bg-base-300 shadow scroll-mt-18">
            <div class="card-body p-4">
                <h3 class="font-bold text-base mb-3 flex items-center gap-2">
                    <x-icon name="solar.chat-round-bold-duotone" class="w-5 h-5 text-primary" />
                    Hubungi Kami
                </h3>

                <div class="space-y-3">
                    {{-- WhatsApp --}}
                    <div class="bg-base-200 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.phone-bold-duotone" class="w-4 h-4 text-success" />
                                <span class="text-sm font-semibold">WhatsApp</span>
                            </div>
                            <span class="text-sm">{{ $this->hasCSWhatsApp() ? config('sosmed.phone') : 'Tidak tersedia' }}</span>
                        </div>
                        @if ($this->hasCSWhatsApp())
                            <a href="{{ $this->getWhatsAppCSUrl() }}" target="_blank" class="btn btn-success btn-sm w-full">
                                <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                                Hubungi via WhatsApp
                            </a>
                        @else
                            <button disabled class="btn btn-success btn-sm w-full opacity-50 cursor-not-allowed">
                                <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                                Tidak Tersedia
                            </button>
                        @endif
                    </div>

                    {{-- Email --}}
                    <div class="bg-base-200 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.letter-bold-duotone" class="w-4 h-4 text-info" />
                                <span class="text-sm font-semibold">Email</span>
                            </div>
                            <span class="text-sm">{{ $this->hasCSEmail() ? config('sosmed.email') : 'Tidak tersedia' }}</span>
                        </div>
                        @if ($this->hasCSEmail())
                            <a href="mailto:{{ config('sosmed.email') }}" class="btn btn-info btn-sm w-full">
                                <x-icon name="solar.letter-bold-duotone" class="w-4 h-4" />
                                Kirim Email
                            </a>
                        @else
                            <button disabled class="btn btn-info btn-sm w-full opacity-50 cursor-not-allowed">
                                <x-icon name="solar.letter-bold-duotone" class="w-4 h-4" />
                                Tidak Tersedia
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. FAQ (Frequently Asked Questions) --}}
        <div id="faq" class="card bg-base-300 shadow scroll-mt-18">
            <div class="card-body p-4">
                <h3 class="font-bold text-base mb-3 flex items-center gap-2">
                    <x-icon name="solar.question-circle-bold-duotone" class="w-5 h-5 text-accent" />
                    Pertanyaan yang Sering Ditanyakan
                </h3>

                <div class="space-y-2">
                    {{-- FAQ: Cara pesan --}}
                    <x-collapse>
                        <x-slot:heading>
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.cart-large-2-bold-duotone" class="w-4 h-4 text-primary" />
                                <span class="text-sm font-semibold">Bagaimana cara pesan laundry?</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="text-sm space-y-2">
                                <p>1. Buka halaman <strong>Beranda</strong></p>
                                <p>2. Pilih layanan yang Anda butuhkan</p>
                                <p>3. Klik <strong>Pesan Sekarang</strong></p>
                                <p>4. Isi detail pesanan dan alamat</p>
                                <p>5. Pilih metode pembayaran (saat jemput/antar)</p>
                                <p>6. Klik <strong>Buat Pesanan</strong></p>
                                <p>7. Tim kami akan menghubungi Anda via WhatsApp</p>
                            </div>
                        </x-slot:content>
                    </x-collapse>

                    {{-- FAQ: Layanan --}}
                    <x-collapse>
                        <x-slot:heading>
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.washing-machine-bold-duotone" class="w-4 h-4 text-primary" />
                                <span class="text-sm font-semibold">Layanan apa saja yang tersedia?</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="text-sm space-y-2">
                                @foreach ($this->services as $service)
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-semibold">• {{ $service->name }}</span>
                                        <span class="text-xs text-primary font-semibold whitespace-nowrap">
                                            Rp. {{ number_format($service->price_per_kg, 0, ',', '.') }}/kg
                                        </span>
                                        <div class="text-right whitespace-nowrap">
                                            @if ($service->duration_days == 0)
                                                <span class="text-xs badge badge-warning">Same Day</span>
                                            @else
                                                <span class="text-xs badge badge-info">{{ $service->duration_days }} hari</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-slot:content>
                    </x-collapse>

                    {{-- FAQ: Pembayaran --}}
                    <x-collapse>
                        <x-slot:heading>
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.wallet-money-bold-duotone" class="w-4 h-4 text-primary" />
                                <span class="text-sm font-semibold">Bagaimana cara pembayaran?</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="text-sm space-y-2">
                                <p><strong>Bayar Saat Jemput:</strong></p>
                                <p>• Pembayaran saat kurir menjemput cucian</p>
                                <p>• Harga dihitung setelah cucian ditimbang</p>
                                <p>• Pembayaran: Tunai atau Transfer</p>
                                <div class="divider my-2"></div>
                                <p><strong>Bayar Saat Antar:</strong></p>
                                <p>• Pembayaran saat cucian dikembalikan</p>
                                <p>• Lebih fleksibel</p>
                                <p>• Sama-sama: Tunai atau Transfer</p>
                            </div>
                        </x-slot:content>
                    </x-collapse>

                    {{-- FAQ: Status pesanan --}}
                    <x-collapse>
                        <x-slot:heading>
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.clipboard-list-bold-duotone" class="w-4 h-4 text-primary" />
                                <span class="text-sm font-semibold">Apa arti setiap status pesanan?</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="text-sm space-y-2">
                                @foreach ($this->getWorkflowStatuses() as $status)
                                    <div class="flex items-center gap-2">
                                        <span class="badge {{ $status['badge'] }} badge-xs whitespace-nowrap">{{ $status['label'] }}</span>
                                        <span>{{ $status['description'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </x-slot:content>
                    </x-collapse>

                  </div>
            </div>
        </div>

        {{-- 3. Lokasi Kami --}}
        <div class="card bg-base-300 shadow">
            <div class="card-body p-4">
                <h3 class="font-bold text-base mb-3 flex items-center gap-2">
                    <x-icon name="solar.map-point-bold-duotone" class="w-5 h-5 text-primary" />
                    Lokasi Kami
                </h3>

                <div class="space-y-3">
                    {{-- POS Cards --}}
                    @foreach ($this->getPosList() as $pos)
                        <div class="bg-base-200 rounded-lg p-3">
                            <div class="flex items-start gap-3">
                                <div class="bg-primary/20 p-2 rounded-full">
                                    <x-icon name="solar.shop-2-bold-duotone" class="w-4 h-4 text-primary" />
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-1">
                                        <p class="font-semibold">{{ $pos->name }}</p>
                                        <span class="text-xs badge badge-info">POS</span>
                                    </div>
                                    <p class="text-xs text-base-content/70">{{ $pos->address }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Pagination --}}
                    <div class="flex justify-center gap-2 mt-4 mx-auto">
                        @if ($this->getCanLoadLessPos() && $this->getHasMorePos())
                            <x-button wire:click="loadMorePos" label="Lihat Banyak"
                                icon="solar.add-circle-bold-duotone" class="btn-accent btn-sm" />
                            <x-button wire:click="loadLessPos" label="Lebih Sedikit"
                                icon="solar.minus-circle-bold-duotone" class="btn-secondary btn-sm" />
                        @elseif ($this->getCanLoadLessPos() && !$this->getHasMorePos())
                            <x-button wire:click="loadLessPos" label="Tampilkan Lebih Sedikit"
                                icon="solar.minus-circle-bold-duotone" class="btn-secondary btn-sm btn-block" />
                        @else
                            <x-button wire:click="loadMorePos" label="Lihat Lokasi Lainnya"
                                icon="solar.add-circle-bold-duotone" class="btn-accent btn-sm btn-block"
                                :disabled="!$this->getHasMorePos()" />
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Informasi Aplikasi --}}
        <div class="card bg-base-300 shadow">
            <div class="card-body p-4">
                <h3 class="font-bold text-base mb-3 flex items-center gap-2">
                    <x-icon name="solar.info-circle-bold-duotone" class="w-5 h-5 text-info" />
                    Informasi Aplikasi
                </h3>

                <div class="bg-base-200 rounded-lg p-3 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Nama Aplikasi</span>
                        <span class="font-semibold">{{ config('app.name') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Versi Aplikasi</span>
                        <span class="badge badge-primary">v{{ config('app.mobile_version') ?? '1.0.0' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Environment</span>
                        <span class="badge {{ config('app.env') === 'production' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst(config('app.env')) }}
                        </span>
                    </div>
                </div>

                <div class="divider my-2"></div>

                <div class="text-center text-xs text-base-content/50">
                    <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
                    <p class="mt-1">Powered by <span class="font-semibold">MAIN GROUP</span></p>
                </div>
            </div>
        </div>
    </div>
</section>
