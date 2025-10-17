<section id="tentang-kami" class="bg-base-200 scroll-mt-16 min-h-dvh py-16 lg:py-24 relative overflow-hidden">
    <div class="container mx-auto px-4">
        {{-- Section Header --}}
        <div data-aos="fade-up">
            <div class="text-center mb-16 transition-all duration-500">
                <div class="badge badge-primary badge-lg mb-4 gap-2">
                    <x-icon name="mdi.information" class="h-4 w-4" />
                    TENTANG KAMI
                </div>
                <h2 class="text-4xl lg:text-5xl font-bold text-primary mb-6">
                    Gerbang Menuju <span class="text-accent">Main Group</span>
                </h2>
                <p class="text-xl text-base-content opacity-80 max-w-3xl mx-auto leading-relaxed">
                    <strong class="text-primary">Main Laundry</strong> bukan sekadar layanan laundry biasa.
                    Kami adalah <strong class="text-accent">pintu gerbang</strong> dari ekosistem
                    <strong class="text-primary">Main Group</strong> di Indonesia —
                    sebuah gerakan untuk membuat hidup masyarakat lebih praktis, efisien, dan berkualitas.
                </p>
            </div>
        </div>

        {{-- Main Content with Image --}}
        <div data-aos="fade-up">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-20 transition-all duration-500">
                {{-- Image --}}
                <div class="order-2 lg:order-1">
                    <div class="relative">
                        <div class="absolute inset-0 bg-primary/10 rounded-3xl blur-3xl"></div>
                        <img src="{{ asset('image/logo.png') }}"
                             alt="Main Laundry - Main Group"
                             class="relative w-full max-w-md mx-auto h-auto object-contain" />
                    </div>
                </div>

                {{-- Content --}}
                <div class="order-1 lg:order-2">
                    <h3 class="text-3xl lg:text-4xl font-bold text-accent mb-6">
                        Mengapa Main Laundry?
                    </h3>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center">
                                    <x-icon name="mdi.rocket-launch" class="h-6 w-6 text-primary" />
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-primary mb-2">Bagian dari Visi Besar</h4>
                                <p class="text-base-content opacity-80">
                                    Main Laundry adalah langkah pertama Main Group dalam menciptakan ekosistem layanan
                                    yang terintegrasi untuk masyarakat Indonesia.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center">
                                    <x-icon name="mdi.target" class="h-6 w-6 text-accent" />
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-accent mb-2">Fokus pada Kemudahan</h4>
                                <p class="text-base-content opacity-80">
                                    Kami percaya bahwa teknologi dan layanan berkualitas harus mudah diakses oleh semua orang.
                                    Main Laundry membuktikan komitmen itu.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-secondary/10 rounded-full flex items-center justify-center">
                                    <x-icon name="mdi.lightbulb-on" class="h-6 w-6 text-secondary" />
                                </div>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-secondary mb-2">Inovasi Berkelanjutan</h4>
                                <p class="text-base-content opacity-80">
                                    Setiap layanan dirancang untuk terus berkembang, mengikuti kebutuhan masyarakat
                                    dan tren teknologi terkini.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Values Grid --}}
        <div data-aos="fade-up">
            <div class="mb-16 transition-all duration-500">
                <h3 class="text-3xl lg:text-4xl font-bold text-center text-primary mb-12">
                    Nilai-Nilai <span class="text-accent">Main Group</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($values as $index => $value)
                        <div data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                            <div class="card bg-base-300 shadow-lg hover:shadow-xl transition-all duration-500 h-full">
                                <div class="card-body items-center text-center flex flex-col">
                                    {{-- Graphic Image --}}
                                    <div class="mb-6 w-full max-w-[250px]">
                                        <img src="{{ asset('grafis/' . $value['graphic']) }}"
                                             alt="{{ $value['title'] }}"
                                             class="w-full h-40 object-contain" />
                                    </div>

                                    {{-- Title --}}
                                    <h4 class="card-title text-xl text-{{ $value['color'] }} mb-3">
                                        {{ $value['title'] }}
                                    </h4>

                                    {{-- Description --}}
                                    <p class="text-base-content opacity-80">
                                        {{ $value['description'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Vision Statement --}}
        <div data-aos="fade-up">
            <div class="card bg-gradient-to-br from-primary/20 via-accent/20 to-secondary/20 border-2 border-primary/20 transition-all duration-500">
                <div class="card-body text-center py-12">
                    <div class="max-w-3xl mx-auto">
                        <div class="mb-6">
                            <x-icon name="mdi.flag-variant" class="h-16 w-16 text-primary mx-auto" />
                        </div>
                        <h3 class="text-3xl lg:text-4xl font-bold text-primary mb-6">
                            Visi Main Group
                        </h3>
                        <p class="text-xl text-base-content opacity-90 leading-relaxed mb-6">
                            Menciptakan ekosistem layanan terintegrasi yang membuat hidup masyarakat Indonesia
                            lebih <strong class="text-accent">praktis</strong>, <strong class="text-primary">efisien</strong>,
                            dan <strong class="text-secondary">berkualitas</strong>.
                        </p>
                        <p class="text-lg text-base-content opacity-80">
                            Main Laundry adalah langkah awal dari perjalanan panjang kami untuk menghadirkan
                            solusi terbaik di berbagai sektor kehidupan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Decorative Background Elements --}}
    <div class="absolute inset-0 pointer-events-none opacity-5">
        <img src="{{ asset('grafis/mesin-cuci-di-maintenence.svg') }}" alt=""
             class="absolute top-40 right-10 w-32 lg:w-48 animate-float-slow" />
        <img src="{{ asset('grafis/anakperempuan-pegang-detergent.svg') }}" alt=""
             class="absolute bottom-40 left-10 w-24 lg:w-36 animate-float-medium" />
    </div>
</section>
