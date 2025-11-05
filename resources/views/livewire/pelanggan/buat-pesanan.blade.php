<section class="bg-base-100">
    <x-header icon="solar.add-circle-bold-duotone" icon-classes="text-primary w-6 h-6" title="Buat Pesanan"
        subtitle="Buat pesanan laundry baru" separator />

    <div class="space-y-4">
        {{-- Service Selection Card --}}
        <x-card class="bg-base-300 shadow" title="Pilih Layanan" subtitle="Pilih layanan laundry yang kamu inginkan"
            separator>
            <livewire:pelanggan.components.service-card :isOnOrderPage="true" />
        </x-card>

        {{-- Order Form Card --}}
        <x-card class="bg-base-300 shadow" title="Detail Pesanan" subtitle="Isi detail pesanan laundry kamu" separator>
            <x-form wire:submit="submit" no-separator class="space-y-1">
                {{-- Layanan Terpilih --}}
                <x-select label="Layanan Terpilih" icon="solar.washing-machine-bold-duotone"
                    placeholder="Pilih layanan terlebih dahulu" hint="Pilih layanan dari card di atas"
                    wire:model="service_id" :options="$this->services" option-value="id" option-label="name" />

                {{-- Payment Timing --}}
                <div class="grid grid-cols-1 gap-3">
                    {{-- Title --}}
                    <h3 class="text-xs font-bold">Metode Pembayaran</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {{-- Option: Bayar Saat Jemput --}}
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_timing" value="on_pickup" wire:model.live="payment_timing"
                                class="radio radio-accent hidden peer" />
                            <div
                                class="card border border-secondary/54 bg-base-100 hover:border-accent peer-checked:border-accent peer-checked:text-accent-content peer-checked:bg-accent transition-all">
                                <div class="card-body p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="bg-warning/20 p-2 rounded">
                                            <x-icon name="mdi.cash-check" class="w-6 h-6 text-warning" />
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold">Bayar Saat Jemput</h4>
                                            <p class="text-sm">Bayar setelah cucian ditimbang</p>
                                        </div>
                                        @if($payment_timing === 'on_pickup')
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
                            <input type="radio" name="payment_timing" value="on_delivery" wire:model.live="payment_timing"
                                class="radio radio-accent hidden peer" />
                            <div
                                class="card border border-secondary/54 bg-base-100 hover:border-accent peer-checked:border-accent peer-checked:text-accent-content peer-checked:bg-accent transition-all">
                                <div class="card-body p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="bg-info/20 p-2 rounded">
                                            <x-icon name="mdi.cash-multiple" class="w-6 h-6 text-info" />
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-bold">Bayar Saat Antar</h4>
                                            <p class="text-sm">Bayar saat cucian diantar kembali</p>
                                        </div>
                                        @if($payment_timing === 'on_delivery')
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
                    <p class="text-xs text-base-content/58">Pilih metode pembayaran sesuai kebutuhanmu</p>

                    {{-- Error message --}}
                    @error('payment_timing')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Detail Alamat --}}
                <x-textarea label="Detail Alamat" icon="solar.map-point-bold-duotone" rows="3"
                    placeholder="Contoh: Jl. Mawar No. 123, RT 001/RW 002"
                    hint="Nama jalan, nomor rumah, RT/RW, patokan, dll"
                    wire:model="detail_address" />

                {{-- Catatan Tambahan --}}
                <x-textarea label="Catatan Tambahan (Opsional)" icon="solar.document-text-bold-duotone" rows="3"
                    placeholder="Contoh: Tolong jangan pakai pewangi, saya alergi"
                    hint="Permintaan khusus untuk cucian Anda"
                    wire:model="notes" />

                {{-- Action Buttons --}}
                <x-slot:actions>
                    <div class="grid grid-cols-2 w-full gap-4">
                        <x-button label="Batal" link="{{ route('pelanggan.beranda') }}"
                            icon="solar.close-circle-bold-duotone" class="btn-secondary" />
                        <x-button label="Buat Pesanan" icon="solar.check-circle-bold-duotone" class="btn-primary"
                            type="submit" spinner="submit" />
                    </div>
                </x-slot:actions>
            </x-form>
        </x-card>
    </div>
</section>
