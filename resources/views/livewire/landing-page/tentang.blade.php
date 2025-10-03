<section id="tentang" class="min-h-dvh px-8 bg-base-200 flex flex-col justify-start pt-24">
    <div class="container mx-auto">
        <div class="text-left mb-16">
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 text-primary" data-aos="fade-right"
                data-aos-duration="600">
                Tentang <span class="text-accent">{{ config('app.name') }}</span>
            </h2>
            <p class="text-lg md:text-xl lg:text-2xl text-neutral/75 max-w-3xl leading-relaxed" data-aos="fade-right"
                data-aos-delay="100" data-aos-duration="600">
                Layanan laundry terpercaya dengan pengalaman bertahun-tahun melayani ribuan pelanggan setia
            </p>
            <div class="flex justify-start mt-4" data-aos="fade-right" data-aos-delay="200" data-aos-duration="600">
                <div class="w-24 h-1 bg-gradient-to-r from-primary to-accent rounded-full"></div>
            </div>
        </div>

        <!-- Konten Utama -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
            <!-- Left Column - Story & Values -->
            <div class="space-y-6">
                <!-- Story Card -->
                <div data-aos="fade-right" data-aos-delay="100" data-aos-duration="500">
                    <x-card
                        class="bg-base-300 transition-all duration-300 shadow-lg hover:shadow-primary hover:scale-102 border-2 border-primary relative">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="avatar avatar-placeholder">
                                <div class="bg-primary text-primary-content rounded-full w-16 h-16">
                                    <x-icon class="h-8 w-8" name="mdi.history" />
                                </div>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-primary mb-2">Cerita Kami</h3>
                                <p class="text-neutral/75 leading-relaxed">
                                    Dimulai dari tahun 2019, {{ config('app.name') }} hadir dengan komitmen
                                    memberikan
                                    layanan laundry terbaik. Dengan tim profesional dan peralatan modern, kami
                                    telah
                                    melayani lebih dari 1000+ pelanggan setia di seluruh Indonesia.
                                </p>
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Visi Card -->
                <div data-aos="fade-right" data-aos-delay="100" data-aos-duration="500">
                    <x-card
                        class="bg-base-300 transition-all duration-300 shadow-lg hover:shadow-accent hover:scale-102 border-2 border-accent relative">
                        <div class="flex items-start gap-4">
                            <div class="avatar avatar-placeholder">
                                <div class="bg-accent text-accent-content rounded-full w-16 h-16">
                                    <x-icon class="h-8 w-8" name="mdi.eye-outline" />
                                </div>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-accent mb-2">Visi Kami</h3>
                                <p class="text-neutral/75 leading-relaxed">
                                    Menjadi penyedia layanan laundry terdepan di Indonesia yang dikenal dengan
                                    kualitas
                                    premium, layanan cepat, dan kepuasan pelanggan yang maksimal.
                                </p>
                            </div>
                        </div>
                    </x-card>
                </div>

                <!-- Misi Card -->
                <div data-aos="fade-right" data-aos-delay="300" data-aos-duration="500">
                    <x-card
                        class="bg-base-300 transition-all duration-300 shadow-lg hover:shadow-primary hover:scale-102 border-2 border-primary relative">
                        <div class="flex items-start gap-4">
                            <div class="avatar avatar-placeholder">
                                <div class="bg-primary text-primary-content rounded-full w-16 h-16">
                                    <x-icon class="h-8 w-8" name="mdi.target" />
                                </div>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-primary mb-3">Misi Kami</h3>
                                <div class="space-y-2">
                                    <div class="flex items-start gap-2">
                                        <x-icon class="h-5 w-5 text-success mt-0.5" name="mdi.check-circle" />
                                        <span class="text-neutral/75 leading-relaxed">Memberikan layanan laundry
                                            berkualitas tinggi dengan harga terjangkau</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <x-icon class="h-5 w-5 text-success mt-0.5" name="mdi.check-circle" />
                                        <span class="text-neutral/75 leading-relaxed">Menggunakan teknologi dan
                                            metode pencucian ramah lingkungan</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <x-icon class="h-5 w-5 text-success mt-0.5" name="mdi.check-circle" />
                                        <span class="text-neutral/75 leading-relaxed">Memberikan pengalaman
                                            pelanggan terbaik dengan layanan cepat dan profesional</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>

            <!-- Right Column - Why Choose Us -->
            <div class="space-y-6">
                <!-- Header -->
                <div data-aos="fade-left" data-aos-delay="100" data-aos-duration="500">
                    <h3 class="text-3xl font-bold text-primary mb-6">Mengapa Memilih Kami?</h3>
                </div>

                <!-- Feature Items -->
                <div class="space-y-4">
                    <div data-aos="fade-left" data-aos-delay="150" data-aos-duration="500">
                        <x-card
                            class="bg-base-300 transition-all duration-300 shadow-md hover:shadow-primary hover:scale-102">
                            <div class="flex items-center gap-4">
                                <div class="avatar avatar-placeholder">
                                    <div class="bg-primary text-primary-content rounded-lg w-14 h-14">
                                        <x-icon class="h-7 w-7" name="mdi.shield-check-outline" />
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-primary">Terpercaya & Aman</h4>
                                    <p class="text-sm text-neutral/75">Pakaian Anda diasuransikan dan dijamin
                                        aman</p>
                                </div>
                            </div>
                        </x-card>
                    </div>

                    <div data-aos="fade-left" data-aos-delay="100" data-aos-duration="500">
                        <x-card
                            class="bg-base-300 transition-all duration-300 shadow-md hover:shadow-primary hover:scale-102">
                            <div class="flex items-center gap-4">
                                <div class="avatar avatar-placeholder">
                                    <div class="bg-primary text-primary-content rounded-lg w-14 h-14">
                                        <x-icon class="h-7 w-7" name="mdi.star-circle-outline" />
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-primary">Kualitas Premium</h4>
                                    <p class="text-sm text-neutral/75">Detergen berkualitas dan teknologi modern
                                    </p>
                                </div>
                            </div>
                        </x-card>
                    </div>

                    <div data-aos="fade-left" data-aos-delay="250" data-aos-duration="500">
                        <x-card
                            class="bg-base-300 transition-all duration-300 shadow-md hover:shadow-primary hover:scale-102">
                            <div class="flex items-center gap-4">
                                <div class="avatar avatar-placeholder">
                                    <div class="bg-primary text-primary-content rounded-lg w-14 h-14">
                                        <x-icon class="h-7 w-7" name="mdi.clock-fast" />
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-primary">Proses Cepat</h4>
                                    <p class="text-sm text-neutral/75">Layanan express 6 jam tersedia</p>
                                </div>
                            </div>
                        </x-card>
                    </div>

                    <div data-aos="fade-left" data-aos-delay="300" data-aos-duration="500">
                        <x-card
                            class="bg-base-300 transition-all duration-300 shadow-md hover:shadow-primary hover:scale-102">
                            <div class="flex items-center gap-4">
                                <div class="avatar avatar-placeholder">
                                    <div class="bg-primary text-primary-content rounded-lg w-14 h-14">
                                        <x-icon class="h-7 w-7" name="mdi.account-group-outline" />
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-primary">Tim Profesional</h4>
                                    <p class="text-sm text-neutral/75">Staff terlatih dan berpengalaman</p>
                                </div>
                            </div>
                        </x-card>
                    </div>

                    <div data-aos="fade-left" data-aos-delay="350" data-aos-duration="500">
                        <x-card
                            class="bg-base-300 transition-all duration-300 shadow-md hover:shadow-primary hover:scale-102">
                            <div class="flex items-center gap-4">
                                <div class="avatar avatar-placeholder">
                                    <div class="bg-primary text-primary-content rounded-lg w-14 h-14">
                                        <x-icon class="h-7 w-7" name="mdi.leaf-circle-outline" />
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-primary">Eco Friendly</h4>
                                    <p class="text-sm text-neutral/75">Ramah lingkungan dan sustainable</p>
                                </div>
                            </div>
                        </x-card>
                    </div>

                    <div data-aos="fade-left" data-aos-delay="200" data-aos-duration="500">
                        <x-card
                            class="bg-base-300 transition-all duration-300 shadow-md hover:shadow-primary hover:scale-102">
                            <div class="flex items-center gap-4">
                                <div class="avatar avatar-placeholder">
                                    <div class="bg-primary text-primary-content rounded-lg w-14 h-14">
                                        <x-icon class="h-7 w-7" name="mdi.cash-multiple" />
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-primary">Harga Terjangkau</h4>
                                    <p class="text-sm text-neutral/75">Mulai dari Rp 5.000/kg saja</p>
                                </div>
                            </div>
                        </x-card>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
