<section id="beranda" class="bg-primary/14 scroll-mt-16 min-h-dvh flex items-center relative overflow-hidden">
    {{-- Background Decorations --}}
    <x-landing-page.bg-decoration />

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
                <div class="badge badge-accent badge-lg mb-4 gap-2">
                    <x-icon name="mdi.fire" class="h-4 w-4" />
                    TELAH HADIR DI KOTA KENDARI!
                </div>
                <h1 class="text-4xl lg:text-6xl font-bold text-accent leading-tight mb-4">
                    Tetap Main, <br />
                    <span class="text-primary">Tetap Bersih!</span>
                </h1>
                <p class="text-xl lg:text-2xl font-semibold text-secondary mb-6">
                    Cuma <span class="text-accent">Rp 3.000/kg</span>
                    <span class="text-primary">— Satu Kota Dijemput Gratis!</span>
                </p>
                <p class="text-lg text-base-content opacity-80 mb-8 max-w-xl mx-auto lg:mx-0">
                    Gak perlu keluar rumah, gak perlu repot mikir baju kotor.
                    <strong>Main aja. Kerja aja. Kuliah aja.</strong><br />
                    Urusan bajumu, biar kami yang antar-jemput, cuci, bersih, beres.
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                    <a href="#pesan"
                        class="btn btn-accent btn-lg rounded-full gap-2 shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                        <x-icon name="mdi.phone" class="h-6 w-6" />
                        Pesan Sekarang
                    </a>
                    <a href="#cara-kerja"
                        class="btn btn-secondary btn-outline btn-lg rounded-full gap-2 hover:scale-105 transition-all">
                        <x-icon name="mdi.information" class="h-6 w-6" />
                        Cara Kerja
                    </a>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-4 max-w-md mx-auto lg:mx-0">
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-bold text-primary">
                            {{ $totalCustomers >= 1000 ? number_format($totalCustomers / 1000, 1) . 'K+' : $totalCustomers }}
                        </div>
                        <div class="text-sm text-base-content opacity-70">Lebih Pelanggan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-bold text-accent">{{ $totalPos }}</div>
                        <div class="text-sm text-base-content opacity-70">Lebih Pos</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-bold text-secondary">
                            {{ $totalTransactions >= 1000 ? number_format($totalTransactions / 1000, 1) . 'K+' : $totalTransactions }}
                        </div>
                        <div class="text-sm text-base-content opacity-70">Lebih Pesanan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
