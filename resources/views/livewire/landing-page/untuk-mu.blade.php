<section id="untuk-mu" class="bg-base-200 scroll-mt-16 min-h-dvh py-16 lg:py-24 relative overflow-hidden">
    {{-- Background Decorations --}}
    <x-landing-page.bg-decoration
        topRight="ibu&anak-nyuci.svg"
        bottomLeft="pria-angkat-kerangjang-laundry.svg"
        topLeft="kaos-putih-bersinar.svg"
        bottomRight="pria-menjemur.svg" />

    <div class="container mx-auto px-4 relative z-10">
        {{-- Section Header --}}
        <div data-aos="fade-up">
            <div class="text-center mb-16 transition-all duration-500">
                <div class="badge badge-primary badge-lg mb-4 gap-2">
                    <x-icon name="mdi.account-heart" class="h-4 w-4" />
                    UNTUK MU
                </div>
                <h2 class="text-4xl lg:text-5xl font-bold text-primary mb-4">
                    Main Laundry Cocok Banget <span class="text-accent">Buat Kamu!</span>
                </h2>
            </div>
        </div>

        {{-- Target Personas Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-20">
            {{-- Persona 1: Pasangan Muda Karir --}}
            <div data-aos="fade-up" data-aos-delay="0">
                <div class="card bg-base-300 shadow-lg hover:shadow-xl transition-all duration-500 h-full">
                    <div class="card-body items-center text-center flex flex-col">
                        {{-- Graphic Image --}}
                        <div class="mb-4 w-full max-w-[200px]">
                            <img src="{{ asset('grafis/pria-nyuci.svg') }}"
                                alt="Pasangan Muda & Sibuk Berkarir"
                                class="w-full h-32 object-contain" />
                        </div>

                        {{-- Title --}}
                        <h4 class="card-title text-lg text-primary mb-2">
                            Pasangan Muda & Sibuk Berkarir
                        </h4>
                    </div>
                </div>
            </div>

            {{-- Persona 2: Mahasiswa --}}
            <div data-aos="fade-up" data-aos-delay="100">
                <div class="card bg-base-300 shadow-lg hover:shadow-xl transition-all duration-500 h-full">
                    <div class="card-body items-center text-center flex flex-col">
                        {{-- Graphic Image --}}
                        <div class="mb-4 w-full max-w-[200px]">
                            <img src="{{ asset('grafis/anakperempuan-pegang-detergent.svg') }}"
                                alt="Mahasiswa"
                                class="w-full h-32 object-contain" />
                        </div>

                        {{-- Title --}}
                        <h4 class="card-title text-lg text-primary mb-2">
                            Mahasiswa
                        </h4>
                    </div>
                </div>
            </div>

            {{-- Persona 3: Pekerja & Atlet Aktif --}}
            <div data-aos="fade-up" data-aos-delay="200">
                <div class="card bg-base-300 shadow-lg hover:shadow-xl transition-all duration-500 h-full">
                    <div class="card-body items-center text-center flex flex-col">
                        {{-- Graphic Image --}}
                        <div class="mb-4 w-full max-w-[200px]">
                            <img src="{{ asset('grafis/pria-angkat-kerangjang-laundry.svg') }}"
                                alt="Pekerja & Atlet Aktif"
                                class="w-full h-32 object-contain" />
                        </div>

                        {{-- Title --}}
                        <h4 class="card-title text-lg text-primary mb-2">
                            Pekerja & Atlet Aktif
                        </h4>
                    </div>
                </div>
            </div>

            {{-- Persona 4: Gaya Hidup Clean & Smart --}}
            <div data-aos="fade-up" data-aos-delay="300">
                <div class="card bg-base-300 shadow-lg hover:shadow-xl transition-all duration-500 h-full">
                    <div class="card-body items-center text-center flex flex-col">
                        {{-- Graphic Image --}}
                        <div class="mb-4 w-full max-w-[200px]">
                            <img src="{{ asset('grafis/wanita-menjemur.svg') }}"
                                alt="Gaya Hidup Clean & Smart"
                                class="w-full h-32 object-contain" />
                        </div>

                        {{-- Title --}}
                        <h4 class="card-title text-lg text-primary mb-2">
                            Gaya Hidup Clean & Smart
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- CTA Section --}}
        <div data-aos="fade-up">
            <div class="text-center mt-16 transition-all duration-500">
                <div class="card bg-gradient-to-br from-accent/20 via-primary/20 to-secondary/20 border-2 border-accent/20">
                    <div class="card-body text-center py-12">
                        <div class="max-w-2xl mx-auto">
                            <div class="mb-6">
                                <x-icon name="mdi.heart-circle" class="h-16 w-16 text-accent mx-auto" />
                            </div>
                            <h3 class="text-3xl lg:text-4xl font-bold text-accent mb-4">
                                Tetap Main, Tetap Bersih!
                            </h3>
                            <p class="text-lg text-base-content opacity-80 mb-6">
                                Urusan bajumu? <strong class="text-primary">Kami yang beresin!</strong>
                                Cukup pesan, kami jemput, cuci, dan antar balik ke lokasi kamu.
                                <span class="text-accent font-semibold">Gampang kan?</span>
                            </p>
                            <a href="#pesan"
                                class="btn btn-accent btn-lg rounded-full gap-2 shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                                <x-icon name="mdi.cart-plus" class="h-6 w-6" />
                                Yuk, Pesan Sekarang!
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
