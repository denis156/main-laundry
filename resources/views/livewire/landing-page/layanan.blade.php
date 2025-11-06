<section id="layanan" class="bg-base-300 scroll-mt-16 min-h-dvh py-16 lg:py-24 relative overflow-hidden">
    {{-- Background Decorations --}}
    <x-landing-page.bg-decoration topRight="mesin-cuci-di-maintenence.svg" bottomLeft="anakperempuan-pegang-detergent.svg"
        topLeft="tutup-mesin-cuci.svg" bottomRight="wanita-menjemur.svg" />

    <div class="container mx-auto px-4 relative z-10">
        {{-- Section Header --}}
        <div data-aos="fade-up">
            <div class="text-center mb-16 transition-all duration-500">
                <div class="badge badge-accent badge-lg mb-4 gap-2">
                    <x-icon name="mdi.washing-machine" class="h-4 w-4" />
                    LAYANAN KAMI
                </div>
                <h2 class="text-4xl lg:text-5xl font-bold text-accent mb-4">
                    Laundry Termurah SeIndonesia
                </h2>
                <p class="text-xl text-base-content opacity-80 max-w-2xl mx-auto">
                    Kami adalah <strong class="text-primary">ekspresi gaya hidup baru di kotamu</strong> —
                    <span class="text-accent font-semibold">"main sepuasnya, baju kotormu biar kami yang
                        beresin."</span>
                </p>
            </div>
        </div>

        {{-- Services Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-20">
            @forelse($services as $index => $service)
                <div data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    <div
                        class="card bg-base-200 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 h-full relative overflow-hidden">
                        {{-- Background Logo --}}
                        <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                            <img src="{{ asset('image/logo.png') }}" alt="Main Laundry Logo"
                                class="w-64 h-64 object-contain" />
                        </div>

                        {{-- Service Content --}}
                        <div class="card-body flex flex-col relative z-10">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="bg-accent/10 p-3 rounded-full">
                                    <x-icon name="mdi.washing-machine" class="h-6 w-6 text-accent" />
                                </div>
                                <h3 class="card-title text-2xl text-accent">{{ $service->name }}</h3>
                            </div>

                            {{-- Price & Duration --}}
                            <div class="flex items-center gap-4 mb-6">
                                <div class="badge badge-primary badge-lg font-bold">
                                    Rp {{ number_format($service->price_per_kg, 0, ',', '.') }}/kg
                                </div>
                                <div class="badge badge-secondary badge-outline">
                                    {{ $service->duration_days }} Hari
                                </div>
                            </div>

                            {{-- Spacer to push button to bottom --}}
                            <div class="flex-grow"></div>

                            {{-- CTA Button --}}
                            <div class="card-actions justify-end mt-4">
                                <a href="#pesan"
                                    class="btn btn-accent btn-block rounded-full gap-2 hover:scale-105 transition-transform">
                                    <x-icon name="mdi.cart" class="h-5 w-5" />
                                    Buat Pesanan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-base-content opacity-60">
                        <x-icon name="mdi.alert-circle" class="h-12 w-12 mx-auto mb-4" />
                        <p class="text-lg">Belum ada layanan tersedia saat ini.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Features Grid --}}
        <div data-aos="fade-up">
            <div class="mb-16 transition-all duration-500">
                <h3 class="text-3xl lg:text-4xl font-bold text-center text-accent mb-12">
                    Kenapa Pilih <span class="text-primary">Main Laundry</span>?
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($features as $index => $feature)
                        <div data-aos="zoom-in" data-aos-delay="{{ $index * 100 }}">
                            <div class="card bg-base-200 shadow-lg hover:shadow-xl transition-all duration-500 h-full">
                                <div class="card-body items-center text-center flex flex-col">
                                    {{-- Graphic Image --}}
                                    <div class="mb-4 w-full max-w-[200px]">
                                        <img src="{{ asset('grafis/' . $feature['graphic']) }}"
                                            alt="{{ $feature['title'] }}" class="w-full h-32 object-contain" />
                                    </div>

                                    {{-- Title --}}
                                    <h4 class="card-title text-lg text-{{ $feature['color'] }} mb-2">
                                        {{ $feature['title'] }}
                                    </h4>

                                    {{-- Description --}}
                                    <p class="text-sm text-base-content opacity-80">
                                        {{ $feature['description'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- CTA Section --}}
        <div data-aos="fade-up">
            <div class="text-center mt-16 transition-all duration-500">
                <div
                    class="card bg-gradient-to-br from-accent/20 via-primary/20 to-secondary/20 border-2 border-accent/20">
                    <div class="card-body text-center py-12">
                        <div class="max-w-2xl mx-auto">
                            <div class="mb-6">
                                <x-icon name="mdi.star-circle" class="h-16 w-16 text-accent mx-auto" />
                            </div>
                            <h3 class="text-3xl lg:text-4xl font-bold text-accent mb-4">
                                Segera Cobain Layanan Kami
                            </h3>
                            <p class="text-lg text-base-content opacity-80 mb-6">
                                Mulai dari <strong class="text-primary">Rp 3.000/kg</strong> aja!
                                Dijemput gratis, diantar gratis, dijamin bersih & wangi.
                                <span class="text-accent font-semibold">Gak ada alasan lagi untuk pakaian kotor!</span>
                            </p>
                            <a href="#pesan"
                                class="btn btn-accent btn-lg rounded-full gap-2 shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                                <x-icon name="mdi.phone" class="h-6 w-6" />
                                Pesan Sekarang!
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
