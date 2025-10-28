<section class="bg-base-100">
    <x-header icon="solar.user-circle-bold-duotone" icon-classes="text-primary w-6 h-6" title="Profil"
        subtitle="Kelola Profil & Akun Anda" separator>
        <x-slot:actions>
            <x-button label="Keluar" wire:click="logout" class="btn-sm btn-error" icon="solar.logout-linear" />
        </x-slot:actions>
    </x-header>

    <div class="space-y-4">
        {{-- Avatar Section --}}
        <x-card class="bg-base-300" title="Foto Profil" subtitle="Upload dan kelola foto profil Anda" shadow separator>
            <div class="flex flex-col items-center gap-2 text-center">
                <h3 class="text-lg font-bold">{{ $this->courier->name }}</h3>
                <p class="text-sm text-secondary">{{ $this->courier->email }}</p>

                {{-- Avatar with Upload --}}
                <x-file wire:model="avatar" accept="image/png, image/jpeg, image/jpg" change-text="Ubah Foto">
                    <div class="ring-accent ring-offset-base-100 w-40 h-40 rounded-full ring-2 ring-offset-2">
                        <img src="{{ $this->courier->getFilamentAvatarUrl() }}" class="w-40 h-40 rounded-full"
                            alt="Avatar {{ $this->courier->name }}" />
                    </div>
                </x-file>
                <p class="text-sm text-secondary">Klik gambar untuk mengubah foto profil maksimal 2MB.</p>
            </div>
        </x-card>

        {{-- Form Update Profil & Password --}}
        <x-card class="bg-base-300" title="Informasi Akun" subtitle="Kelola data pribadi dan keamanan akun Anda" shadow
            separator>
            <x-form wire:submit="save">
                <x-input label="Nama Lengkap" wire:model.blur="name" icon="solar.user-bold-duotone" />
                <x-input label="Email" wire:model.blur="email" type="email" icon="solar.letter-bold-duotone" />
                <x-input label="No. Telepon" wire:model.blur="phone" prefix="+62"
                    hint="Bisa tulis dengan 08 atau langsung 8" maxlength="15" />
                <x-input label="Plat Nomor" wire:model.blur="vehicle_number"
                    icon="solar.transmission-bold-duotone" />

                <x-password label="Password Lama" wire:model.blur="current_password" type="password"
                    icon="solar.lock-bold-duotone" hint="Kosongkan jika tidak ingin mengubah password" right />
                <x-password label="Password Baru" wire:model.blur="new_password" type="password"
                    icon="solar.lock-keyhole-bold-duotone" :hint="empty($current_password) ? 'Isi password lama terlebih dahulu' : 'Minimal 8 karakter'" right :readonly="empty($current_password)" />
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
