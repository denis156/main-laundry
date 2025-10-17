<section id="kontak" class="bg-base-200 scroll-mt-16 min-h-dvh py-16 lg:py-24 relative overflow-hidden">
    {{-- Background Decorations --}}
    <x-landing-page.bg-decoration
        topRight="kurir.svg"
        bottomLeft="smartphone.svg"
        topLeft="mobil.svg"
        bottomRight="pos.svg" />

    <div class="container mx-auto px-4 relative z-10">
        {{-- Section Header --}}
        <div data-aos="fade-up">
            <div class="text-center mb-16 transition-all duration-500">
                <div class="badge badge-primary badge-lg mb-4 gap-2">
                    <x-icon name="mdi.map-marker" class="h-4 w-4" />
                    HUBUNGI KAMI
                </div>
                <h2 class="text-4xl lg:text-5xl font-bold text-primary mb-4">
                    Ada Pertanyaan? <span class="text-accent">Kontak Kami!</span>
                </h2>
                <p class="text-xl text-base-content opacity-80 max-w-2xl mx-auto">
                    Tim kami siap membantu Anda <strong class="text-primary">24/7</strong>.
                    Hubungi kami melalui berbagai channel yang tersedia.
                </p>
            </div>
        </div>

        {{-- Contact Info & Map Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
            {{-- Contact Cards --}}
            <div data-aos="fade-right">
                <div class="space-y-4">
                    <h3 class="text-2xl lg:text-3xl font-bold text-accent mb-6">
                        Informasi Kontak
                    </h3>

                    @foreach ($contacts as $index => $contact)
                        <div data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                            <a href="{{ $contact['link'] }}" target="_blank"
                                class="card bg-base-300 shadow-lg hover:shadow-xl transition-all duration-500 hover:-translate-y-1 block">
                                <div class="card-body p-6">
                                    <div class="flex items-center gap-4">
                                        <div class="bg-{{ $contact['color'] }}/10 p-4 rounded-full">
                                            <x-icon name="{{ $contact['icon'] }}" class="h-8 w-8 text-{{ $contact['color'] }}" />
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-lg text-{{ $contact['color'] }}">
                                                {{ $contact['title'] }}
                                            </h4>
                                            <p class="text-base-content opacity-80">
                                                {{ $contact['value'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Google Maps --}}
            <div data-aos="fade-left">
                <div class="card bg-base-300 shadow-xl h-full overflow-hidden">
                    <div class="card-body p-0">
                        <iframe
                            src="{{ $address['mapEmbed'] }}"
                            class="w-full h-[500px] lg:h-full min-h-[500px]"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>

        {{-- CTA Section --}}
        <div data-aos="fade-up">
            <div class="text-center mt-16 transition-all duration-500">
                <div class="card bg-gradient-to-br from-primary/20 via-accent/20 to-secondary/20 border-2 border-primary/20">
                    <div class="card-body text-center py-12">
                        <div class="max-w-2xl mx-auto">
                            <div class="mb-6">
                                <x-icon name="mdi.chat" class="h-16 w-16 text-primary mx-auto" />
                            </div>
                            <h3 class="text-3xl lg:text-4xl font-bold text-primary mb-4">
                                Siap Untuk Memulai?
                            </h3>
                            <p class="text-lg text-base-content opacity-80 mb-6">
                                Hubungi kami sekarang atau langsung buat pesanan!
                                <strong class="text-accent">CS kami akan segera merespon pertanyaan Anda.</strong>
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" target="_blank"
                                    class="btn btn-success btn-lg rounded-full gap-2 shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                                    <x-icon name="mdi.whatsapp" class="h-6 w-6" />
                                    Tanya ke CS
                                </a>
                                <a href="#pesan"
                                    class="btn btn-accent btn-lg rounded-full gap-2 shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                                    <x-icon name="mdi.phone" class="h-6 w-6" />
                                    Pesan Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
