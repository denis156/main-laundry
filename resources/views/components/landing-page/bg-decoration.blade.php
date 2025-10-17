@props([
    'topRight' => 'dus-detergent.svg',
    'bottomLeft' => 'botol-pewangi.svg',
    'topLeft' => 'tutup-mesin-cuci.svg',
    'bottomRight' => 'mesin-cuci.svg',
])

<div class="absolute inset-0 pointer-events-none overflow-visible z-0">
    {{-- Top Right --}}
    <img src="{{ asset('grafis/' . $topRight) }}" alt=""
        class="absolute top-24 md:top-32 lg:top-40 right-8 md:right-12 lg:right-20 w-24 md:w-32 lg:w-44 opacity-10 animate-float-slow" />

    {{-- Bottom Left --}}
    <img src="{{ asset('grafis/' . $bottomLeft) }}" alt=""
        class="absolute bottom-20 left-10 w-16 md:w-24 lg:w-36 opacity-10 animate-float-medium" />

    {{-- Top Left --}}
    <img src="{{ asset('grafis/' . $topLeft) }}" alt=""
        class="absolute top-40 md:top-48 lg:top-56 left-8 md:left-12 lg:left-20 w-20 md:w-24 lg:w-36 opacity-10 animate-float-fast" />

    {{-- Bottom Right --}}
    <img src="{{ asset('grafis/' . $bottomRight) }}" alt=""
        class="absolute bottom-32 right-20 w-24 md:w-32 lg:w-44 opacity-10 animate-float-slow"
        style="animation-delay: 2s;" />
</div>
