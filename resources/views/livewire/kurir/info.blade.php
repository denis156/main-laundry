<section class="bg-base-100 min-h-dvh w-full">
    {{-- Header --}}
    <x-header icon="solar.info-circle-bold-duotone" icon-classes="text-primary w-6 h-6" title="Informasi"
        subtitle="Pusat Informasi & Bantuan" separator />

    <div class="space-y-4">
        {{-- Quick Links --}}
        <div class="card bg-linear-to-r from-primary/10 to-accent/10 shadow-lg">
            <div class="card-body p-4">
                <h3 class="font-bold text-base mb-3 text-center">Butuh Bantuan Cepat?</h3>
                <div class="grid grid-cols-3 gap-2">
                    <a href="#kontak-cs" class="btn btn-primary btn-sm">
                        <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                        CS
                    </a>
                    <a href="#faq" class="btn btn-accent btn-sm">
                        <x-icon name="solar.question-circle-bold-duotone" class="w-4 h-4" />
                        FAQ
                    </a>
                    <a href="#pos" class="btn btn-secondary btn-sm">
                        <x-icon name="solar.map-point-bold-duotone" class="w-4 h-4" />
                        Pos
                    </a>
                </div>
            </div>
        </div>

        {{-- 1. Kontak Customer Service --}}
        <div id="kontak-cs" class="card bg-base-300 shadow-lg scroll-mt-18">
            <div class="card-body p-4">
                <h3 class="font-bold text-base mb-3 flex items-center gap-2">
                    <x-icon name="solar.chat-round-bold-duotone" class="w-5 h-5 text-primary" />
                    Kontak Customer Service
                </h3>

                <div class="space-y-3">
                    {{-- WhatsApp --}}
                    <div class="bg-base-200 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.phone-bold-duotone" class="w-4 h-4 text-success" />
                                <span class="text-sm font-semibold">WhatsApp</span>
                            </div>
                            <span class="text-sm">{{ config('sosmed.phone') }}</span>
                        </div>
                        <a href="{{ $this->getWhatsAppCSUrl() }}" target="_blank" class="btn btn-success btn-sm w-full">
                            <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                            Hubungi via WhatsApp
                        </a>
                    </div>

                    {{-- Email --}}
                    <div class="bg-base-200 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.letter-bold-duotone" class="w-4 h-4 text-info" />
                                <span class="text-sm font-semibold">Email</span>
                            </div>
                            <span class="text-sm">{{ config('sosmed.email') }}</span>
                        </div>
                        <a href="mailto:{{ config('sosmed.email') }}" class="btn btn-info btn-sm w-full">
                            <x-icon name="solar.letter-bold-duotone" class="w-4 h-4" />
                            Kirim Email
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. FAQ (Frequently Asked Questions) --}}
        <div id="faq" class="card bg-base-300 shadow-lg scroll-mt-18">
            <div class="card-body p-4">
                <h3 class="font-bold text-base mb-3 flex items-center gap-2">
                    <x-icon name="solar.question-circle-bold-duotone" class="w-5 h-5 text-accent" />
                    Pertanyaan yang Sering Ditanyakan
                </h3>

                <div class="space-y-2">
                    {{-- FAQ: Cara mengambil pesanan --}}
                    <x-collapse>
                        <x-slot:heading>
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.box-bold-duotone" class="w-4 h-4 text-primary" />
                                <span class="text-sm font-semibold">Bagaimana cara mengambil pesanan baru?</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="text-sm space-y-2">
                                <p>1. Buka menu <strong>Pesanan</strong></p>
                                <p>2. Cari pesanan dengan status <span class="badge badge-secondary badge-sm">Menunggu
                                        Konfirmasi</span></p>
                                <p>3. Klik tombol <strong>Ambil Pesanan</strong></p>
                                <p>4. Pesanan akan berubah status menjadi <span
                                        class="badge badge-info badge-sm">Terkonfirmasi</span></p>
                            </div>
                        </x-slot:content>
                    </x-collapse>

                    {{-- FAQ: Input berat --}}
                    <x-collapse>
                        <x-slot:heading>
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.scale-bold-duotone" class="w-4 h-4 text-primary" />
                                <span class="text-sm font-semibold">Bagaimana cara input berat cucian?</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="text-sm space-y-2">
                                <p>1. Pastikan pesanan sudah <span
                                        class="badge badge-info badge-sm">Terkonfirmasi</span></p>
                                <p>2. Input berat dalam kg (contoh: 8.5)</p>
                                <p>3. Total harga akan muncul otomatis</p>
                                <p>4. Jika bayar saat jemput, upload bukti pembayaran</p>
                                <p>5. Klik <strong>Dijemput</strong></p>
                            </div>
                        </x-slot:content>
                    </x-collapse>

                    {{-- FAQ: Upload bukti pembayaran --}}
                    <x-collapse>
                        <x-slot:heading>
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.camera-bold-duotone" class="w-4 h-4 text-primary" />
                                <span class="text-sm font-semibold">Kapan harus upload bukti pembayaran?</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="text-sm space-y-2">
                                <p><strong>Bayar Saat Jemput:</strong></p>
                                <p>- Upload saat pesanan akan dijemput (status Terkonfirmasi)</p>
                                <p>- Wajib upload sebelum klik "Dijemput"</p>
                                <div class="divider my-2"></div>
                                <p><strong>Bayar Saat Antar:</strong></p>
                                <p>- Upload saat pesanan akan diantar (status Mengantar)</p>
                                <p>- Wajib upload sebelum klik "Selesai"</p>
                                <div class="divider my-2"></div>
                                <p><strong>Tips:</strong> Pastikan foto bukti jelas dan terbaca!</p>
                            </div>
                        </x-slot:content>
                    </x-collapse>

                    {{-- FAQ: Gagal upload --}}
                    <x-collapse>
                        <x-slot:heading>
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.close-circle-bold-duotone" class="w-4 h-4 text-error" />
                                <span class="text-sm font-semibold">Bagaimana jika upload bukti pembayaran gagal?</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="text-sm space-y-2">
                                <p><strong>Penyebab umum:</strong></p>
                                <p>• Ukuran file terlalu besar (max 2MB)</p>
                                <p>• Koneksi internet lemah</p>
                                <p>• Format file tidak didukung (harus JPG/PNG)</p>
                                <div class="divider my-2"></div>
                                <p><strong>Solusi:</strong></p>
                                <p>1. Kompres foto terlebih dahulu</p>
                                <p>2. Coba koneksi WiFi yang stabil</p>
                                <p>3. Bisa upload nanti via menu <strong>Pembayaran > Detail Pembayaran</strong></p>
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
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-secondary badge-xs">Konfirmasi?</span>
                                    <span>Belum diambil kurir</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-info badge-xs">Terkonfirmasi</span>
                                    <span>Siap dijemput ke customer</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-warning badge-xs">Dijemput</span>
                                    <span>Dalam perjalanan ke pos</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-warning badge-xs">Di Pos</span>
                                    <span>Sudah sampai pos loading</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-primary badge-xs">Dicuci</span>
                                    <span>Proses pencucian</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-success badge-xs">Siap Antar</span>
                                    <span>Siap diantar ke customer</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-warning badge-xs">Mengantar</span>
                                    <span>Sedang diantar ke customer</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="badge badge-success badge-xs">Selesai</span>
                                    <span>Sudah diterima customer</span>
                                </div>
                            </div>
                        </x-slot:content>
                    </x-collapse>

                    {{-- FAQ: Customer tidak ada --}}
                    <x-collapse>
                        <x-slot:heading>
                            <div class="flex items-center gap-2">
                                <x-icon name="solar.user-cross-bold-duotone" class="w-4 h-4 text-warning" />
                                <span class="text-sm font-semibold">Apa yang dilakukan jika customer tidak ada?</span>
                            </div>
                        </x-slot:heading>
                        <x-slot:content>
                            <div class="text-sm space-y-2">
                                <p><strong>Langkah-langkah:</strong></p>
                                <p>1. Hubungi customer via WhatsApp terlebih dahulu</p>
                                <p>2. Tunggu maksimal 15 menit</p>
                                <p>3. Jika tidak ada respon, hubungi CS untuk koordinasi</p>
                                <p>4. Dokumentasikan dengan foto lokasi</p>
                                <p>5. Jangan tinggalkan cucian tanpa konfirmasi</p>
                            </div>
                        </x-slot:content>
                    </x-collapse>
                </div>
            </div>
        </div>

        {{-- 3. Informasi Pos --}}
        <div id="pos" class="card bg-base-300 shadow-lg">
            <div class="card-body p-4">
                <h3 class="font-bold text-base mb-3 flex items-center gap-2">
                    <x-icon name="solar.map-point-bold-duotone" class="w-5 h-5 text-secondary" />
                    Informasi Pos Saya
                </h3>

                @if ($this->assignedPos)
                    <div class="bg-base-200 rounded-lg p-3">
                        <div class="mb-3">
                            <h4 class="font-bold text-lg text-primary">{{ $this->assignedPos->name }}</h4>
                            @if ($this->assignedPos->address)
                                <p class="text-sm text-base-content/70 mt-2">
                                    <x-icon name="solar.map-point-linear" class="w-4 h-4 inline" />
                                    {{ $this->assignedPos->address }}
                                </p>
                            @endif
                        </div>

                        @if ($this->assignedPos->pic_name || $this->assignedPos->phone)
                            <div class="divider my-2"></div>
                            <div>
                                <p class="text-xs font-semibold text-base-content/60 mb-2">Penanggung Jawab Pos</p>
                                <div class="space-y-2 mb-3">
                                    @if ($this->assignedPos->pic_name)
                                        <div class="flex items-center gap-2">
                                            <x-icon name="solar.user-bold-duotone" class="w-4 h-4 text-primary" />
                                            <span
                                                class="text-sm font-semibold">{{ $this->assignedPos->pic_name }}</span>
                                        </div>
                                    @endif
                                    @if ($this->assignedPos->phone)
                                        <div class="flex items-center gap-2">
                                            <x-icon name="solar.phone-bold-duotone" class="w-4 h-4 text-success" />
                                            <span class="text-sm">{{ $this->assignedPos->phone }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if ($this->assignedPos->phone)
                                    <a href="{{ $this->getWhatsAppPosUrl() }}" target="_blank"
                                        class="btn btn-success btn-sm w-full">
                                        <x-icon name="solar.chat-round-bold-duotone" class="w-4 h-4" />
                                        Hubungi Penanggung Jawab
                                    </a>
                                @endif
                            </div>
                        @endif

                        @if ($this->assignedPos->area && count($this->assignedPos->area) > 0)
                            <div class="divider my-2"></div>
                            <div>
                                <p class="text-xs font-semibold text-base-content/60 mb-2">Area Layanan</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($this->assignedPos->area as $kelurahan)
                                        <span class="badge badge-outline badge-sm">{{ $kelurahan }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <x-icon name="solar.inbox-bold-duotone" class="w-12 h-12 text-base-content/20 mx-auto mb-2" />
                        <p class="text-sm text-base-content/60">Anda belum ditugaskan ke pos manapun</p>
                        <p class="text-xs text-base-content/50 mt-1">Hubungi admin untuk informasi lebih lanjut</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- 4. Informasi Aplikasi --}}
        <div class="card bg-base-300 shadow-lg">
            <div class="card-body p-4">
                <h3 class="font-bold text-base mb-3 flex items-center gap-2">
                    <x-icon name="solar.info-circle-bold-duotone" class="w-5 h-5 text-info" />
                    Informasi Aplikasi
                </h3>

                <div class="bg-base-200 rounded-lg p-3 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Nama Aplikasi</span>
                        <span class="font-semibold">Kurir {{ config('app.name') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Versi Kurir</span>
                        <span class="badge badge-primary">v{{ config('app.kurir_version') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/70">Environment</span>
                        <span
                            class="badge {{ config('app.env') === 'production' ? 'badge-success' : 'badge-warning' }}">
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
