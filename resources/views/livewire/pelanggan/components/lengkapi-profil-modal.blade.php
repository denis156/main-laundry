{{-- Modal Lengkapi Profil --}}
<x-modal wire:model="showModal" title="Profil Belum Lengkap" subtitle="Halo, {{ $customerName ?: 'Pelanggan' }}!"
    class="modal-bottom sm:modal-middle" persistent separator>
    <div class="space-y-4">
        {{-- Icon Warning --}}
        <div class="flex justify-center">
            <div class="p-4 bg-warning/20 rounded-full">
                <x-icon name="solar.danger-bold" class="w-12 h-12 text-warning" />
            </div>
        </div>

        {{-- Message --}}
        <div class="text-center">
            <p class="text-base font-medium mb-4">
                Sebelum melanjutkan, silakan lengkapi data profil Anda terlebih dahulu.
            </p>

            {{-- Required Data --}}
            <div class="text-left bg-base-200 rounded-lg p-4">
                <p class="text-sm font-semibold mb-3">Data yang diperlukan:</p>
                <ul class="space-y-2">
                    <li class="flex items-start gap-2">
                        <x-icon name="solar.map-point-bold" class="w-4 h-4 text-warning mt-0.5 shrink-0" />
                        <span class="text-sm">Kecamatan dan Kelurahan tempat tinggal</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-icon name="solar.home-smile-bold" class="w-4 h-4 text-warning mt-0.5 shrink-0" />
                        <span class="text-sm">Detail alamat lengkap (nomor rumah, RT/RW, dll)</span>
                    </li>
                </ul>
                <p class="text-xs text-base-content/60 mt-3">
                    <x-icon name="solar.info-circle-linear" class="w-3 h-3 inline" />
                    Data alamat lengkap diperlukan untuk pengantaran dan penjemputan cucian
                </p>
            </div>
        </div>
    </div>

    <x-slot:actions>
        <div class="grid grid-cols-2 gap-2 w-full">
            @if ($redirectFrom === 'buat-pesanan')
                <x-button label="Kembali" wire:click="close" class="btn-outline" icon="solar.arrow-left-linear" />
            @else
                <x-button label="Batal" wire:click="close" class="btn-outline" icon="solar.close-circle-bold" />
            @endif
            <x-button label="Lengkapi Profil" wire:click="goToProfil" class="btn-primary"
                icon="solar.user-circle-bold-duotone" spinner="goToProfil" />
        </div>
    </x-slot:actions>
</x-modal>
