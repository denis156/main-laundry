{{-- Layanan Kami --}}

<div class="grid grid-cols-2 gap-4">
    @foreach ($this->services as $index => $service)
        <a href="{{ route('pelanggan.buat-pesanan') }}" wire:navigate>
            <x-card
                class="bg-base-100 border-t-6 {{ $index % 2 === 0 ? 'border-r-6' : 'border-l-6' }} border-secondary shadow p-0 transition-all active:border-0 active:scale-96 cursor-pointer"
                body-class="space-y-2 text-align-center relative z-10">
                {{-- Logo Background --}}
                <div class="absolute inset-0 opacity-18 flex items-center justify-center pointer-events-none p-4">
                    <img src="{{ asset('image/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>

                <h2 class="font-bold text-base-content">{{ $service->name }}</h2>
                <p class="text-xs text-base-content/80">{{ $this->formatDuration($service->duration_days) }}</p>
                <div class="divider my-1"></div>
                <div class="flex items-baseline justify-between">
                    <span class="text-xs text-base-content">Harga</span>
                    <span class="text-xs font-bold text-accent">{{ $this->formatPrice($service->price_per_kg) }}</span>
                </div>
            </x-card>
        </a>
    @endforeach
</div>
