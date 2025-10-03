<section id="beranda"
    class="scroll-mt-18 min-h-dvh px-8 bg-gradient-to-b from-base-100 via-primary/46 to-primary/48 flex items-center">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            {{-- Grid 1 --}}
            <div class="col-span-1">
                <div class="text-center lg:text-left">
                    <div data-aos="fade-right" data-aos-duration="600">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-bold text-primary mb-6">
                            Layanan Laundry <span class="text-accent">Terpercaya</span>
                        </h1>
                    </div>
                    <div data-aos="fade-right" data-aos-delay="100" data-aos-duration="600">
                        <p class="text-lg sm:text-xl lg:text-2xl text-neutral/48 mb-8 leading-relaxed">
                            Kami memberikan pelayanan laundry terbaik dengan kualitas premium dan harga terjangkau
                            untuk semua kebutuhan Anda
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start" data-aos="fade-right" data-aos-delay="200" data-aos-duration="600">
                        <div>
                            <x-button class="btn-accent btn-lg shadow-lg shadow-accent transition-all duration-300 hover:scale-110" link="/#layanan" no-wire-navigate>
                                <x-icon class="h-5 w-5" name="mdi.arrow-down-bold-outline" />
                                Mulai Sekarang
                            </x-button>
                        </div>
                        <div>
                            <x-button class="btn-secondary btn-lg shadow-lg shadow-secondary transition-all duration-300 hover:scale-110" link="/#reservasi" no-wire-navigate>
                                <x-icon class="h-5 w-5" name="mdi.receipt-text-send-outline" />
                                Reservasi
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grid 2 --}}
            <div class="col-span-1">
                <div class="text-center space-y-6">
                    {{-- Feature Badges --}}
                    <div class="flex flex-row gap-3 justify-center flex-wrap" data-aos="fade-left" data-aos-delay="100" data-aos-duration="600">
                        <div>
                            <x-badge value="24/7 Service" class="badge-success badge-lg font-medium transition-all duration-300 hover:scale-105" />
                        </div>
                        <div>
                            <x-badge value="Eco Friendly" class="badge-info badge-lg font-medium transition-all duration-300 hover:scale-105" />
                        </div>
                    </div>

                    {{-- Customer Review Card --}}
                    <div data-aos="fade-left" data-aos-delay="300" data-aos-duration="600">
                        <x-card class="max-w-md mx-auto bg-base-100/95 shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-primary">
                            <div class="flex items-center justify-center gap-2 mb-3">
                                <x-rating value="0" class="bg-warning" total="5" />
                                <span class="text-sm font-bold text-warning">5.0</span>
                            </div>
                            <p class="text-sm text-secondary italic text-center leading-relaxed mb-3 font-medium">
                                "Pelayanan sangat memuaskan! Pakaian bersih dan wangi. Recommended!"</p>
                            <div class="flex items-center justify-center gap-3">
                                <div class="avatar">
                                    <div class="w-8 rounded-full bg-primary">
                                        <div class="w-full h-full flex items-center justify-center">
                                            <span class="text-sm text-primary-content font-bold">SM</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-primary">Sarah M.</span>
                            </div>
                        </x-card>
                    </div>

                    {{-- Stats Card --}}
                    <div data-aos="fade-left" data-aos-delay="500" data-aos-duration="600">
                        <div class="stats stats-vertical lg:stats-horizontal shadow-lg bg-base-100/95 w-full mb-4 transition-all duration-300 hover:scale-105 hover:shadow-primary">
                            <div class="stat py-4 text-center">
                                <div class="stat-title text-sm font-medium">Pelanggan</div>
                                <div class="stat-value text-2xl text-success font-bold">1000+</div>
                                <div class="stat-desc text-sm font-medium">Pelanggan Puas</div>
                            </div>

                            <div class="stat py-4 text-center">
                                <div class="stat-title text-sm font-medium">Rating</div>
                                <div class="stat-value text-2xl text-info font-bold">4.9</div>
                                <div class="stat-desc text-sm font-medium">Dari 1000 Review</div>
                            </div>

                            <div class="stat py-4 text-center">
                                <div class="stat-title text-sm font-medium">Pengalaman</div>
                                <div class="stat-value text-2xl text-warning font-bold">5+</div>
                                <div class="stat-desc text-sm font-medium">Tahun Melayani</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
