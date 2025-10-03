<section id="harga" class="min-h-dvh px-8 flex flex-col justify-start pt-24">
    <div class="container mx-auto">
        <div class="text-left mb-16">
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 text-primary" data-aos="fade-right"
                data-aos-duration="600">
                Paket <span class="text-accent">Harga</span> Terbaik
            </h2>
            <p class="text-lg md:text-xl lg:text-2xl text-neutral/75 max-w-3xl leading-relaxed" data-aos="fade-right"
                data-aos-delay="100" data-aos-duration="600">
                Pilih paket yang sesuai dengan kebutuhan dan budget Anda. Harga transparan tanpa biaya
                tersembunyi
            </p>
            <div class="flex justify-start mt-4" data-aos="fade-right" data-aos-delay="200" data-aos-duration="600">
                <div class="w-24 h-1 bg-gradient-to-r from-primary to-accent rounded-full"></div>
            </div>
        </div>
        <!-- Grid Harga -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <!-- Cuci Kering -->
            <div data-aos="fade-right" data-aos-delay="100" data-aos-duration="500">
                <x-card
                    class="bg-base-300 transition-all duration-300 shadow-lg hover:shadow-primary hover:scale-102 border-2 border-primary relative">
                    <div class="text-center">
                        <div class="avatar avatar-placeholder mb-4">
                            <div class="bg-primary text-primary-content rounded-full w-16 h-16">
                                <x-icon class="h-8 w-8" name="mdi.washing-machine" />
                            </div>
                        </div>
                        <h4 class="text-2xl font-bold text-primary mb-3">Cuci Kering</h4>
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-primary">Rp 5.000</span>
                            <span class="text-base-content/60">/kg</span>
                        </div>
                        <p class="text-neutral/68 mb-6 leading-relaxed">
                            Layanan cuci dan kering lengkap dengan deterjen premium untuk hasil optimal
                        </p>
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Cuci + Kering Lengkap</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Deterjen Premium</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Pewangi Berkualitas</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Proses 24 Jam</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-secondary" name="mdi.plus-circle-outline" />
                                <span class="text-sm">3 Lainnya</span>
                            </div>
                        </div>
                        <x-slot:actions separator>
                            <x-button label="Detail Layanan" class="btn-secondary btn-outline" link="#"
                                no-wire-navigate />
                            <x-button label="Pesan Sekarang" class="btn-primary" link="#reservasi" no-wire-navigate />
                        </x-slot:actions>
                    </div>
                </x-card>
            </div>

            <!-- Setrika Saja -->
            <div data-aos="fade-down" data-aos-delay="100" data-aos-duration="500">
                <x-card
                    class="bg-base-300 transition-all duration-300 shadow-lg hover:shadow-primary hover:scale-102 border-2 border-primary relative">
                    <div class="text-center">
                        <div class="avatar avatar-placeholder mb-4">
                            <div class="bg-primary text-primary-content rounded-full w-16 h-16">
                                <x-icon class="h-8 w-8" name="mdi.iron-outline" />
                            </div>
                        </div>
                        <h4 class="text-2xl font-bold text-primary mb-3">Setrika Saja</h4>
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-primary">Rp 2.000</span>
                            <span class="text-base-content/60">/pcs</span>
                        </div>
                        <p class="text-neutral/68 mb-6 leading-relaxed">
                            Khusus layanan setrika untuk pakaian yang sudah bersih dengan hasil rapi
                        </p>
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Setrika Profesional</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Lipatan Rapi</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Proses Cepat</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Harga Terjangkau</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-secondary" name="mdi.plus-circle-outline" />
                                <span class="text-sm">3 Lainnya</span>
                            </div>
                        </div>
                        <x-slot:actions separator>
                            <x-button label="Detail Layanan" class="btn-secondary btn-outline" link="#"
                                no-wire-navigate />
                            <x-button label="Pesan Sekarang" class="btn-primary" link="#reservasi" no-wire-navigate />
                        </x-slot:actions>
                    </div>
                </x-card>
            </div>

            <!-- Dry Cleaning -->
            <div data-aos="fade-left" data-aos-delay="100" data-aos-duration="500">
                <x-card
                    class="bg-base-300 transition-all duration-300 shadow-lg hover:shadow-accent hover:scale-102 border-2 border-accent relative">
                    <div class="text-center">
                        <div class="avatar avatar-placeholder mb-4">
                            <div class="bg-accent text-accent-content rounded-full w-16 h-16">
                                <x-icon class="h-8 w-8" name="mdi.tie" />
                            </div>
                        </div>
                        <h4 class="text-2xl font-bold text-accent mb-3">Dry Cleaning</h4>
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-accent">Rp 15.000</span>
                            <span class="text-base-content/60">/pcs</span>
                        </div>
                        <p class="text-neutral/68 mb-6 leading-relaxed">
                            Layanan khusus untuk pakaian premium dan bahan-bahan yang memerlukan perawatan
                            khusus
                        </p>
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Perawatan Premium</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Bahan Aman</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Hasil Premium</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Garansi Kualitas</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-secondary" name="mdi.plus-circle-outline" />
                                <span class="text-sm">3 Lainnya</span>
                            </div>
                        </div>
                        <x-slot:actions separator>
                            <x-button label="Detail Layanan" class="btn-secondary btn-outline" link="#"
                                no-wire-navigate />
                            <x-button label="Pesan Sekarang" class="btn-accent" link="#reservasi" no-wire-navigate />
                        </x-slot:actions>
                    </div>
                </x-card>
            </div>

            <!-- Cuci Sepatu -->
            <div data-aos="fade-right" data-aos-delay="100" data-aos-duration="500">
                <x-card
                    class="bg-base-300 transition-all duration-300 shadow-lg hover:shadow-primary hover:scale-102 border-2 border-primary relative">
                    <div class="text-center">
                        <div class="avatar avatar-placeholder mb-4">
                            <div class="bg-primary text-primary-content rounded-full w-16 h-16">
                                <x-icon class="h-8 w-8" name="mdi.shoe-sneaker" />
                            </div>
                        </div>
                        <h4 class="text-2xl font-bold text-primary mb-3">Cuci Sepatu</h4>
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-primary">Rp 10.000</span>
                            <span class="text-base-content/60">/pasang</span>
                        </div>
                        <p class="text-neutral/68 mb-6 leading-relaxed">
                            Layanan pembersihan sepatu dengan peralatan khusus untuk berbagai jenis material
                        </p>
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Deep Cleaning</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Perawatan Sol</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Anti Bakteri</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Semua Jenis Sepatu</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-secondary" name="mdi.plus-circle-outline" />
                                <span class="text-sm">3 Lainnya</span>
                            </div>
                        </div>
                        <x-slot:actions separator>
                            <x-button label="Detail Layanan" class="btn-secondary btn-outline" link="#"
                                no-wire-navigate />
                            <x-button label="Pesan Sekarang" class="btn-primary" link="#reservasi"
                                no-wire-navigate />
                        </x-slot:actions>
                    </div>
                </x-card>
            </div>

            <!-- Express 6 Jam -->
            <div data-aos="fade-up" data-aos-delay="100" data-aos-duration="500">
                <x-card
                    class="bg-base-300 transition-all duration-300 shadow-lg hover:shadow-primary hover:scale-102 border-2 border-primary relative">
                    <div class="text-center">
                        <div class="avatar avatar-placeholder mb-4">
                            <div class="bg-primary text-primary-content rounded-full w-16 h-16">
                                <x-icon class="h-8 w-8" name="mdi.lightning-bolt-outline" />
                            </div>
                        </div>
                        <h4 class="text-2xl font-bold text-primary mb-3">Express 6 Jam</h4>
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-primary">Rp 12.000</span>
                            <span class="text-base-content/60">/kg</span>
                        </div>
                        <p class="text-neutral/68 mb-6 leading-relaxed">
                            Layanan kilat untuk kebutuhan mendadak dengan hasil maksimal dalam waktu singkat
                        </p>
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Cuci + Kering + Setrika</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Proses 6 Jam</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Prioritas Utama</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Hasil Premium</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-secondary" name="mdi.plus-circle-outline" />
                                <span class="text-sm">3 Lainnya</span>
                            </div>
                        </div>
                        <x-slot:actions separator>
                            <x-button label="Detail Layanan" class="btn-secondary btn-outline" link="#"
                                no-wire-navigate />
                            <x-button label="Pesan Sekarang" class="btn-primary" link="#reservasi"
                                no-wire-navigate />
                        </x-slot:actions>
                    </div>
                </x-card>
            </div>

            <!-- Antar Jemput -->
            <div data-aos="fade-left" data-aos-delay="100" data-aos-duration="500">
                <x-card
                    class="bg-base-300 transition-all duration-300 shadow-lg hover:shadow-primary hover:scale-102 border-2 border-primary relative">
                    <div class="text-center">
                        <div class="avatar avatar-placeholder mb-4">
                            <div class="bg-primary text-primary-content rounded-full w-16 h-16">
                                <x-icon class="h-8 w-8" name="mdi.truck-delivery-outline" />
                            </div>
                        </div>
                        <h4 class="text-2xl font-bold text-primary mb-3">Antar Jemput</h4>
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-success">GRATIS</span>
                            <span class="text-base-content/60">*syarat berlaku</span>
                        </div>
                        <p class="text-neutral/68 mb-6 leading-relaxed">
                            Layanan antar jemput gratis untuk area tertentu, praktis tanpa perlu keluar rumah
                        </p>
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Antar Jemput Gratis</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Area Coverage Luas</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Jadwal Fleksibel</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-success" name="mdi.check-circle-outline" />
                                <span class="text-sm">Driver Terpercaya</span>
                            </div>
                            <div class="flex items-center justify-start gap-2">
                                <x-icon class="h-4 w-4 text-secondary" name="mdi.plus-circle-outline" />
                                <span class="text-sm">3 Lainnya</span>
                            </div>
                        </div>
                        <x-slot:actions separator>
                            <x-button label="Detail Layanan" class="btn-secondary btn-outline" link="#"
                                no-wire-navigate />
                            <x-button label="Pesan Sekarang" class="btn-primary" link="#reservasi"
                                no-wire-navigate />
                        </x-slot:actions>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</section>
