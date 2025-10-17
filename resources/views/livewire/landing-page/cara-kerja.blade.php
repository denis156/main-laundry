<section id="cara-kerja" class="bg-base-300 scroll-mt-16 min-h-dvh py-16 lg:py-24 relative overflow-hidden">
    <div class="container mx-auto px-4">
        {{-- Section Header --}}
        <div data-aos="fade-up">
            <div class="text-center mb-16 transition-all duration-500">
                <div class="badge badge-secondary badge-lg mb-4 gap-2">
                    <x-icon name="mdi.map-marker-path" class="h-4 w-4" />
                    CARA KERJA
                </div>
                <h2 class="text-4xl lg:text-5xl font-bold text-secondary mb-4">
                    Simpel & <span class="text-accent">Transparan</span>
                </h2>
                <p class="text-xl text-base-content opacity-80 max-w-2xl mx-auto">
                    Dari pesan sampai terima pakaian bersih, semua <strong class="text-primary">mudah ditrack</strong> dan
                    <span class="text-accent font-semibold">dijamin aman.</span>
                </p>
            </div>
        </div>

        {{-- Timeline --}}
        <div data-aos="fade-up">
            <ul class="timeline timeline-snap-icon max-md:timeline-compact timeline-vertical">
                @foreach ($steps as $index => $step)
                    <li>
                        @if ($index > 0)
                            <hr class="bg-primary" />
                        @endif
                        <div class="timeline-middle">
                            <x-icon name="{{ $step['icon'] }}" class="h-6 w-6 text-{{ $step['color'] }}" />
                        </div>
                        <div class="timeline-{{ $index % 2 === 0 ? 'start' : 'end' }} mb-10 {{ $index % 2 === 0 ? 'md:text-end' : '' }}">
                            <time class="font-mono italic text-{{ $step['color'] }}">{{ $step['phase'] }}</time>
                            <div class="text-2xl lg:text-3xl font-black text-{{ $step['color'] }} mb-2">
                                {{ $step['title'] }}
                            </div>
                            <p class="text-base-content opacity-80 mb-4">
                                {{ $step['description'] }}
                            </p>
                            <div class="flex {{ $index % 2 === 0 ? 'justify-end' : 'justify-start' }} mb-4">
                                <img src="{{ asset('grafis/' . $step['graphic']) }}"
                                    alt="{{ $step['title'] }}"
                                    class="w-32 h-32 object-contain opacity-90" />
                            </div>
                            <ul class="space-y-2 {{ $index % 2 === 0 ? 'text-right' : 'text-left' }}">
                                @foreach ($step['details'] as $detail)
                                    <li class="flex items-start gap-2 text-sm text-base-content opacity-70 {{ $index % 2 === 0 ? 'flex-row-reverse' : '' }}">
                                        <x-icon name="mdi.check-circle" class="h-5 w-5 text-{{ $step['color'] }} flex-shrink-0 mt-0.5" />
                                        <span>{{ $detail }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @if ($index < count($steps) - 1)
                            <hr class="bg-primary" />
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- CTA Section --}}
        <div data-aos="fade-up">
            <div class="text-center mt-16 transition-all duration-500">
                <div class="card bg-gradient-to-br from-accent/20 via-primary/20 to-secondary/20 border-2 border-accent/20">
                    <div class="card-body text-center py-12">
                        <div class="max-w-2xl mx-auto">
                            <div class="mb-6">
                                <x-icon name="mdi.lightning-bolt" class="h-16 w-16 text-accent mx-auto" />
                            </div>
                            <h3 class="text-3xl lg:text-4xl font-bold text-accent mb-4">
                                Mudah Kan?
                            </h3>
                            <p class="text-lg text-base-content opacity-80 mb-6">
                                Gak perlu repot, gak perlu ribet. Cukup pesan, kami urus semuanya dari pickup sampai antar balik.
                                <strong class="text-primary">Main aja, beres!</strong>
                            </p>
                            <a href="#pesan"
                                class="btn btn-accent btn-lg rounded-full gap-2 shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                                <x-icon name="mdi.rocket-launch" class="h-6 w-6" />
                                Yuk, Pesan Sekarang!
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Decorative Background Elements --}}
    <div class="absolute inset-0 pointer-events-none opacity-5">
        <img src="{{ asset('grafis/ibu&anak-nyuci.svg') }}" alt=""
            class="absolute top-20 right-10 w-32 lg:w-48 animate-float-slow" />
        <img src="{{ asset('grafis/wanita-menjemur.svg') }}" alt=""
            class="absolute bottom-20 left-10 w-24 lg:w-36 animate-float-medium" />
    </div>
</section>
