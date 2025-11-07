<section id="beranda" class="min-h-dvh p-8 scroll-mt-12 md:scroll-mt-14 lg:scroll-mt-16 bg-linear-to-tr from-primary via-accent/84 to-accent relative overflow-hidden">
    <div class="container mx-auto h-full flex items-center">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center w-full">
            <!-- Content -->
            <div class="text-primary-content text-center lg:text-left">
                <!-- Heading -->
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-3 leading-tight">
                    {{ config('app.name') }}
                </h1>

                <!-- Slogan -->
                <p class="text-xl md:text-2xl lg:text-3xl font-extralight mb-4">
                    Tetap Main, Tetap Bersih
                </p>

                <!-- Price Highlight -->
                <div class="mb-5 flex justify-center lg:justify-start">
                    <div class="inline-flex items-baseline gap-2 bg-accent px-5 py-2.5 rounded-xl shadow-lg">
                        <span class="text-sm md:text-md lg:text-xl font-bold">Rp 3.000</span>
                        <span class="text-xs md:text-sm lg:text-lg font-semibold">/kg</span>
                        <span class="mx-2">—</span>
                        <span class="text-xs md:text-sm lg:text-lg font-semibold">Gratis Antar Jemput</span>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6 space-y-2">
                    <p class="text-lg md:text-xl text-primary-content/95">
                        Nggak perlu keluar rumah, nggak perlu repot mikirin baju kotor.
                    </p>
                    <p class="text-md md:text-lg font-semibold">
                        Main aja, Kerja aja, Bebaskan Ekspresimu.
                    </p>
                    <p class="text-sm md:text-base text-primary-content/90">
                        Pakaian kotormu, biar kami yang antar jemput, cuci bersih sampai beres.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <x-button label="Buat Pesanan" class="btn-accent btn-block btn-sm md:btn-md lg:btn-xl" icon="solar.add-square-bold" link="#pesan" no-wire-navigate/>
                    <x-button label="Masuk Aplikasi" class="btn-secondary btn-soft btn-block btn-sm md:btn-md lg:btn-xl" icon="solar.exit-bold-duotone" link="{{ route('pelanggan.login') }}" />
                </div>
            </div>

            <!-- Mockup Phone -->
            <div class="flex justify-center lg:justify-end relative">
                <!-- Decorative Circle Background -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[212px] h-[212px] md:w-[280px] md:h-[280px] lg:w-[398px] lg:h-[398px] bg-linear-to-tr from-primary/78 to-primary rounded-full z-0"></div>

                <div class="relative mx-auto border-neutral bg-neutral border-8 md:border-10 lg:border-14 rounded-[1.75rem] md:rounded-4xl lg:rounded-[2.5rem] h-80 md:h-[420px] lg:h-[600px] w-40 md:w-[210px] lg:w-[300px] shadow-xl z-10">
                    <!-- Notch -->
                    <div class="w-20 md:w-[105px] lg:w-[148px] h-1.5 md:h-2 lg:h-3 bg-neutral top-0 rounded-b-lg md:rounded-b-xl lg:rounded-b-2xl left-1/2 -translate-x-1/2 absolute"></div>

                    <!-- Volume Buttons -->
                    <div class="h-6 md:h-8 lg:h-12 w-1 md:w-1.5 lg:w-2 bg-secondary absolute -start-[11px] md:-start-[13px] lg:-start-4 top-16 md:top-[84px] lg:top-[124px] rounded-s-lg"></div>
                    <div class="h-6 md:h-8 lg:h-12 w-1 md:w-1.5 lg:w-2 bg-secondary absolute -start-[11px] md:-start-[13px] lg:-start-4 top-24 md:top-[126px] lg:top-[178px] rounded-s-lg"></div>

                    <!-- Power Button -->
                    <div class="h-8 md:h-9 lg:h-12 w-1 md:w-1.5 lg:w-2 bg-secondary absolute -end-[11px] md:-end-[13px] lg:-end-4 top-[76px] md:top-[101px] lg:top-[142px] rounded-e-lg"></div>

                    <!-- Screen -->
                    <div class="rounded-[1.25rem] md:rounded-2xl lg:rounded-4xl overflow-hidden w-36 md:w-[190px] lg:w-[272px] h-[304px] md:h-[400px] lg:h-[572px] bg-base-100">
                        <img src="{{ asset('image/cust-mobile-view.png') }}" class="w-full h-full object-cover" alt="{{ config('app.name') }} Mobile App">
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
