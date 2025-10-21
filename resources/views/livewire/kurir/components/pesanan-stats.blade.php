{{-- Stats Cards dengan Polling --}}
<div class="grid grid-cols-2 gap-4" wire:poll.25s.visible>
    {{-- Pending --}}
    <div class="card bg-secondary text-secondary-content shadow-lg">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs opacity-80">Pending</p>
                    <p class="text-2xl font-bold">{{ $this->stats['pending_count'] }}</p>
                </div>
                <x-icon name="solar.clock-circle-bold-duotone" class="w-10 h-10 opacity-50" />
            </div>
            <div class="text-xs mt-2 font-semibold">
                Konfirmasi?
            </div>
        </div>
    </div>

    {{-- Active --}}
    <div class="card bg-primary text-primary-content shadow-lg">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs opacity-80">Aktif</p>
                    <p class="text-2xl font-bold">{{ $this->stats['active_count'] }}</p>
                </div>
                <x-icon name="solar.rocket-bold-duotone" class="w-10 h-10 opacity-50" />
            </div>
            <div class="text-xs mt-2 font-semibold">
                Dalam Proses
            </div>
        </div>
    </div>

    {{-- Delivered --}}
    <div class="card bg-success text-success-content shadow-lg">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs opacity-80">Selesai</p>
                    <p class="text-2xl font-bold">{{ $this->stats['delivered_count'] }}</p>
                </div>
                <x-icon name="solar.star-bold-duotone" class="w-10 h-10 opacity-50" />
            </div>
            <div class="text-xs mt-2 font-semibold">
                Selesai
            </div>
        </div>
    </div>

    {{-- Cancelled --}}
    <div class="card bg-error text-error-content shadow-lg">
        <div class="card-body p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs opacity-80">Batal</p>
                    <p class="text-2xl font-bold">{{ $this->stats['cancelled_count'] }}</p>
                </div>
                <x-icon name="solar.close-circle-bold-duotone" class="w-10 h-10 opacity-50" />
            </div>
            <div class="text-xs mt-2 font-semibold">
                Batal
            </div>
        </div>
    </div>
</div>
