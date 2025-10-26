<footer class="fixed bottom-0 left-0 right-0 bg-primary p-2 rounded-t-2xl z-50">
    <div class="flex items-center">
        <a href="{{ route('pelanggan.beranda') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1">
            <x-icon name="{{ in_array(Route::currentRouteName(), ['pelanggan.beranda']) ? 'solar.home-bold' : 'solar.home-linear' }}"
                class="h-6 {{ in_array(Route::currentRouteName(), ['pelanggan.beranda']) ? 'text-accent' : 'text-primary-content' }}" />
            <span class="text-xs {{ in_array(Route::currentRouteName(), ['pelanggan.beranda']) ? 'text-accent' : 'text-primary-content' }} font-semibold">Beranda</span>
        </a>
        <a href="{{ route('pelanggan.pesanan') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1">
            <x-icon name="{{ in_array(Route::currentRouteName(), ['pelanggan.pesanan', 'pelanggan.pesanan.detail']) ? 'solar.bill-list-bold' : 'solar.bill-list-linear' }}"
                class="h-6 {{ in_array(Route::currentRouteName(), ['pelanggan.pesanan', 'pelanggan.pesanan.detail']) ? 'text-accent' : 'text-primary-content' }}" />
            <span class="text-xs {{ in_array(Route::currentRouteName(), ['pelanggan.pesanan', 'pelanggan.pesanan.detail']) ? 'text-accent' : 'text-primary-content' }} font-semibold">Pesanan</span>
        </a>
        <a href="{{ route('pelanggan.buat-pesanan') }}" wire:navigate class="flex-1 flex flex-col items-center">
            <div class="bg-accent rounded-xl p-4 mb-2 w-14 h-10 flex items-center justify-center hover:scale-110 transition-transform">
                <x-icon name="solar.add-circle-linear" class="h-6 text-accent-content" />
            </div>
        </a>
        <a href="{{ route('pelanggan.info') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1">
            <x-icon name="{{ Route::currentRouteName() === 'pelanggan.info' ? 'solar.info-circle-bold' : 'solar.info-circle-linear' }}"
                class="h-6 {{ Route::currentRouteName() === 'pelanggan.info' ? 'text-accent' : 'text-primary-content' }}" />
            <span class="text-xs {{ Route::currentRouteName() === 'pelanggan.info' ? 'text-accent' : 'text-primary-content' }} font-semibold">Informasi</span>
        </a>
        <a href="{{ route('pelanggan.profil') }}" wire:navigate class="flex-1 flex flex-col items-center gap-1">
            <x-icon name="{{ Route::currentRouteName() === 'pelanggan.profil' ? 'solar.user-circle-bold' : 'solar.user-circle-linear' }}"
                class="h-6 {{ Route::currentRouteName() === 'pelanggan.profil' ? 'text-accent' : 'text-primary-content' }}" />
            <span class="text-xs {{ Route::currentRouteName() === 'pelanggan.profil' ? 'text-accent' : 'text-primary-content' }} font-semibold">Profil</span>
        </a>
    </div>
</footer>
