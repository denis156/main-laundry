<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Main Laundry - Layanan laundry terpercaya dengan kualitas terbaik">

    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-dvh min-w-dvw antialiased bg-base-100 font-sans">

    {{-- Navbar --}}
    <header
        class="navbar bg-base-300/80 backdrop-blur-md border-b-4 border-b-primary shadow-lg rounded-b-2xl px-8 sticky top-0 z-10">
        <div class="flex-1">
            <div class="flex items-center text-primary gap-1">
                <x-icon class="h-6 md:h-8 lg:h-10" name="solar.washing-machine-minimalistic-bold-duotone" />
                <h1 class="text-md md:text-lg lg:text-2xl font-bold">{{ config('app.name') }}</h1>
            </div>
        </div>
        {{-- Desktop Menu (lg dan ke atas) --}}
        <div class="flex-none hidden lg:block">
            <div class="flex items-center gap-6">
                <a href="#beranda"
                    class="link link-primary link-hover text-xs md:text-sm lg:text-md font-semibold">Beranda</a>
                <a href="#layanan"
                    class="link link-secondary link-hover text-xs md:text-sm lg:text-md font-semibold">Layanan</a>
                <a href="#harga"
                    class="link link-secondary link-hover text-xs md:text-sm lg:text-md font-semibold">Harga</a>
                <a href="#faq"
                    class="link link-secondary link-hover text-xs md:text-sm lg:text-md font-semibold">FAQ</a>
                <a href="#tentang"
                    class="link link-secondary link-hover text-xs md:text-sm lg:text-md font-semibold">Tentang</a>
                <a href="#tim"
                    class="link link-secondary link-hover text-xs md:text-sm lg:text-md font-semibold">Tim</a>
                <a href="#testimoni"
                    class="link link-secondary link-hover text-xs md:text-sm lg:text-md font-semibold">Testimoni</a>
                <a href="#lokasi"
                    class="link link-secondary link-hover text-xs md:text-sm lg:text-md font-semibold">Lokasi</a>
                <a href="#galeri"
                    class="link link-secondary link-hover text-xs md:text-sm lg:text-md font-semibold">Galeri</a>
                <a href="#promo"
                    class="link link-secondary link-hover text-xs md:text-sm lg:text-md font-semibold">Promo</a>
                <a href="#reservasi"
                    class="link link-secondary link-hover text-xs md:text-sm lg:text-md font-semibold">Reservasi</a>
            </div>
        </div>

        {{-- Mobile Menu (kurang dari lg) --}}
        <div class="dropdown dropdown-end lg:hidden">
            <div tabindex="0" role="button" class="btn btn-xs btn-primary btn-circle">
                <x-icon class="h-4" name="solar.hamburger-menu-linear" />
            </div>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-50 w-52 p-2 shadow-lg">
                <li><a href="#beranda" class="link link-primary">Beranda</a></li>
                <li><a href="#layanan" class="link link-neutral">Layanan</a></li>
                <li><a href="#harga" class="link link-neutral">Harga</a></li>
                <li><a href="#faq" class="link link-neutral">FAQ</a></li>
                <li><a href="#tentang" class="link link-neutral">Tentang</a></li>
                <li><a href="#tim" class="link link-neutral">Tim</a></li>
                <li><a href="#testimoni" class="link link-neutral">Testimoni</a></li>
                <li><a href="#lokasi" class="link link-neutral">Lokasi</a></li>
                <li><a href="#galeri" class="link link-neutral">Galeri</a></li>
                <li><a href="#promo" class="link link-neutral">Promo</a></li>
                <li><a href="#reservasi" class="link link-neutral">Reservasi</a></li>
            </ul>
        </div>
    </header>

    {{-- Main --}}
    <main class="min-h-dvh min-w-dvw">
        {{-- Section Beranda --}}
        <section id="beranda"
            class="scroll-mt-12 min-h-dvh px-8 bg-gradient-to-b from-base-100 via-primary/46 to-primary/48 flex items-center">
            <div class="container mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                    {{-- Grid 1 --}}
                    <div class="col-span-1">
                        <div class="text-center lg:text-left">
                            <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-bold text-primary mb-6">
                                Layanan Laundry <span class="text-accent">Terpercaya</span>
                            </h1>
                            <p class="text-lg sm:text-xl lg:text-2xl text-secondary mb-8 leading-relaxed">
                                Kami memberikan pelayanan laundry terbaik dengan kualitas premium dan harga terjangkau
                                untuk semua kebutuhan Anda
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                                <x-button class="btn-accent btn-lg shadow-lg" link="/#layanan" no-wire-navigate >
                                   <x-icon class="h-5 w-5" name="solar.arrow-down-line-duotone" />
                                    Mulai Sekarang
                                </x-button>
                                <x-button class="btn-outline btn-primary btn-lg shadow-lg" link="/#reservasi" no-wire-navigate >
                                   <x-icon class="h-5 w-5" name="solar.plain-2-line-duotone" />
                                    Reservasi
                                </x-button>
                            </div>
                        </div>
                    </div>

                    {{-- Grid 2 --}}
                    <div class="col-span-1">
                        <div class="text-center space-y-6">
                            {{-- Feature Badges --}}
                            <div class="flex flex-row gap-3 justify-center flex-wrap">
                                <div class="badge badge-soft badge-accent badge-lg gap-2 p-4 text-sm font-medium">
                                    <x-icon class="h-4 w-4" name="solar.watch-square-line-duotone" />
                                    24/7 Service
                                </div>
                                <div class="badge badge-soft badge-primary badge-lg gap-2 p-4 text-sm font-medium">
                                    <x-icon class="h-4 w-4" name="solar.leaf-line-duotone" />
                                    Eco Friendly
                                </div>
                            </div>

                            {{-- Customer Review Card --}}
                            <div class="card bg-base-100/95 shadow-lg rounded-2xl w-full max-w-md mx-auto">
                                <div class="card-body p-4">
                                    <div class="flex items-center justify-center gap-2 mb-3">
                                        <div class="rating rating-sm">
                                            <input type="radio" name="rating-1"
                                                class="mask mask-star-2 bg-accent" />
                                            <input type="radio" name="rating-1"
                                                class="mask mask-star-2 bg-accent" />
                                            <input type="radio" name="rating-1"
                                                class="mask mask-star-2 bg-accent" />
                                            <input type="radio" name="rating-1"
                                                class="mask mask-star-2 bg-accent" />
                                            <input type="radio" name="rating-1" class="mask mask-star-2 bg-accent"
                                                checked="checked" />
                                        </div>
                                        <span class="text-sm font-bold text-accent">5.0</span>
                                    </div>
                                    <p
                                        class="text-sm text-secondary italic text-center leading-relaxed mb-3 font-medium">
                                        "Pelayanan sangat memuaskan! Pakaian bersih dan wangi. Recommended!"</p>
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="avatar">
                                            <div class="w-6 rounded-full bg-primary">
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <span class="text-sm text-primary-content font-bold">S</span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-sm font-bold text-primary">Sarah M.</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Stats Card --}}
                            <div class="stats stats-vertical lg:stats-horizontal shadow-lg bg-base-100/95 w-full mb-4">
                                <div class="stat py-4 text-center">
                                    <div class="stat-title text-sm font-medium">Pelanggan</div>
                                    <div class="stat-value text-2xl text-primary font-bold">1000+</div>
                                    <div class="stat-desc text-sm font-medium">Pelanggan Puas</div>
                                </div>

                                <div class="stat py-4 text-center">
                                    <div class="stat-title text-sm font-medium">Rating</div>
                                    <div class="stat-value text-2xl text-accent font-bold">4.9</div>
                                    <div class="stat-desc text-sm font-medium">↗︎ 0.2 (4%)</div>
                                </div>

                                <div class="stat py-4 text-center">
                                    <div class="stat-title text-sm font-medium">Pengalaman</div>
                                    <div class="stat-value text-2xl text-secondary font-bold">5+</div>
                                    <div class="stat-desc text-sm font-medium">Tahun Melayani</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section Layanan --}}
        <section id="layanan" class="min-h-dvh px-8 bg-base-200 flex flex-col justify-start pt-24">
            <div class="container mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-center mb-8 text-primary">Layanan Kami</h2>
                <h3 class="text-lg md:text-xl lg:text-2xl text-center mb-12 text-secondary">Berbagai layanan laundry
                    profesional untuk kebutuhan Anda</h3>
                <!-- Konten layanan akan ditambahkan di sini -->
            </div>
        </section>

        {{-- Section Harga --}}
        <section id="harga" class="min-h-dvh px-8 flex flex-col justify-start pt-24">
            <div class="container mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-left mb-8 text-primary">Paket Harga</h2>
                <h3 class="text-lg md:text-xl lg:text-2xl text-left mb-12 text-secondary">Pilih paket yang sesuai
                    dengan kebutuhan dan budget Anda</h3>
                <!-- Konten harga akan ditambahkan di sini -->
            </div>
        </section>

        {{-- Section FAQ --}}
        <section id="faq" class="min-h-dvh px-8 bg-base-200 flex flex-col justify-start pt-24">
            <div class="container mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-right mb-8 text-primary">FAQ</h2>
                <h3 class="text-lg md:text-xl lg:text-2xl text-right mb-12 text-secondary">Pertanyaan yang sering
                    ditanyakan</h3>
                <!-- Konten FAQ akan ditambahkan di sini -->
            </div>
        </section>

        {{-- Section Tentang --}}
        <section id="tentang" class="min-h-dvh px-8 flex flex-col justify-start pt-24">
            <div class="container mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-center mb-8 text-primary">Tentang
                    {{ config('app.name') }}</h2>
                <h3 class="text-lg md:text-xl lg:text-2xl text-center mb-12 text-secondary">Layanan laundry terpercaya
                    dengan pengalaman bertahun-tahun</h3>
                <!-- Konten tentang akan ditambahkan di sini -->
            </div>
        </section>

        {{-- Section Tim Kami --}}
        <section id="tim" class="min-h-dvh px-8 bg-base-200 flex flex-col justify-start pt-24">
            <div class="container mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-left mb-8 text-primary">Tim Kami</h2>
                <h3 class="text-lg md:text-xl lg:text-2xl text-left mb-12 text-secondary">Tenaga ahli berpengalaman
                    yang siap melayani Anda dengan profesional</h3>
                <!-- Konten tim kami akan ditambahkan di sini -->
            </div>
        </section>

        {{-- Section Testimoni --}}
        <section id="testimoni" class="min-h-dvh px-8 flex flex-col justify-start pt-24">
            <div class="container mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-right mb-8 text-primary">Testimoni</h2>
                <h3 class="text-lg md:text-xl lg:text-2xl text-right mb-12 text-secondary">Apa kata pelanggan kami</h3>
                <!-- Konten testimoni akan ditambahkan di sini -->
            </div>
        </section>

        {{-- Section Lokasi --}}
        <section id="lokasi" class="min-h-dvh px-8 bg-base-200 flex flex-col justify-start pt-24">
            <div class="container mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-center mb-8 text-primary">Lokasi & Coverage
                </h2>
                <h3 class="text-lg md:text-xl lg:text-2xl text-center mb-12 text-secondary">Area layanan dan lokasi
                    kami</h3>
                <!-- Konten lokasi akan ditambahkan di sini -->
            </div>
        </section>

        {{-- Section Galeri --}}
        <section id="galeri" class="min-h-dvh px-8 flex flex-col justify-start pt-24">
            <div class="container mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-left mb-8 text-primary">Galeri</h2>
                <h3 class="text-lg md:text-xl lg:text-2xl text-left mb-12 text-secondary">Dokumentasi fasilitas dan
                    hasil kerja berkualitas tinggi kami</h3>
                <!-- Konten galeri akan ditambahkan di sini -->
            </div>
        </section>

        {{-- Section Promo --}}
        <section id="promo" class="min-h-dvh px-8 bg-base-200 flex flex-col justify-start pt-24">
            <div class="container mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-right mb-8 text-primary">Promo & Diskon
                </h2>
                <h3 class="text-lg md:text-xl lg:text-2xl text-right mb-12 text-secondary">Penawaran menarik untuk
                    Anda</h3>
                <!-- Konten promo akan ditambahkan di sini -->
            </div>
        </section>

        {{-- Section Reservasi --}}
        <section id="reservasi" class="min-h-dvh px-8 flex flex-col justify-start pt-24">
            <div class="container mx-auto max-w-4xl">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-center mb-8 text-primary">Reservasi Sekarang
                </h2>
                <h3 class="text-lg md:text-xl lg:text-2xl text-center mb-12 text-secondary">Pesan layanan laundry
                    dengan mudah dan cepat</h3>
                <!-- Form reservasi akan ditambahkan di sini -->
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer
        class="footer sm:footer-horizontal bg-base-300 text-base-content border-t-4 border-t-primary rounded-t-2xl p-10">
        <aside>
            <x-icon class="text-primary h-12 w-12" name="solar.washing-machine-minimalistic-bold-duotone" />
            <p class="font-bold text-primary">
                {{ config('app.name') }}
                <br />
                Layanan laundry terpercaya sejak 2025
            </p>
        </aside>
        <nav>
            <h6 class="footer-title text-primary">Layanan</h6>
            <a href="#layanan" class="link link-hover">Cuci Kering</a>
            <a href="#layanan" class="link link-hover">Setrika</a>
            <a href="#layanan" class="link link-hover">Dry Cleaning</a>
            <a href="#promo" class="link link-hover">Promo Spesial</a>
        </nav>
        <nav>
            <h6 class="footer-title text-primary">Perusahaan</h6>
            <a href="#tentang" class="link link-hover">Tentang Kami</a>
            <a href="#lokasi" class="link link-hover">Lokasi</a>
            <a href="#testimoni" class="link link-hover">Testimoni</a>
            <a href="#faq" class="link link-hover">FAQ</a>
        </nav>
        <nav>
            <h6 class="footer-title text-primary">Kontak</h6>
            <a class="link link-hover">0812-3456-7890</a>
            <a class="link link-hover">info@mainlaundry.com</a>
            <a class="link link-hover">Jl. Laundry Bersih No. 123</a>
            <a href="#reservasi" class="link link-hover">Reservasi Online</a>
        </nav>
    </footer>
</body>

</html>
