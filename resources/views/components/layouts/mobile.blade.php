<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth no-scrollbar">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('image/favico.svg') }}">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- PWA Head --}}
    @PwaHead

    {{-- Kurir Style --}}
    @include('components.kurir.style')
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

    {{-- Register Service Worker --}}
    @RegisterServiceWorkerScript
</body>

@include('components.kurir.script')

</html>
