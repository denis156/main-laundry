<section id="faq" class="min-h-dvh px-8 flex flex-col justify-start pt-24">
    <div class="container mx-auto">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 text-primary" data-aos="fade-up"
                data-aos-duration="600">
                <span class="text-accent">FAQ</span> & Jawaban
            </h2>
            <p class="text-lg md:text-xl lg:text-2xl text-neutral/75 max-w-3xl mx-auto leading-relaxed" data-aos="fade-up"
                data-aos-delay="100" data-aos-duration="600">
                Temukan jawaban untuk pertanyaan yang sering ditanyakan pelanggan kami
            </p>
            <div class="flex justify-center mt-4" data-aos="fade-up" data-aos-delay="200" data-aos-duration="600">
                <div class="w-24 h-1 bg-gradient-to-r from-primary to-accent rounded-full"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Kolom Kiri --}}
            <div class="space-y-6" data-aos="fade-right" data-aos-duration="600">
                {{-- FAQ 1 --}}
                <x-collapse class="bg-base-300 shadow-md rounded-lg transition-all duration-300 hover:scale-105 hover:shadow-primary">
                    <x-slot:heading class="text-lg font-semibold text-primary">
                        Berapa lama waktu yang dibutuhkan untuk mencuci?
                    </x-slot:heading>
                    <x-slot:content>
                        <p class="text-neutral/75 leading-relaxed">
                            Waktu pengerjaan laundry standar adalah 2-3 hari. Untuk layanan express, pakaian Anda akan
                            selesai dalam 24 jam. Kami juga menyediakan layanan kilat 6 jam untuk kebutuhan mendesak.
                        </p>
                    </x-slot:content>
                </x-collapse>

                {{-- FAQ 2 --}}
                <x-collapse class="bg-base-300 shadow-md rounded-lg transition-all duration-300 hover:scale-105 hover:shadow-primary">
                    <x-slot:heading class="text-lg font-semibold text-primary">
                        Apakah ada layanan antar jemput?
                    </x-slot:heading>
                    <x-slot:content>
                        <p class="text-neutral/75 leading-relaxed">
                            Ya, kami menyediakan layanan antar jemput gratis untuk area tertentu dengan minimum order.
                            Silakan hubungi customer service kami untuk informasi lebih lanjut mengenai coverage area.
                        </p>
                    </x-slot:content>
                </x-collapse>

                {{-- FAQ 3 --}}
                <x-collapse class="bg-base-300 shadow-md rounded-lg transition-all duration-300 hover:scale-105 hover:shadow-primary">
                    <x-slot:heading class="text-lg font-semibold text-primary">
                        Bagaimana cara pembayarannya?
                    </x-slot:heading>
                    <x-slot:content>
                        <p class="text-neutral/75 leading-relaxed">
                            Kami menerima berbagai metode pembayaran seperti cash, transfer bank, e-wallet (GoPay, OVO,
                            Dana), dan QRIS. Pembayaran dapat dilakukan saat pengambilan atau melalui platform online
                            kami.
                        </p>
                    </x-slot:content>
                </x-collapse>

                {{-- FAQ 4 --}}
                <x-collapse class="bg-base-300 shadow-md rounded-lg transition-all duration-300 hover:scale-105 hover:shadow-primary">
                    <x-slot:heading class="text-lg font-semibold text-primary">
                        Apakah pakaian saya diasuransikan?
                    </x-slot:heading>
                    <x-slot:content>
                        <p class="text-neutral/75 leading-relaxed">
                            Ya, setiap pakaian yang diserahkan kepada kami akan diasuransikan. Kami bertanggung jawab
                            penuh atas keamanan dan kualitas pakaian Anda selama proses pencucian.
                        </p>
                    </x-slot:content>
                </x-collapse>
            </div>

            {{-- Kolom Kanan --}}
            <div class="space-y-6" data-aos="fade-left" data-aos-duration="600">
                {{-- FAQ 5 --}}
                <x-collapse class="bg-base-300 shadow-md rounded-lg transition-all duration-300 hover:scale-105 hover:shadow-primary">
                    <x-slot:heading class="text-lg font-semibold text-primary">
                        Apakah bisa mencuci barang khusus seperti boneka atau sepatu?
                    </x-slot:heading>
                    <x-slot:content>
                        <p class="text-neutral/75 leading-relaxed">
                            Tentu saja! Kami memiliki layanan khusus untuk mencuci boneka, sepatu, tas, karpet, dan
                            barang-barang lainnya. Setiap item akan ditangani dengan metode pencucian yang sesuai.
                        </p>
                    </x-slot:content>
                </x-collapse>

                {{-- FAQ 6 --}}
                <x-collapse class="bg-base-300 shadow-md rounded-lg transition-all duration-300 hover:scale-105 hover:shadow-primary">
                    <x-slot:heading class="text-lg font-semibold text-primary">
                        Berapa harga minimum untuk laundry?
                    </x-slot:heading>
                    <x-slot:content>
                        <p class="text-neutral/75 leading-relaxed">
                            Minimum order kami adalah 2 kg dengan harga mulai dari Rp 5.000/kg untuk layanan reguler.
                            Untuk layanan express dan premium, harga dapat berbeda. Cek halaman harga kami untuk detail
                            lengkap.
                        </p>
                    </x-slot:content>
                </x-collapse>

                {{-- FAQ 7 --}}
                <x-collapse class="bg-base-300 shadow-md rounded-lg transition-all duration-300 hover:scale-105 hover:shadow-primary">
                    <x-slot:heading class="text-lg font-semibold text-primary">
                        Apakah detergen yang digunakan aman untuk kulit sensitif?
                    </x-slot:heading>
                    <x-slot:content>
                        <p class="text-neutral/75 leading-relaxed">
                            Ya, kami menggunakan detergen berkualitas tinggi yang hypoallergenic dan ramah lingkungan.
                            Jika Anda memiliki alergi khusus, kami juga dapat menggunakan detergen pribadi Anda.
                        </p>
                    </x-slot:content>
                </x-collapse>

                {{-- FAQ 8 --}}
                <x-collapse class="bg-base-300 shadow-md rounded-lg transition-all duration-300 hover:scale-105 hover:shadow-primary">
                    <x-slot:heading class="text-lg font-semibold text-primary">
                        Bagaimana cara tracking status laundry saya?
                    </x-slot:heading>
                    <x-slot:content>
                        <p class="text-neutral/75 leading-relaxed">
                            Anda dapat melacak status laundry melalui website kami atau aplikasi mobile. Setiap
                            perubahan status (diterima, sedang dicuci, siap diambil) akan dikirimkan notifikasi ke
                            WhatsApp atau email Anda.
                        </p>
                    </x-slot:content>
                </x-collapse>
            </div>
        </div>
    </div>
</section>
