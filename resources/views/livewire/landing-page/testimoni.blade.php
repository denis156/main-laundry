<section id="testimoni" class="min-h-dvh px-8 flex flex-col justify-start pt-24">
    <div class="container mx-auto">
        <div class="text-right mb-16">
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 text-primary" data-aos="fade-left"
                data-aos-duration="600">
                <span class="text-accent">Testimoni</span> Pelanggan
            </h2>
            <p class="text-lg md:text-xl lg:text-2xl text-neutral/75 max-w-3xl ml-auto leading-relaxed"
                data-aos="fade-left" data-aos-delay="100" data-aos-duration="600">
                Dengarkan langsung pengalaman dan kepuasan pelanggan setia kami
            </p>
            <div class="flex justify-end mt-4" data-aos="fade-left" data-aos-delay="200" data-aos-duration="600">
                <div class="w-24 h-1 bg-gradient-to-r from-primary to-accent rounded-full"></div>
            </div>
        </div>

        <!-- Horizontal Scroll Testimoni -->
        <div class="flex gap-6 overflow-x-auto no-scrollbar p-8 mb-12 mt-38">

            @foreach ($testimonials as $index => $testimonial)
                <x-card
                    class="max-w-md mx-auto bg-base-300 shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-primary flex-shrink-0 w-96 border-2 border-primary/20">
                    <div class="flex items-center justify-center gap-2 mb-3">
                        <x-rating wire:model="ratings.{{ $index }}" class="bg-warning" total="5" />
                        {{-- <span class="text-sm font-bold text-warning">{{ $testimonial['rating'] }}</span> --}}
                    </div>
                    <p class="text-sm text-secondary italic text-center leading-relaxed mb-3 font-medium">
                        "{{ $testimonial['text'] }}"
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        <div class="avatar">
                            <div class="w-8 rounded-full bg-primary">
                                <div class="w-full h-full flex items-center justify-center">
                                    <span
                                        class="text-sm text-primary-content font-bold">{{ $testimonial['initials'] }}</span>
                                </div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-primary">{{ $testimonial['name'] }}</span>
                    </div>
                </x-card>
            @endforeach
        </div>
    </div>
</section>
