<section id="lokasi" class="min-h-dvh px-8 bg-base-200 flex flex-col justify-start pt-24">
    <div class="container mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 text-primary" data-aos="fade-up"
                data-aos-duration="600">
                <span class="text-accent">Lokasi</span> & Coverage
            </h2>
            <p class="text-lg md:text-xl lg:text-2xl text-neutral/75 max-w-3xl mx-auto leading-relaxed" data-aos="fade-up"
                data-aos-delay="100" data-aos-duration="600">
                Jangkauan layanan luas dengan lokasi strategis untuk kemudahan akses Anda
            </p>
            <div class="flex justify-center mt-4" data-aos="fade-up" data-aos-delay="200" data-aos-duration="600">
                <div class="w-24 h-1 bg-gradient-to-r from-primary to-accent rounded-full"></div>
            </div>
        </div>

        <!-- Grid Layout untuk Lokasi & Map -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Left Column - Informasi Lokasi -->
            <div class="space-y-6" data-aos="fade-right" data-aos-duration="600">
                <!-- Alamat -->
                <x-card class="bg-base-300 shadow-lg hover:shadow-primary hover:scale-105 transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <x-icon name="mdi.map-marker" class="h-8 w-8 text-primary" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-primary mb-2">Alamat Lengkap</h3>
                            <p class="text-neutral/75 leading-relaxed">
                                Asrama Melati<br>
                                Kendari, Sulawesi Tenggara<br>
                                Indonesia
                            </p>
                        </div>
                    </div>
                </x-card>

                <!-- Jam Operasional -->
                <x-card class="bg-base-300 shadow-lg hover:shadow-primary hover:scale-105 transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <x-icon name="mdi.clock-outline" class="h-8 w-8 text-primary" />
                        </div>
                        <div class="w-full">
                            <h3 class="text-xl font-bold text-primary mb-3">Jam Operasional</h3>
                            <div class="space-y-2 text-neutral/75">
                                <div class="flex justify-between">
                                    <span>Senin - Jumat</span>
                                    <span class="font-semibold">08:00 - 20:00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Sabtu - Minggu</span>
                                    <span class="font-semibold">09:00 - 18:00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>

                <!-- Kontak -->
                <x-card class="bg-base-300 shadow-lg hover:shadow-primary hover:scale-105 transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <x-icon name="mdi.phone" class="h-8 w-8 text-primary" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-primary mb-3">Hubungi Kami</h3>
                            <div class="space-y-2 text-neutral/75">
                                <div class="flex items-center gap-2">
                                    <x-icon name="mdi.whatsapp" class="h-5 w-5 text-success" />
                                    <span>+62 812-3456-7890</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-icon name="mdi.email" class="h-5 w-5 text-info" />
                                    <span>info@mainlaundry.com</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>

                <!-- Coverage Area -->
                <x-card class="bg-base-300 shadow-lg hover:shadow-primary hover:scale-105 transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <x-icon name="mdi.map-marker-radius" class="h-8 w-8 text-primary" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-primary mb-3">Area Layanan Antar Jemput</h3>
                            <p class="text-neutral/75 leading-relaxed mb-3">
                                Gratis antar jemput dalam radius 5km dari lokasi kami
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <div class="badge badge-primary">Kendari Barat</div>
                                <div class="badge badge-primary">Kendari Kota</div>
                                <div class="badge badge-primary">Baruga</div>
                                <div class="badge badge-primary">Mandonga</div>
                                <div class="badge badge-primary">Poasia</div>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Right Column - Google Maps -->
            <div data-aos="fade-left" data-aos-duration="600">
                <x-card class="bg-base-300 shadow-lg shadow-accent h-full overflow-hidden p-0">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3980.1383363494065!2d122.51491817560236!3d-3.9919604959817736!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d988da4a3243e39%3A0x8dd4558c7a5a113d!2sAsrama%20Melati!5e0!3m2!1sid!2sid!4v1759489417536!5m2!1sid!2sid"
                        class="w-full h-full min-h-[400px] lg:min-h-[600px]" style="border:0;" allowfullscreen=""
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </x-card>
            </div>
        </div>
    </div>
</section>
