{{-- Stats Cards --}}
<div class="stats stats-vertical lg:stats-horizontal shadow w-full">
    <div class="stat bg-success">
        <div class="stat-figure text-base-content">
            <x-icon name="solar.bill-check-bold-duotone" class="inline-block h-8 stroke-current" />
        </div>
        <div class="stat-title text-base-content">Selesai</div>
        <div class="stat-value text-base-content">{{ $this->completedTransactions }}</div>
        <div class="stat-desc text-base-content">Sudah dikirim semua</div>
    </div>
    <div class="stat bg-info">
        <div class="stat-figure text-base-content">
            <x-icon name="solar.chat-round-check-bold-duotone" class="inline-block h-8 stroke-current" />
        </div>
        <div class="stat-title text-base-content">Terkonfirmasi</div>
        <div class="stat-value text-base-content">{{ $this->confirmedTransactions }}</div>
        <div class="stat-desc text-base-content">Siap dikerjakan segera</div>
    </div>
</div>
