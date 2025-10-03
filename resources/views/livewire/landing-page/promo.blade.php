<section id="promo" class="min-h-dvh px-8 bg-base-200 flex flex-col justify-start pt-24">
    <div class="container mx-auto">
        <div class="text-right mb-16">
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 text-primary" data-aos="fade-left"
                data-aos-duration="600">
                <span class="text-accent">Promo</span> & Diskon Spesial
            </h2>
            <p class="text-lg md:text-xl lg:text-2xl text-neutral/75 max-w-3xl ml-auto leading-relaxed" data-aos="fade-left"
                data-aos-delay="100" data-aos-duration="600">
                Dapatkan penawaran menarik dan diskon eksklusif untuk layanan terbaik kami
            </p>
            <div class="flex justify-end mt-4" data-aos="fade-left" data-aos-delay="200" data-aos-duration="600">
                <div class="w-24 h-1 bg-gradient-to-r from-primary to-accent rounded-full"></div>
            </div>
        </div>
        <!-- Grid Layout untuk Promo -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-stretch mb-12">
            <!-- Left Column - Membership & Mini Promos -->
            <div class="col-span-1 space-y-6">
                <!-- Membership Section -->
                <div data-aos="fade-right" data-aos-delay="100" data-aos-duration="500">
                    <x-card class="bg-base-300 border border-primary shadow-md shadow-primary relative">
                        <div class="flex flex-col lg:grid lg:grid-cols-2 lg:grid-rows-1 gap-2">
                            <x-stat title="Silver Member" description="Diskon 10% + Free Pickup 2x" value="Rp 50k"
                                icon="mdi.medal"
                                class="bg-secondary/48 transition-all duration-300 shadow-lg hover:shadow-secondary hover:scale-102 border-2 border-secondary relative z-10"
                                color="text-secondary-content" />
                            <x-stat title="Gold Member" description="Diskon 15% + Priority Service" value="Rp 100k"
                                icon="mdi.crown"
                                class="bg-warning/48 transition-all duration-300 shadow-lg hover:shadow-warning hover:scale-102 border-2 border-warning relative z-10"
                                color="text-warning-content" />
                            <x-stat title="Platinum Member" description="Diskon 20% + Unlimited Pickup" value="Rp 200k"
                                icon="mdi.diamond-stone"
                                class="bg-neutral/48 transition-all duration-300 shadow-lg hover:shadow-neutral hover:scale-102 border-2 border-neutral relative z-10"
                                color="text-neutral-content" />
                            <x-stat title="VIP Member" description="Diskon 25% + Personal Assistant" value="Rp 500k"
                                icon="mdi.star-circle"
                                class="bg-success/48 transition-all duration-300 shadow-lg hover:shadow-success hover:scale-102 border-2 border-success relative z-10"
                                color="text-success-content" />
                        </div>
                    </x-card>
                </div>

                <!-- Quick Promos Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" data-aos="fade-right" data-aos-delay="100" data-aos-duration="500">
                    <!-- Mini promo card 1 -->
                    <x-card
                        class="bg-gradient-to-br from-info/10 via-info/20 to-info/30 border border-info relative transition-all duration-300 shadow-lg hover:shadow-info hover:scale-105">
                        <x-icon class="h-8 w-8 text-info mb-3" name="mdi.account-plus-outline" />
                        <div class="font-bold text-info mb-1">First Timer</div>
                        <div class="text-sm text-neutral/75">Gratis antar jemput untuk pelanggan baru</div>
                    </x-card>
                    <!-- Mini promo card 2 -->
                    <x-card
                        class="bg-gradient-to-br from-accent/10 via-accent/20 to-accent/30 border border-accent relative transition-all duration-300 shadow-lg hover:shadow-accent hover:scale-105">
                        <x-icon class="h-8 w-8 text-accent mb-3" name="mdi.package-variant" />
                        <div class="font-bold text-accent mb-1">Bundle Deal</div>
                        <div class="text-sm text-neutral/75">Cuci + Setrika mulai 15rb/kg</div>
                    </x-card>
                </div>

                <!-- Bottom Section -->
                <div data-aos="fade-right" data-aos-delay="300" data-aos-duration="500">
                    <x-card
                        class="bg-gradient-to-br from-error/10 via-error/20 to-error/30 border border-error relative shadow-md shadow-error"
                        body-class="text-center">
                        <div class="text-sm text-error font-semibold mb-2">Promo bulanan berakhir dalam:</div>
                        <div class="grid grid-flow-col gap-2 sm:gap-3 text-center auto-cols-max justify-center">
                            <div class="bg-error rounded-box text-error-content flex flex-col p-2">
                                <span class="countdown font-mono text-3xl sm:text-4xl lg:text-5xl">
                                    <span style="--value:{{ $countdown['days'] }};" aria-live="polite"
                                        aria-label="{{ $countdown['days'] }}">{{ $countdown['days'] }}</span>
                                </span>
                                <span class="text-sm">hari</span>
                            </div>
                            <div class="bg-error rounded-box text-error-content flex flex-col p-2">
                                <span class="countdown font-mono text-3xl sm:text-4xl lg:text-5xl">
                                    <span style="--value:{{ $countdown['hours'] }};" aria-live="polite"
                                        aria-label="{{ $countdown['hours'] }}">{{ $countdown['hours'] }}</span>
                                </span>
                                <span class="text-sm">jam</span>
                            </div>
                            <div class="bg-error rounded-box text-error-content flex flex-col p-2">
                                <span class="countdown font-mono text-3xl sm:text-4xl lg:text-5xl">
                                    <span style="--value:{{ $countdown['minutes'] }};" aria-live="polite"
                                        aria-label="{{ $countdown['minutes'] }}">{{ $countdown['minutes'] }}</span>
                                </span>
                                <span class="text-sm">menit</span>
                            </div>
                            <div class="bg-error rounded-box text-error-content flex flex-col p-2">
                                <span class="countdown font-mono text-3xl sm:text-4xl lg:text-5xl">
                                    <span style="--value:{{ $countdown['seconds'] }};" aria-live="polite"
                                        aria-label="{{ $countdown['seconds'] }}">{{ $countdown['seconds'] }}</span>
                                </span>
                                <span class="text-sm">detik</span>
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>

            <!-- Right Column - Main Promo -->
            <div data-aos="fade-left" data-aos-delay="100" data-aos-duration="500">
                <x-card
                    class="col-span-1 bg-gradient-to-br from-accent/20 via-primary/30 to-accent/40 relative overflow-hidden transition-all duration-300 shadow-lg hover:shadow-accent hover:scale-105"
                    body-class="p-8 lg:p-12">

                    <!-- Background decoration -->
                    <div class="absolute top-4 right-4 opacity-10">
                        <x-icon class="h-32 w-32 text-accent" name="mdi.gift" />
                    </div>

                    <!-- Kolom kanan untuk promo utama -->
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="badge badge-accent badge-lg text-xs xl:text-md font-bold animate-pulse">
                                HOT DEAL
                            </div>
                            <div class="badge badge-outline badge-lg text-xs xl:text-md">
                                Terbatas
                            </div>
                        </div>
                        <h3 class="text-3xl lg:text-4xl font-bold text-primary mb-4">
                            Promo Akhir Bulan
                        </h3>
                        <div class="text-6xl lg:text-7xl font-bold text-accent mb-2">
                            30%
                        </div>
                        <p class="text-xl font-semibold text-primary mb-6">
                            OFF untuk semua layanan
                        </p>
                        <p class="text-neutral/75 mb-8 leading-relaxed">
                            Berlaku untuk minimum 3kg. Gratis antar jemput dalam radius 5km. Promo berakhir 31
                            Desember 2024.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button class="btn btn-accent btn-lg shadow-lg">
                                <x-icon class="h-5 w-5" name="mdi.gift-outline" />
                                Ambil Promo
                            </button>
                            <button class="btn btn-outline btn-primary btn-lg">
                                Syarat & Ketentuan
                            </button>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</section>
