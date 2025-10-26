<footer class="fixed bottom-0 left-0 right-0 bg-primary p-2 rounded-t-2xl z-50">
    <div class="flex items-center">
        <a href="{{ route('kurir.beranda') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1">
            <x-icon name="{{ Route::currentRouteName() === 'kurir.beranda' ? 'solar.home-bold' : 'solar.home-linear' }}"
                class="h-6 {{ Route::currentRouteName() === 'kurir.beranda' ? 'text-accent' : 'text-primary-content' }}" />
            <span class="text-xs {{ Route::currentRouteName() === 'kurir.beranda' ? 'text-accent' : 'text-primary-content' }} font-semibold">Beranda</span>
        </a>
        <a href="{{ route('kurir.pembayaran') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1">
            <x-icon name="{{ Route::currentRouteName() === 'kurir.pembayaran' ? 'solar.wallet-money-bold' : 'solar.wallet-money-linear' }}"
                class="h-6 {{ Route::currentRouteName() === 'kurir.pembayaran' ? 'text-accent' : 'text-primary-content' }}" />
            <span class="text-xs {{ Route::currentRouteName() === 'kurir.pembayaran' ? 'text-accent' : 'text-primary-content' }} font-semibold">Pembayaran</span>
        </a>
        <a href="{{ route('kurir.pesanan') }}" wire:navigate class="flex-1 flex flex-col items-center">
            <div class="bg-accent rounded-xl p-4 mb-2 w-14 h-10 flex items-center justify-center hover:scale-110 transition-transform">
                <x-icon name="solar.bill-list-linear" class="h-6 text-accent-content" />
            </div>
        </a>
        <a href="{{ route('kurir.info') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1">
            <x-icon name="{{ Route::currentRouteName() === 'kurir.info' ? 'solar.info-circle-bold' : 'solar.info-circle-linear' }}"
                class="h-6 {{ Route::currentRouteName() === 'kurir.info' ? 'text-accent' : 'text-primary-content' }}" />
            <span class="text-xs {{ Route::currentRouteName() === 'kurir.info' ? 'text-accent' : 'text-primary-content' }} font-semibold">Informasi</span>
        </a>
        <a href="{{ route('kurir.profil') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1">
            <x-icon name="{{ Route::currentRouteName() === 'kurir.profil' ? 'solar.user-circle-bold' : 'solar.user-circle-linear' }}"
                class="h-6 {{ Route::currentRouteName() === 'kurir.profil' ? 'text-accent' : 'text-primary-content' }}" />
            <span class="text-xs {{ Route::currentRouteName() === 'kurir.profil' ? 'text-accent' : 'text-primary-content' }} font-semibold">Profil</span>
        </a>
    </div>
</footer>
