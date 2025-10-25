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
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">

    {{-- Apple Touch Icons (berbagai ukuran untuk iOS) --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('image/app.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('image/app.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('image/app.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('image/app.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('image/app.png') }}">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-dvh bg-base-100">
    <div class="min-h-dvh flex flex-col">
        {{-- TOP NAV --}}
        <nav class="fixed top-0 left-0 right-0 navbar bg-primary text-primary-content px-4 z-50 shadow-lg">

        </nav>
        {{-- CONTENT GRID --}}
        <main class="flex-1 p-4 pt-20 pb-24 overflow-y-auto">
            {{ $slot }}
        </main>
        <footer class="fixed bottom-0 left-0 right-0 bg-primary p-2 rounded-t-2xl z-50">
            <div class="flex items-center">
                {{-- <a href="{{ route('pelanggan.beranda') }}" wire:navigate
                    class="flex-1 flex flex-col items-center gap-1 relative">
                    <x-icon name="solar.home-linear" class="h-6 text-primary-content" />
                    <span class="text-xs text-primary-content font-semibold">Beranda</span>
                    @if (Route::currentRouteName() === 'pelanggan.beranda')
                        <div
                            class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-8 h-1 bg-accent rounded-full">
                        </div>
                    @endif
                </a> --}}
                <a href="{{ route('pelanggan.beranda') }}" wire:navigate class="flex-1 flex flex-col items-center">
                    <div class="bg-accent rounded-full p-4 mb-2 w-12 h-12 flex items-center justify-center">
                        <x-icon name="solar.home-linear" class="h-6 text-accent-content" />
                    </div>
                </a>
            </div>
        </footer>
    </div>

    {{--  TOAST area --}}
    <x-toast />

</body>

</html>
