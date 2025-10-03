<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark" class="scroll-smooth no-scrollbar">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Main Laundry - Layanan laundry terpercaya dengan kualitas terbaik">

    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    {{-- AOS CSS CDN --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-dvh min-w-dvw antialiased bg-base-100 font-sans overflow-y-auto no-scrollbar">

    {{-- Navbar --}}
    <livewire:landing-page.component.navbar />

    {{-- Main --}}
    <main class="min-h-dvh min-w-dvw">
        {{-- Section Beranda --}}
        <livewire:landing-page.beranda />

        {{-- Section Layanan --}}
        <livewire:landing-page.layanan />

        {{-- Section Harga --}}
        <livewire:landing-page.harga />

        {{-- Section Promo --}}
        <livewire:landing-page.promo />

        {{-- Section FAQ --}}
        <livewire:landing-page.faq />

        {{-- Section Tentang --}}
        <livewire:landing-page.tentang />

        {{-- Section Testimoni --}}
        <livewire:landing-page.testimoni />

        {{-- Section Lokasi --}}
        <livewire:landing-page.lokasi />

        {{-- Section Reservasi --}}
        <livewire:landing-page.reservasi />
    </main>

    {{-- Footer --}}
    <livewire:landing-page.component.footer />

    {{--  TOAST area --}}
    <x-toast />

</body>

{{-- AOS JS CDN --}}
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

{{-- AOS INIT --}}
<script>
    AOS.init({
        once: true, // Animasi hanya sekali
    });
    AOS.refreshHard();
</script>

</html>
