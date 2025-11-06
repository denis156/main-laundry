<section class="bg-base-100">
    <x-header icon="solar.user-circle-bold-duotone" icon-classes="text-primary w-6 h-6" title="Profil Saya"
        subtitle="Kelola akun dan pengaturan {{ config('app.name') }}" separator>
        <x-slot:actions>
            <x-button label="Keluar" wire:click="logout" class="btn-sm btn-error" icon="solar.logout-linear" />
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Avatar Section --}}
        <x-card class="bg-base-300" title="Foto Profil" subtitle="Upload dan kelola foto profil Anda" shadow separator>
            <div class="flex flex-col items-center gap-2 text-center">
                <h3 class="text-lg font-bold">{{ $this->customer->name }}</h3>
                <p class="text-sm text-secondary">{{ $this->customer->email }}</p>

                {{-- Avatar with Upload --}}
                <x-file wire:model="avatar" accept="image/png, image/jpeg, image/jpg" change-text="Ubah Foto">
                    <div class="ring-accent ring-offset-base-100 w-40 h-40 rounded-full ring-2 ring-offset-2">
                        <img src="{{ $this->customer->getFilamentAvatarUrl() }}" class="w-40 h-40 rounded-full"
                            alt="Avatar {{ $this->customer->name }}" />
                    </div>
                </x-file>
                <p class="text-sm text-secondary">Klik gambar untuk mengubah foto profil maksimal 2MB.</p>
            </div>
        </x-card>

        
        {{-- Form Update Profil & Password --}}
        <x-card class="bg-base-300" title="Informasi Akun" subtitle="Kelola data pribadi dan keamanan akun Anda" shadow separator>
            <x-form wire:submit="save">
                <x-input label="Nama Lengkap" wire:model.blur="name" icon="solar.user-bold-duotone" />
                <x-input label="Email" wire:model.blur="email" type="email" icon="solar.letter-bold-duotone" />
                <x-input label="No. Telepon" wire:model.blur="phone" prefix="+62"
                    hint="Bisa tulis dengan 08 atau langsung 8" maxlength="15" />

                <div class="divider">Alamat</div>

                @if(!empty($district_code) && !empty($village_code))
                    <p class="text-sm text-base-content/70 mb-2">Wilayah: Kota Kendari, Sulawesi Tenggara</p>
                @endif

                <x-select label="Kecamatan" wire:model.live="district_code"
                    :options="$districts" option-value="code" option-label="name"
                    icon="solar.map-point-bold-duotone" hint="Pilih kecamatan tempat tinggal" placeholder="Pilih kecamatan" />

                <x-select label="Desa/Kelurahan" wire:model.live="village_code"
                    :options="$villages" option-value="code" option-label="name"
                    icon="solar.home-bold-duotone" hint="Pilih desa/kelurahan tempat tinggal"
                    placeholder="Pilih desa/kelurahan" :disabled="empty($district_code)" />
                <x-textarea label="Detail Alamat" wire:model.blur="detail_address"
                    icon="solar.document-text-bold-duotone" hint="Detail tambahan alamat (RT/RW, patokan, dll)" rows="2" />
                <x-textarea label="Alamat Lengkap" wire:model="computed_address" readonly
                    icon="solar.building-bold-duotone" hint="Alamat otomatis ter-generate"
                    class="textarea textarea-bordered textarea-disabled" rows="2" />

                <div class="divider">Keamanan</div>

                <x-password label="Password Lama" wire:model.blur="current_password" type="password"
                    icon="solar.lock-bold-duotone" hint="Kosongkan jika tidak ingin mengubah password" right />
                <x-password label="Password Baru" wire:model.blur="new_password" type="password"
                    icon="solar.lock-keyhole-bold-duotone" :hint="empty($current_password) ? 'Isi password lama terlebih dahulu' : 'Minimal 6 karakter'" right :readonly="empty($current_password)" />
                <x-password label="Konfirmasi Password Baru" wire:model.blur="new_password_confirmation" type="password"
                    icon="solar.lock-keyhole-bold-duotone" :hint="empty($current_password) ? 'Isi password lama terlebih dahulu' : 'Ulangi password baru'" right :readonly="empty($current_password)" />

                <x-slot:actions>
                    <x-button label="Batal" class="btn-secondary btn-outline" wire:click="cancel"
                        icon="solar.close-circle-bold-duotone" :disabled="!$this->hasChanges" />
                    <x-button label="Simpan Perubahan" class="btn-primary" type="submit" spinner="save"
                        icon="solar.diskette-bold-duotone" :disabled="!$this->hasChanges" />
                </x-slot:actions>
            </x-form>
        </x-card>
    </div>
</section>
