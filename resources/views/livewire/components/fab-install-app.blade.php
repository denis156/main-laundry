<div>
    @if ($showFab && !$isInstalled)
        <div class="fixed bottom-10 right-10 z-50">
            {{-- Ring effect yang berkedip di belakang --}}
            <div class="absolute inset-0 rounded-full bg-accent opacity-30 animate-pulse"></div>
            <div class="absolute inset-0 rounded-full bg-accent opacity-20 animate-ping"></div>

            {{-- Tombol utama --}}
            <div class="relative tooltip tooltip-open tooltip-accent font-semibold text-4xl" data-tip="Install">
                <x-button class="btn-accent btn-circle btn-lg shadow-xl" wire:click="openConfirmationInstallApp"
                    spinner="openConfirmationInstallApp">
                    <x-icon name="solar.download-minimalistic-bold-duotone" class="w-7 h-7"
                        wire:loading.remove="openConfirmationInstallApp" />
                </x-button>
            </div>

            <x-modal wire:model="installWebApp" title="Install Aplikasi"
                class="backdrop-blur-sm modal-bottom sm:modal-middle">
                <div class="flex flex-col items-center text-center space-y-6 py-4">
                    {{-- Logo Aplikasi --}}
                    <div class="relative flex items-center justify-center">
                        <div class="absolute inset-0 bg-accent rounded-full blur-xl"></div>
                        <img src="{{ asset('image/app.png') }}" alt="Main Laundry App"
                            class="relative w-28 h-28 rounded-3xl shadow-2xl object-contain">
                    </div>

                    {{-- Judul dan Deskripsi --}}
                    <div class="space-y-2">
                        <h3 class="text-2xl font-bold text-primary">{{ $appName }}</h3>
                        <p class="text-sm text-secondary font-medium">Versi {{ $appVersion }}</p>
                        <p class="text-base-content/70 max-w-sm">
                            Install aplikasi ini ke perangkat Anda untuk akses yang lebih cepat dan pengalaman yang
                            lebih baik
                        </p>
                    </div>
                </div>

                <x-slot:actions>
                    <x-button label="Install Sekarang" wire:click="installPWA" class="btn-accent btn-block"
                        spinner="installPWA" />
                </x-slot:actions>
            </x-modal>
        </div>
    @endif
</div>
