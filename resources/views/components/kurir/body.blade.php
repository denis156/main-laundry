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
</body>
