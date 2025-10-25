<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth no-scrollbar">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('image/favico.svg') }}">

    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#3b82f6">

    {{-- Android PWA --}}
    <meta name="mobile-web-app-capable" content="yes">

    {{-- iOS PWA (PENTING!) --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Kurir ML">

    {{-- Apple Touch Icons (berbagai ukuran untuk iOS) --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('image/app.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('image/app.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('image/app.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('image/app.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('image/app.png') }}">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/kurir.js'])

    {{-- Pass courier POS area data to JavaScript for filtering --}}
    @auth('courier')
        @php
            $courier = Auth::guard('courier')->user();
            $assignedPos = $courier?->assignedPos;
            $posArea = $assignedPos && !empty($assignedPos->area) ? $assignedPos->area : [];
        @endphp
        <script>
            // Global config untuk filtering transaction area
            window.COURIER_POS_AREA = @json($posArea);
            window.COURIER_POS_ID = @json($assignedPos?->id);
        </script>
    @endauth

    {{-- Kurir Style --}}
    @include('components.kurir.style')

    {{-- Cdn Chart --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

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

    {{-- AUDIO RINGTONE - Persistent audio element untuk notifikasi pesanan --}}
    @persist('order-ringtone-audio')
        <audio id="order-ringtone" preload="auto">
            <source src="{{ asset('music/ringtone.wav') }}" type="audio/wav">
            Your browser does not support the audio element.
        </audio>
    @endpersist

</body>

@include('components.kurir.script')

</html>
