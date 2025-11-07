<section id="layanan" class="min-h-dvh scroll-mt-12 md:scroll-mt-14 lg:scroll-mt-16 relative overflow-hidden bg-base-100">
    <!-- Background Decoration -->
    <x-landing-page.bg-decoration />

    <!-- Content Container with Padding -->
    <div class="p-8 relative z-10">
        <div class="container mx-auto pb-20">
            <!-- Section Header -->
            <div class="text-center mt-12 mb-14">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-accent">
                    Layanan Kami
                </h2>
                <p class="text-md md:text-lg lg:text-xl text-base-content/80 max-w-2xl mx-auto">
                    Pilih layanan laundry terbaik sesuai kebutuhanmu .
                </p>
            </div>

            <!-- Services Grid -->
            <div class="flex flex-col md:grid md:grid-cols-2 gap-4 md:items-center mb-8">
                <div
                    class="card bg-base-300 shadow-xl h-full relative overflow-hidden max-w-lg md:max-w-xl lg:max-w-2xl mx-auto w-full">
                    {{-- Background Logo --}}
                    <div class="absolute inset-0 flex items-center justify-center opacity-8 pointer-events-none">
                        <img src="{{ asset('image/logo.png') }}" alt="Main Laundry Logo"
                            class="w-auto h-32 md:h-40 lg:h-44 object-contain" />
                    </div>

                    {{-- Service Content --}}
                    <div class="card-body flex flex-col items-center justify-center text-center relative z-10">
                        <div class="text-center w-full mb-6">
                            <h3 class="text-3xl text-accent font-bold text-center w-full">Cuci Lipat</h3>
                            <p class="text-lg text-base-content/60 font-medium text-center w-full">Layanan Pertama Dari Kami
                            </p>
                        </div>

                        {{-- Price & Duration --}}
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="flex items-center justify-center gap-2 bg-primary px-4 py-3 rounded-lg">
                                <span class="text-sm md:text-lg lg:text-xl font-bold text-primary-content">Rp 3.000</span>
                                <span class="text-xs md:text-sm lg:text-md text-primary-content/80">/kg</span>
                            </div>
                            <div class="flex items-center justify-center gap-2 bg-secondary px-4 py-3 rounded-lg">
                                <span class="text-sm md:text-lg lg:text-xl font-bold text-secondary-content">1 Hari</span>
                                <span class="text-xs md:text-sm lg:text-md text-secondary-content/80">Kerja</span>
                            </div>
                        </div>

                        <p class="text-base-content/80 font-medium text-md md:text-lg lg:text-xl text-center">Harga
                            terjangkau, gratis antar jemput.
                            <span class="text-accent font-bold text-lg md:text-xl lg:text-2xl">Yuk Pesan Sekarang!</span>
                        </p>

                        {{-- Spacer to push button to bottom --}}
                        <div class="grow"></div>

                        {{-- CTA Button --}}
                        <div class="card-actions justify-center mt-4">
                            <a href="#pesan"
                                class="btn btn-accent btn-block rounded-full gap-2 hover:scale-105 transition-transform max-w-xs">
                                <x-icon name="mdi.cart" class="h-5 w-5" />
                                Buat Pesanan
                            </a>
                        </div>
                    </div>
                </div>
                <div
                    class="card bg-base-300 shadow-xl h-full relative overflow-hidden max-w-lg md:max-w-xl lg:max-w-2xl mx-auto w-full">
                    {{-- Background Logo --}}
                    <div class="absolute inset-0 flex items-center justify-center opacity-8 pointer-events-none">
                        <img src="{{ asset('image/logo.png') }}" alt="Main Laundry Logo"
                            class="w-auto h-32 md:h-40 lg:h-44 object-contain" />
                    </div>

                    {{-- Service Content --}}
                    <div class="card-body flex flex-col items-center justify-center text-center relative z-10">
                        <div class="text-center w-full mb-6">
                            <h3 class="text-3xl text-accent font-bold text-center w-full">Cuci Lipat</h3>
                            <p class="text-lg text-base-content/60 font-medium text-center w-full">Layanan Pertama Dari Kami
                            </p>
                        </div>

                        {{-- Price & Duration --}}
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <div class="flex items-center justify-center gap-2 bg-primary px-4 py-3 rounded-lg">
                                <span class="text-sm md:text-lg lg:text-xl font-bold text-primary-content">Rp 3.000</span>
                                <span class="text-xs md:text-sm lg:text-md text-primary-content/80">/kg</span>
                            </div>
                            <div class="flex items-center justify-center gap-2 bg-secondary px-4 py-3 rounded-lg">
                                <span class="text-sm md:text-lg lg:text-xl font-bold text-secondary-content">1 Hari</span>
                                <span class="text-xs md:text-sm lg:text-md text-secondary-content/80">Kerja</span>
                            </div>
                        </div>

                        <p class="text-base-content/80 font-medium text-md md:text-lg lg:text-xl text-center">Harga
                            terjangkau, gratis antar jemput.
                            <span class="text-accent font-bold text-lg md:text-xl lg:text-2xl">Yuk Pesan Sekarang!</span>
                        </p>

                        {{-- Spacer to push button to bottom --}}
                        <div class="grow"></div>

                        {{-- CTA Button --}}
                        <div class="card-actions justify-center mt-4">
                            <a href="#pesan"
                                class="btn btn-accent btn-block rounded-full gap-2 hover:scale-105 transition-transform max-w-xs">
                                <x-icon name="mdi.cart" class="h-5 w-5" />
                                Buat Pesanan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
