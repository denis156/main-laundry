{{-- Stats Cards --}}
<div class="stats stats-vertical lg:stats-horizontal shadow w-full">
    <div class="stat bg-primary">
        <div class="stat-figure text-base-content">
            <x-icon name="solar.clock-circle-bold-duotone" class="inline-block h-8 stroke-current" />
        </div>
        <div class="stat-title text-base-content">Pesanan Aktif</div>
        <div class="stat-value text-base-content">{{ $this->activeOrdersCount }}</div>
        <div class="stat-desc text-base-content">Sedang diproses</div>
    </div>
    <div class="stat bg-success">
        <div class="stat-figure text-base-content">
            <x-icon name="solar.check-circle-bold-duotone" class="inline-block h-8 stroke-current" />
        </div>
        <div class="stat-title text-base-content">Selesai</div>
        <div class="stat-value text-base-content">{{ $this->completedOrdersCount }}</div>
        <div class="stat-desc text-base-content">Total pesanan selesai</div>
    </div>
</div>
