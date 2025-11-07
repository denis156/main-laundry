<!-- Footer -->
<footer class="bg-base-200 text-base-content px-8">
    <!-- Main Footer Content -->
    <div class="container py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Brand Section -->
            <div class="space-y-4">
                <img src="{{ asset('image/logo.png') }}" alt="{{ config('app.name') }}" class="h-16 w-auto">
                <p class="text-base-content/70 max-w-xs">
                    Solusi laundry terpercaya dengan layanan berkualitas dan harga terjangkau untuk kebutuhan laundry Anda.
                </p>
            </div>

            <!-- Layanan -->
            <nav aria-label="Services" class="space-y-4">
                <h4 class="font-semibold text-lg">Layanan Kami</h4>
                <ul class="space-y-2">
                    <li><a href="#layanan" class="link link-hover text-base-content/70 hover:text-primary transition-colors">Cuci Kilat</a></li>
                    <li><a href="#layanan" class="link link-hover text-base-content/70 hover:text-primary transition-colors">Cuci Reguler</a></li>
                    <li><a href="#layanan" class="link link-hover text-base-content/70 hover:text-primary transition-colors">Setrika</a></li>
                    <li><a href="#layanan" class="link link-hover text-base-content/70 hover:text-primary transition-colors">Cuci Karpet</a></li>
                </ul>
            </nav>

            <!-- Navigasi -->
            <nav aria-label="Navigation" class="space-y-4">
                <h4 class="font-semibold text-lg">Navigasi</h4>
                <ul class="space-y-2">
                    <li><a href="#beranda" class="link link-hover text-base-content/70 hover:text-primary transition-colors">Beranda</a></li>
                    <li><a href="#faq" class="link link-hover text-base-content/70 hover:text-primary transition-colors">FAQ</a></li>
                    <li><a href="#layanan" class="link link-hover text-base-content/70 hover:text-primary transition-colors">Layanan</a></li>
                    <li><a href="#pesan" class="link link-hover text-base-content/70 hover:text-primary transition-colors">Pesan</a></li>
                </ul>
            </nav>

            <!-- Kontak & Sosial Media -->
            <div class="space-y-4">
                <h4 class="font-semibold text-lg">Ikuti Kami</h4>
                <div class="flex gap-4">
                    <a href="#" aria-label="Facebook" class="btn btn-circle btn-ghost hover:bg-primary hover:text-primary-content transition-colors">
                        <x-icon name="solar.letter-bold" class="h-5 w-5" />
                    </a>
                    <a href="#" aria-label="Instagram" class="btn btn-circle btn-ghost hover:bg-primary hover:text-primary-content transition-colors">
                        <x-icon name="solar.camera-bold" class="h-5 w-5" />
                    </a>
                    <a href="#" aria-label="WhatsApp" class="btn btn-circle btn-ghost hover:bg-primary hover:text-primary-content transition-colors">
                        <x-icon name="solar.chat-round-bold" class="h-5 w-5" />
                    </a>
                </div>
                <div class="pt-4 space-y-2">
                    <p class="text-base-content/70 text-sm">Email: info@mainlaundry.com</p>
                    <p class="text-base-content/70 text-sm">Telepon: +62 812-3456-7890</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="border-t border-base-300">
        <div class="container py-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-base-content/60 text-sm">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="link link-hover text-base-content/60 hover:text-primary text-sm transition-colors">Privacy</a>
                    <span class="text-base-content/40">|</span>
                    <a href="#" class="link link-hover text-base-content/60 hover:text-primary text-sm transition-colors">Terms</a>
                    <span class="text-base-content/40">|</span>
                    <a href="#" class="link link-hover text-base-content/60 hover:text-primary text-sm transition-colors">Cookies</a>
                </div>
            </div>
        </div>
    </div>
</footer>
