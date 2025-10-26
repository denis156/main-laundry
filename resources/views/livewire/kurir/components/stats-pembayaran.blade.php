 {{-- Stats Cards --}}
 <div class="stats stats-vertical lg:stats-horizontal shadow-lg hover:shadow-xl transition-shadow w-full">
     <div class="stat bg-warning">
         <div class="stat-figure text-base-content">
             <x-icon name="solar.wallet-bold-duotone" class="inline-block h-8 stroke-current" />
         </div>
         <div class="stat-title text-base-content">Belum Bayar</div>
         <div class="stat-value text-base-content">{{ $this->stats['unpaid_count'] }}</div>
         <div class="stat-desc text-base-content">Menunggu pembayaran</div>
     </div>
     <div class="stat bg-success">
         <div class="stat-figure text-base-content">
             <x-icon name="solar.check-circle-bold-duotone" class="inline-block h-8 stroke-current" />
         </div>
         <div class="stat-title text-base-content">Lunas</div>
         <div class="stat-value text-base-content">{{ $this->stats['paid_count'] }}</div>
         <div class="stat-desc text-base-content">Sudah dibayar</div>
     </div>
 </div>
