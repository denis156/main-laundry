<section id="faq"
    class="min-h-dvh p-8 scroll-mt-12 md:scroll-mt-14 lg:scroll-mt-16 relative overflow-hidden bg-base-300">
    <!-- Background Decoration -->
    <x-landing-page.bg-decoration />

    <!-- Content Container with Padding -->
    <div class="p-8 relative z-10">
        <div class="container mx-auto pb-20">
            <!-- Section Header -->
            <div class="text-center mt-12 mb-14">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-accent">
                    Pertanyaan yang Sering Ditanyakan
                </h2>
                <p class="text-md md:text-lg lg:text-xl text-base-content/80 max-w-2xl mx-auto">
                    Cari jawaban cepat untuk pertanyaan umum tentang layanan kami.
                </p>
            </div>

            <!-- FAQ Items - Landing Page Specific (No Card Wrapper) -->
            <div class="space-y-4">
                {{-- FAQ: Cara pesan --}}
                <x-collapse class="bg-base-100 border-2">
                    <x-slot:heading>
                        <div class="flex items-center gap-2">
                            <x-icon name="solar.cart-large-2-bold-duotone" class="w-4 h-4 text-primary" />
                            <span class="text-sm font-semibold">Bagaimana cara pesan?</span>
                        </div>
                    </x-slot:heading>
                    <x-slot:content>
                        <div class="text-sm space-y-2">
                            <p class="text-error"><strong>Sangat mudah!</strong></p>
                            <p>Scroll ke bawah, pilih layanan, klik "Pesan Sekarang", isi data, selesai!</p>
                            <p class="text-xs text-base-content/70">Atau masuk ke aplikasi kami untuk fitur lengkap.</p>
                        </div>
                    </x-slot:content>
                </x-collapse>

                {{-- FAQ: Gratis Antar Jemput --}}
                <x-collapse class="bg-base-100 border-2">
                    <x-slot:heading>
                        <div class="flex items-center gap-2">
                            <x-icon name="solar.delivery-bold-duotone" class="w-4 h-4 text-primary" />
                            <span class="text-sm font-semibold">Gratis antar jemput?</span>
                        </div>
                    </x-slot:heading>
                    <x-slot:content>
                        <div class="text-sm space-y-2">
                            <p class="text-error"><strong>100% GRATIS!</strong></p>
                            <p>Tidak ada biaya tersembunyi, tidak ada minimum order. 1kg pun kami layani!</p>
                            <p class="text-xs text-base-content/70">Area layanan: Kota Anda dan sekitarnya.</p>
                        </div>
                    </x-slot:content>
                </x-collapse>

                {{-- FAQ: Pembayaran --}}
                <x-collapse class="bg-base-100 border-2">
                    <x-slot:heading>
                        <div class="flex items-center gap-2">
                            <x-icon name="solar.wallet-money-bold-duotone" class="w-4 h-4 text-primary" />
                            <span class="text-sm font-semibold">Bisa bayar bagaimana?</span>
                        </div>
                    </x-slot:heading>
                    <x-slot:content>
                        <div class="text-sm space-y-2">
                            <p><strong class="text-warning">Bayar Saat Jemput</strong> - Saat kurir datang</p>
                            <p><strong class="text-accent">Bayar Saat Antar</strong> - Saat cucian selesai</p>
                            <p class="text-xs text-base-content/70">Menerima tunai dan transfer.</p>
                        </div>
                    </x-slot:content>
                </x-collapse>

                {{-- FAQ: Proses --}}
                <x-collapse class="bg-base-100 border-2">
                    <x-slot:heading>
                        <div class="flex items-center gap-2">
                            <x-icon name="solar.clock-circle-bold-duotone" class="w-4 h-4 text-primary" />
                            <span class="text-sm font-semibold">Berapa lama selesainya?</span>
                        </div>
                    </x-slot:heading>
                    <x-slot:content>
                        <div class="text-sm space-y-2">
                            <p class="text-error"><strong>1 Hari Kerja!</strong></p>
                            <p>Pagi dijemput, sore besok sudah selesai. Cepat dan berkualitas!</p>
                            <p class="text-xs text-base-content/70">Untuk semua layanan yang ada</p>
                        </div>
                    </x-slot:content>
                </x-collapse>

                {{-- FAQ: Apakah Aman? --}}
                <x-collapse class="bg-base-100 border-2">
                    <x-slot:heading>
                        <div class="flex items-center gap-2">
                            <x-icon name="solar.shield-check-bold-duotone" class="w-4 h-4 text-primary" />
                            <span class="text-sm font-semibold">Apakah cucian aman?</span>
                        </div>
                    </x-slot:heading>
                    <x-slot:content>
                        <div class="text-sm space-y-2">
                            <p class="text-error"><strong>100% AMAN & BERGARANSI!</strong></p>
                            <p>Menggunakan deterjen berkualitas dan proses pengerjaan yang higienis.</p>
                            <p class="text-xs text-base-content/70">Pakaian anda aman bersama kami.</p>
                        </div>
                    </x-slot:content>
                </x-collapse>

                {{-- FAQ: Cek Status Pesanan --}}
                <x-collapse class="bg-base-100 border-2">
                    <x-slot:heading>
                        <div class="flex items-center gap-2">
                            <x-icon name="solar.clipboard-list-bold-duotone" class="w-4 h-4 text-primary" />
                            <span class="text-sm font-semibold">Bagaimana cek pesanan?</span>
                        </div>
                    </x-slot:heading>
                    <x-slot:content>
                        <div class="text-sm space-y-2">
                            <p class="text-error"><strong>Masuk ke Aplikasi Kami!</strong></p>
                            <p>1. Buka aplikasi kami</p>
                            <p>2. Masuk dengan nomor telepon atau Google</p>
                            <p>3. Lihat status pesanan langsung di menu utama</p>
                            <p class="text-xs text-base-content/70">Notifikasi WhatsApp juga dikirim untuk setiap
                                update!</p>
                        </div>
                    </x-slot:content>
                </x-collapse>
            </div>

            <!-- CTA to Pesan Section -->
            <div class="text-center mt-12">
                <p class="text-lg text-base-content/80 mb-4">Masih ada pertanyaan?</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <x-button label="Hubungi CS" icon="solar.chat-round-bold-duotone"
                        link="{{ config('sosmed.phone') ? 'https://wa.me/' . str_replace(['+', '-', ' ', '(', ')'], '', config('sosmed.phone')) : '#' }}"
                        class="btn-success btn-lg" external />
                    <x-button label="Pesan Sekarang" icon="solar.cart-large-2-bold-duotone" class="btn-primary btn-lg"
                        link="#pesan" no-wire-navigate />
                </div>
            </div>
        </div>
    </div>
</section>
