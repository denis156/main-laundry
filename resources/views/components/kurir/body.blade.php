<body class="min-h-dvh bg-base-100">
    <div class="min-h-dvh flex flex-col">
        {{-- TOP NAV --}}
        @include('components.kurir.topnav')

        {{-- CONTENT GRID --}}
        <main class="flex-1 p-4 pt-20 pb-24 overflow-y-auto">
            {{ $slot }}
        </main>

        {{-- BOTTOM NAV --}}
        @include('components.kurir.bottomnav')
    </div>

    {{--  TOAST area --}}
    <x-toast />

    {{-- ORDER NOTIFICATION - Background polling untuk pesanan baru --}}
    @auth('courier')
        <livewire:kurir.order-notification />
    @endauth

    {{-- AUDIO RINGTONE - Persistent audio element untuk notifikasi pesanan --}}
    @persist('order-ringtone-audio')
        <audio id="order-ringtone" preload="auto">
            <source src="{{ asset('music/ringtone.wav') }}" type="audio/wav">
            Your browser does not support the audio element.
        </audio>
    @endpersist
</body>
