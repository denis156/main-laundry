<div>
    {{-- Stats Cards --}}
    <div class="stats stats-vertical lg:stats-horizontal shadow-lg hover:shadow-xl transition-shadow w-full">
        <div class="stat bg-success">
            <div class="stat-figure text-base-content">
                <x-icon name="solar.bill-check-bold-duotone" class="inline-block h-8 stroke-current" />
            </div>
            <div class="stat-title text-base-content">Selesai</div>
            <div class="stat-value text-base-content">{{ $this->stats['delivered_count'] }}</div>
            <div class="stat-desc text-base-content">Sudah dikirim semua</div>
        </div>
        <div class="stat bg-error">
            <div class="stat-figure text-base-content">
                <x-icon name="solar.bill-cross-bold-duotone" class="inline-block h-8 stroke-current" />
            </div>
            <div class="stat-title text-base-content">Batal</div>
            <div class="stat-value text-base-content">{{ $this->stats['cancelled_count'] }}</div>
            <div class="stat-desc text-base-content">Pesanan dibatalkan</div>
        </div>
    </div>
</div>
