<section id="beranda" class="bg-primary/14 scroll-mt-16 min-h-dvh flex items-center relative overflow-hidden">
    {{-- Background Decorations --}}
    <x-landing-page.bg-decoration
        topRight="kaos-kotor-menjadi-kaos-bersinar.svg"
        bottomLeft="botol-pewangi.svg"
        topLeft="dus-detergent.svg"
        bottomRight="mesin-cuci.svg" />

    <div class="container mx-auto px-4 py-16 lg:py-24 relative z-10">
        <div class="flex flex-col lg:flex-row-reverse items-center gap-12">
            {{-- Image --}}
            <div class="flex-1 flex justify-center">
                <div class="relative">
                    <div class="absolute inset-0 bg-accent/40 rounded-3xl blur-3xl"></div>
                    <img src="{{ asset('grafis/kaos-kotor-menjadi-kaos-bersinar.svg') }}"
                        alt="Main Laundry - Layanan Profesional"
                        class="relative w-full max-w-xs lg:max-w-md hover:scale-105 transition-transform duration-300" />
                </div>
            </div>

            {{-- Content --}}
            <div class="flex-1 text-center lg:text-left">
                <p class="text-base sm:text-xl lg:text-2xl bg-accent rounded-full py-2 px-3 sm:px-4 text-base-100 font-bold mb-6 text-center mx-auto lg:mx-0 w-fit">
                    MAIN LAUNDRY TELAH HADIR DI KOTA ANDA!
                </p>
                <h1 class="text-4xl lg:text-6xl font-bold text-accent leading-tight mb-4">
                    Tetap Main, <br />
                    <span class="text-primary">Tetap Bersih!</span>
                </h1>
                <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-3 mb-6 mx-auto lg:mx-0 w-fit">
                    <p class="text-xl sm:text-2xl lg:text-3xl bg-error rounded-full py-2 px-3 sm:px-4 text-base-100 font-bold">
                        Rp 3.000/kg — Dijemputin
                    </p>
                    <span class="text-3xl sm:text-4xl lg:text-5xl font-black text-error animate-pulse">GRATIS!</span>
                </div>
                <p class="text-lg text-base-content opacity-80 mb-8 max-w-xl mx-auto lg:mx-0">
                    Sekarang nggak perlu keluar rumah, apalagi repot mikirin baju kotor.
                    <strong>Main aja, Kerja aja, Bebaskan Ekspresimu.</strong><br />
                    Pakaian kotormu, biar kami yang antar jemput, cuci bersih sampai beres.
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                    <a href="#pesan"
                        class="btn btn-accent btn-lg rounded-full gap-2 shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                        <x-icon name="mdi.phone" class="h-6 w-6" />
                        Pesan Sekarang
                    </a>
                    <a href="#untuk-mu"
                        class="btn btn-secondary btn-outline btn-lg rounded-full gap-2 hover:scale-105 transition-all">
                        <x-icon name="mdi.information" class="h-6 w-6" />
                        Untuk Mu
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
