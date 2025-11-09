<section id="pesan" class="min-h-dvh p-8 scroll-mt-12 md:scroll-mt-14 lg:scroll-mt-16 relative overflow-hidden">
    <!-- Background Decoration -->
    <x-landing-page.bg-decoration />

    <!-- Content Container with Padding -->
    <div class="p-8 relative z-10">
        <div class="container mx-auto pb-20">
            <!-- Section Header -->
            <div class="text-center mt-12 mb-14">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 text-accent">
                    Pesan Sekarang
                </h2>
                <p class="text-md md:text-lg lg:text-xl text-base-content/80 max-w-2xl mx-auto">
                    Isi form di bawah dan tim kami akan segera menghubungi Anda via WhatsApp untuk konfirmasi
                </p>
            </div>

            <!-- Order Form -->
            <div class="max-w-4xl mx-auto">
                <div class="card bg-base-300 shadow-xl">
                    <div class="card-body p-6 lg:p-8">
                        <!-- Mary UI Form with Toast -->
                        <x-form wire:submit="save" no-separator>
                        {{-- Data Customer Section --}}
                        <div class="mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Nama Lengkap --}}
                                <x-input label="Nama Lengkap" wire:model="name" placeholder="Contoh: Budi Santoso"
                                    icon="o-user" hint="Nama sesuai KTP" />

                                {{-- Nomor WhatsApp --}}
                                <x-input label="Nomor WhatsApp" wire:model="phone" placeholder="Contoh: 81234567890"
                                    prefix="+62" hint="Bisa tulis dengan 08 atau langsung 8" maxlength="15" />

                                {{-- Email (Optional) --}}
                                <x-input label="Email (Opsional)" wire:model="email" type="email"
                                    placeholder="Contoh: budi@email.com" icon="o-envelope"
                                    hint="Untuk bukti invoice digital" />

                                {{-- Pilih Layanan --}}
                                <x-select label="Pilih Layanan" wire:model="service_id" :options="$services"
                                    option-value="id" option-label="name" icon="o-sparkles"
                                    placeholder="--- Pilih Layanan ---" hint="Pilih jenis layanan yang Anda inginkan" />

                                {{-- Kecamatan --}}
                                <x-select label="Kecamatan" wire:model.live="district_code" :options="$districts"
                                    option-value="code" option-label="name" icon="o-map-pin"
                                    placeholder="--- Pilih Kecamatan ---" hint="Pilih kecamatan di Kota Kendari" />

                                {{-- Kelurahan --}}
                                <x-select label="Kelurahan" wire:model="village_code" :options="$villages"
                                    option-value="code" option-label="name" icon="o-home"
                                    placeholder="--- Pilih Kelurahan ---"
                                    hint="Pilih kelurahan setelah memilih kecamatan" :disabled="empty($district_code)" />

                                {{-- Detail Alamat --}}
                                <div class="md:col-span-2">
                                    <x-textarea label="Detail Alamat" wire:model="detail_address"
                                        placeholder="Contoh: Jl. Mawar No. 123, RT 001/RW 002" rows="3"
                                        hint="Nama jalan, nomor rumah, RT/RW, patokan, dll" />
                                </div>
                            </div>
                        </div>

                        {{-- Layanan & Pembayaran Section --}}
                        <div class="mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                {{-- Waktu Pembayaran --}}
                                <div class="md:col-span-2">
                                    <h3 class="text-xs font-bold mb-3">Metode Pembayaran</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        {{-- Option: Bayar Saat Jemput --}}
                                        <label class="cursor-pointer">
                                            <input type="radio" name="payment_timing" value="on_pickup"
                                                wire:model.live="payment_timing" class="radio radio-accent hidden peer" />
                                            <div
                                                class="card border border-secondary/54 bg-base-100 hover:border-accent peer-checked:border-accent peer-checked:text-accent-content peer-checked:bg-accent/60 transition-all">
                                                <div class="card-body p-4">
                                                    <div class="flex items-start gap-3">
                                                        <div class="bg-warning/20 p-2 rounded">
                                                            <x-icon name="mdi.cash-check" class="w-6 h-6 text-warning" />
                                                        </div>
                                                        <div class="flex-1">
                                                            <h4 class="font-bold">Bayar Saat Jemput</h4>
                                                            <p class="text-sm">Bayar setelah cucian ditimbang</p>
                                                        </div>
                                                        @if ($payment_timing === 'on_pickup')
                                                            <div>
                                                                <x-icon name="o-check-circle" class="w-5 h-5 text-accent-content" />
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </label>

                                        {{-- Option: Bayar Saat Antar --}}
                                        <label class="cursor-pointer">
                                            <input type="radio" name="payment_timing" value="on_delivery"
                                                wire:model.live="payment_timing" class="radio radio-accent hidden peer" />
                                            <div
                                                class="card border border-secondary/54 bg-base-100 hover:border-accent peer-checked:border-accent peer-checked:text-accent-content peer-checked:bg-accent/60 transition-all">
                                                <div class="card-body p-4">
                                                    <div class="flex items-start gap-3">
                                                        <div class="bg-info/20 p-2 rounded">
                                                            <x-icon name="mdi.cash-multiple" class="w-6 h-6 text-info" />
                                                        </div>
                                                        <div class="flex-1">
                                                            <h4 class="font-bold">Bayar Saat Antar</h4>
                                                            <p class="text-sm">Bayar saat cucian diantar kembali</p>
                                                        </div>
                                                        @if ($payment_timing === 'on_delivery')
                                                            <div>
                                                                <x-icon name="o-check-circle" class="w-5 h-5 text-accent-content" />
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    {{-- Hint --}}
                                    <p class="text-xs text-base-content/58 mt-3">Pilih metode pembayaran sesuai kebutuhanmu</p>

                                    {{-- Error message --}}
                                    @error('payment_timing')
                                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Catatan Section --}}
                        <div class="mb-6">
                            <x-textarea label="Catatan Khusus" wire:model="notes"
                                placeholder="Contoh: Tolong jangan pakai pewangi, saya alergi" rows="3"
                                hint="Permintaan khusus untuk cucian Anda" />
                        </div>

                        {{-- Honeypot (Hidden) - untuk bot detection --}}
                        <input type="text" wire:model="honeypot" class="hidden" tabindex="-1"
                            autocomplete="off" />

                        {{-- Hidden form_loaded_at timestamp - untuk rate limiting --}}
                        <input type="hidden" wire:model="form_loaded_at" />

                        {{-- Info Box --}}
                        <div class="alert alert-info mb-6">
                            <x-icon name="o-information-circle" class="w-5 h-5" />
                            <div>
                                <h4 class="font-bold">Catatan Penting:</h4>
                                <ul class="text-sm list-disc list-inside mt-1">
                                    <li>Kurir akan menghubungi Anda untuk konfirmasi pickup</li>
                                    <li>Harga final akan dihitung setelah cucian ditimbang</li>
                                    <li>Estimasi waktu selesai berdasarkan layanan yang dipilih</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Submit Actions --}}
                        <x-slot:actions>
                            <x-button label="Reset Form" type="button" wire:click="resetForm"
                                class="btn-secondary btn-outline btn-sm md:btn-md lg:btn-lg" icon="o-arrow-path" />
                            <x-button label="Kirim Pesanan" type="submit"
                                class="btn-accent btn-sm md:btn-md lg:btn-lg" spinner="save"
                                icon="o-paper-airplane" />
                        </x-slot:actions>
                        </x-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
