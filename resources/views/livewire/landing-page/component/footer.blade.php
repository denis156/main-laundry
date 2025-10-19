<footer class="footer sm:footer-horizontal bg-base-100 text-base-content p-10">
    <aside>
        <img src="{{ asset('image/logo.png') }}" alt="Main Laundry Logo" class="h-18 w-auto fill-current">
        <p class="text-secondary">
            <span class="font-semibold text-accent">Tetap Main, <span class="text-primary">Tetap
                    Bersih!</span></span>
            <br />
            anda bebas main kemanapun baju anda urusan kami
        </p>
    </aside>
    <nav>
        <h6 class="footer-title opacity-100">Navigasi Cepat</h6>
        <a href="#beranda" class="link link-hover hover:text-primary">Beranda</a>
        <a href="#untuk-mu" class="link link-hover hover:text-primary">Untuk Mu</a>
        <a href="#layanan" class="link link-hover hover:text-primary">Layanan</a>
        <a href="#kontak" class="link link-hover hover:text-primary">Kontak</a>
        <a href="#pesan" class="link link-hover hover:text-primary">Pesan</a>
    </nav>
    <nav>
        <h6 class="footer-title opacity-100">Hubungi Kami</h6>
        @if($phone)
            <a href="tel:{{ str_replace([' ', '-'], '', $phone) }}" class="link link-hover hover:text-primary">{{ $phone }}</a>
        @endif
        @if($email)
            <a href="mailto:{{ $email }}" class="link link-hover hover:text-primary">{{ $email }}</a>
        @endif
        @if($phone)
            <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', $phone) }}" target="_blank" class="link link-hover hover:text-primary">WhatsApp</a>
        @endif
        <p class="text-sm">Kota Kendari</p>
    </nav>
    <nav>
        <h6 class="footer-title opacity-100">Credits</h6>
        <p class="text-sm opacity-70">Powered by</p>
        <div class="text-accent font-bold flex items-center gap-1 mb-4">
            <x-icon name="solar.buildings-bold" class="h-4 w-4" />
            MAIN GROUP
        </div>
        <p class="text-sm opacity-70">Graphic Design by</p>
        <a href="https://www.canva.com/@thidaratsuteeratatphotos" target="_blank"
            class="link link-hover hover:text-primary flex items-center gap-1">
            <x-icon name="mdi.palette" class="h-4 w-4" />
            @thidaratsuteeratatphotos
        </a>
        <p class="text-xs opacity-60">via Canva</p>
    </nav>
</footer>
