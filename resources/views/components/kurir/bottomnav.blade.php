<footer class="fixed bottom-0 left-0 right-0 bg-primary p-2 rounded-t-2xl z-50">
    <div class="flex items-center space-x-1">
        <a href="{{ route('kurir.beranda') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1">
            <x-icon name="{{ Route::currentRouteName() === 'kurir.beranda' ? 'solar.home-bold' : 'solar.home-linear' }}"
                class="h-5 {{ Route::currentRouteName() === 'kurir.beranda' ? 'text-accent' : 'text-primary-content' }}" />
            <span class="text-xs {{ Route::currentRouteName() === 'kurir.beranda' ? 'text-accent' : 'text-primary-content' }} font-normal">Beranda</span>
        </a>
        <a href="{{ route('kurir.pembayaran') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1">
            <x-icon name="{{ in_array(Route::currentRouteName(), ['kurir.pembayaran', 'kurir.pembayaran.detail']) ? 'solar.wallet-money-bold' : 'solar.wallet-money-linear' }}"
                class="h-5 {{ in_array(Route::currentRouteName(), ['kurir.pembayaran', 'kurir.pembayaran.detail']) ? 'text-accent' : 'text-primary-content' }}" />
            <span class="text-xs {{ in_array(Route::currentRouteName(), ['kurir.pembayaran', 'kurir.pembayaran.detail']) ? 'text-accent' : 'text-primary-content' }} font-normal">Pembayaran</span>
        </a>
        <a href="{{ route('kurir.pesanan') }}" wire:navigate class="flex-1 flex flex-col items-center">
            <div class="bg-accent rounded-xl p-4 mb-2 w-14 h-10 flex items-center justify-center {{ in_array(Route::currentRouteName(), ['kurir.pesanan', 'kurir.pesanan.detail']) ? '' : 'border-l-4 border-r-4 border-r-accent-content border-l-accent-content' }}">
                <x-icon name="solar.bill-list-linear" class="h-5 text-accent-content" />
            </div>
        </a>
        <a href="{{ route('kurir.info') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1">
            <x-icon name="{{ Route::currentRouteName() === 'kurir.info' ? 'solar.info-circle-bold' : 'solar.info-circle-linear' }}"
                class="h-5 {{ Route::currentRouteName() === 'kurir.info' ? 'text-accent' : 'text-primary-content' }}" />
            <span class="text-xs {{ Route::currentRouteName() === 'kurir.info' ? 'text-accent' : 'text-primary-content' }} font-normal">Informasi</span>
        </a>
        <a href="{{ route('kurir.profil') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1">
            <x-icon name="{{ Route::currentRouteName() === 'kurir.profil' ? 'solar.user-circle-bold' : 'solar.user-circle-linear' }}"
                class="h-5 {{ Route::currentRouteName() === 'kurir.profil' ? 'text-accent' : 'text-primary-content' }}" />
            <span class="text-xs {{ Route::currentRouteName() === 'kurir.profil' ? 'text-accent' : 'text-primary-content' }} font-normal">Profil</span>
        </a>
    </div>
</footer>
