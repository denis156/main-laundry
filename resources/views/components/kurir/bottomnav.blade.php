<footer class="fixed bottom-0 left-0 right-0 bg-base-300 p-2 rounded-t-2xl z-50">
    <div class="flex items-center">
        <a href="{{ route('kurir.pembayaran') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1 relative">
            <x-icon name="solar.wallet-money-linear" class="h-6" />
            <span class="text-xs">Pembayaran</span>
            @if(Route::currentRouteName() === 'kurir.pembayaran')
                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-8 h-1 bg-primary rounded-full"></div>
            @endif
        </a>
        <a href="{{ route('kurir.pesanan') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1 relative">
            <x-icon name="solar.bill-list-linear" class="h-6" />
            <span class="text-xs">Pesanan</span>
            @if(in_array(Route::currentRouteName(), ['kurir.pesanan', 'kurir.pesanan.detail']))
                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-8 h-1 bg-primary rounded-full"></div>
            @endif
        </a>
        <a href="{{ route('kurir.beranda') }}" wire:navigate class="flex-1 flex flex-col items-center">
            <div class="bg-primary rounded-full p-4 mb-2 w-12 h-12 flex items-center justify-center">
                <x-icon name="solar.home-linear" class="h-6 text-primary-content" />
            </div>
        </a>
        <a href="{{ route('kurir.info') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1 relative">
            <x-icon name="solar.info-circle-linear" class="h-6" />
            <span class="text-xs">Informasi</span>
            @if(Route::currentRouteName() === 'kurir.info')
                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-8 h-1 bg-primary rounded-full"></div>
            @endif
        </a>
        <a href="{{ route('kurir.profil') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1 relative">
            <x-icon name="solar.user-circle-linear" class="h-6" />
            <span class="text-xs">Profil</span>
            @if(Route::currentRouteName() === 'kurir.profil')
                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-8 h-1 bg-primary rounded-full"></div>
            @endif
        </a>
    </div>
</footer>
