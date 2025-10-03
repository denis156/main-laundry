<section id="reservasi" class="min-h-dvh px-8 flex flex-col justify-start pt-24">
    <div class="container mx-auto max-w-4xl">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4 text-primary" data-aos="fade-up"
                data-aos-duration="600">
                <span class="text-accent">Reservasi</span> Sekarang
            </h2>
            <p class="text-lg md:text-xl lg:text-2xl text-neutral/75 max-w-3xl mx-auto leading-relaxed" data-aos="fade-up"
                data-aos-delay="100" data-aos-duration="600">
                Pesan layanan laundry dengan mudah, cepat, dan praktis melalui sistem online kami
            </p>
            <div class="flex justify-center mt-4" data-aos="fade-up" data-aos-delay="200" data-aos-duration="600">
                <div class="w-24 h-1 bg-gradient-to-r from-primary to-accent rounded-full"></div>
            </div>
        </div>

        <!-- Form Reservasi -->
        <div data-aos="fade-up" data-aos-delay="300" data-aos-duration="600">
            <x-card class="bg-base-300 shadow-xl shadow-primary mb-12">
                <x-form wire:submit="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <x-input label="Nama Lengkap" wire:model="nama_lengkap"
                            placeholder="Masukkan nama lengkap" icon="mdi.account" />
                        <x-input label="Nomor Telepon" wire:model="nomor_telepon"
                            placeholder="08xx-xxxx-xxxx" type="tel" icon="mdi.phone" />
                        <x-select label="Jenis Layanan" wire:model="jenis_layanan"
                            :options="$layananOptions" placeholder="Pilih layanan" icon="mdi.washing-machine" />
                        <x-select label="Kecepatan" wire:model="kecepatan" :options="$kecepatanOptions"
                            placeholder="Pilih kecepatan" icon="mdi.clock-fast" />
                        <x-input label="Estimasi Berat (kg)" wire:model="berat" placeholder="Contoh: 5"
                            type="number" min="1" icon="mdi.weight-kilogram" />
                        <x-datetime label="Tanggal & Waktu Pickup" wire:model="tanggal_pickup"
                            type="datetime-local" icon="mdi.calendar-clock" />
                        <x-textarea label="Alamat Lengkap" wire:model="alamat"
                            placeholder="Masukkan alamat lengkap untuk pickup" rows="3"
                            hint="Cantumkan patokan untuk memudahkan kurir" class="md:col-span-2" />
                        <x-textarea label="Catatan (Opsional)" wire:model="catatan"
                            placeholder="Contoh: Ada noda membandel di kemeja putih" rows="2"
                            class="md:col-span-2" />
                    </div>

                    <!-- Summary Info -->
                    <div class="bg-base-100 p-4 rounded-lg mb-6">
                        <div class="flex items-start gap-3">
                            <x-icon name="mdi.information" class="h-5 w-5 text-info flex-shrink-0 mt-0.5" />
                            <div class="text-sm text-neutral/75">
                                <ul class="space-y-1">
                                    <li>• Gratis antar jemput dalam radius 5km</li>
                                    <li>• Minimum order 2kg untuk layanan reguler</li>
                                    <li>• Tim kami akan menghubungi Anda untuk konfirmasi</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-end">
                        <x-button label="Reset" class="btn-outline" type="reset" />
                        <x-button label="Kirim Reservasi" class="btn-primary" type="submit"
                            icon="mdi.send" />
                    </div>
                </x-form>
            </x-card>
        </div>
    </div>
</section>
