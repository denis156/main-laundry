<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth no-scrollbar">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Main Laundry - Layanan laundry terpercaya dengan kualitas terbaik">

    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('image/favico.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="antialiased font-sans">
    <!-- Drawer Wrapper (UI Component Container) -->
    <div class="drawer drawer-end">
        <!-- Hidden Checkbox for Drawer Toggle -->
        <input id="main-drawer" type="checkbox" class="drawer-toggle" />

        <!-- Drawer Content (Main Page) -->
        <div class="drawer-content flex flex-col">
            <!-- Navbar -->
            <header class="sticky top-0 z-50">
                <nav class="navbar bg-base-100 shadow-sm px-8" aria-label="Main navigation">
                    <!-- Logo -->
                    <div class="navbar-start">
                        <a href="#beranda" aria-label="Main Laundry Home">
                            <img src="{{ asset('image/logo.png') }}" alt="{{ config('app.name') }}" class="h-14 w-auto">
                        </a>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="navbar-center hidden lg:flex">
                        <ul class="menu menu-horizontal px-1">
                            <li><a href="#beranda">Beranda</a></li>
                            <li><a href="#layanan">Layanan</a></li>
                            <li><a href="#faq">FAQ</a></li>
                            <li><a href="#pesan">Pesan</a></li>
                        </ul>
                    </div>

                    <!-- Desktop Auth Buttons + Mobile Drawer Button -->
                    <div class="navbar-end gap-2">
                        <!-- Desktop Auth Buttons (hidden di mobile) -->
                        <a href="{{ route('pelanggan.login') }}" class="hidden lg:inline-flex btn btn-ghost btn-sm">Masuk</a>
                        <a href="#pesan" class="hidden lg:inline-flex btn btn-primary btn-sm">Pesan</a>

                        <!-- Mobile Drawer Button (hidden di desktop) -->
                        <button type="button" aria-label="open sidebar" class="btn btn-square btn-ghost lg:hidden">
                            <label for="main-drawer" class="cursor-pointer">
                                <x-icon name="solar.hamburger-menu-linear" class="h-5 w-5" />
                            </label>
                        </button>
                    </div>
                </nav>
            </header>

            <!-- Content -->
            <!-- Beranda/Hero Section -->
            <x-landing-page.beranda />

            <!-- Layanan Section -->
            <x-landing-page.layanan />

            <!-- FAQ Section -->
            <x-landing-page.faq />

            <!-- Pesan Section -->
            <livewire:landing-page.pesan />
            <!-- End Content -->

            <!-- Footer -->
            <x-landing-page.footer />
        </div>

        <!-- Drawer Side (Mobile Sidebar) -->
        <div class="drawer-side z-50">
            <label for="main-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <nav class="menu bg-base-100 min-h-dvh w-80 p-4 flex flex-col" aria-label="Mobile navigation">
                <!-- Sidebar Logo/Header -->
                <div class="mb-4">
                    <a href="#beranda" class="block px-4">
                        <img src="{{ asset('image/logo.png') }}" alt="{{ config('app.name') }}" class="h-14 w-auto">
                    </a>
                </div>

                <!-- Sidebar Menu -->
                <ul class="space-y-2">
                    <li><a href="#beranda" class="btn btn-ghost w-full justify-start">Beranda</a></li>
                    <li><a href="#layanan" class="btn btn-ghost w-full justify-start">Layanan</a></li>
                    <li><a href="#faq" class="btn btn-ghost w-full justify-start">FAQ</a></li>
                    <li><a href="#pesan" class="btn btn-ghost w-full justify-start">Pesan</a></li>
                </ul>

                <!-- Sidebar Auth Buttons -->
                <div class="mt-auto pt-4 space-y-2">
                    <a href="{{ route('pelanggan.login') }}" class="btn btn-ghost w-full">Masuk</a>
                    <a href="#pesan" class="btn btn-primary w-full">Pesan</a>
                </div>
            </nav>
        </div>
    </div>

    @livewireScripts

</body>

</html>
