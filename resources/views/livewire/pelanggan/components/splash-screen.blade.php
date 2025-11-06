{{-- Splash Screen Carousel untuk Pelanggan PWA --}}
<div class="fixed inset-0 z-50 flex flex-col bg-base-100 overflow-hidden min-h-dvh"
    x-data="{
        // Cek apakah aplikasi berjalan dalam mode standalone (PWA installed)
        init() {
            const isProduction = '{{ app()->environment('production') }}' === '1';
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
                                window.navigator.standalone ||
                                document.referrer.includes('android-app://');

            // Hanya enforce standalone check di production
            // Di local/development, splash screen bisa diakses dari browser biasa
            if (isProduction && !isStandalone) {
                window.location.href = '/pelanggan/masuk';
            }
        },
        currentSlide: 0,
        totalSlides: 4,
        startX: 0,
        currentX: 0,
        isDragging: false,

        nextSlide() {
            if (this.currentSlide < this.totalSlides - 1) {
                this.currentSlide++;
            }
        },

        prevSlide() {
            if (this.currentSlide > 0) {
                this.currentSlide--;
            }
        },

        handleTouchStart(e) {
            this.startX = e.touches[0].clientX;
            this.isDragging = true;
        },

        handleTouchMove(e) {
            if (!this.isDragging) return;
            this.currentX = e.touches[0].clientX;
        },

        handleTouchEnd() {
            if (!this.isDragging) return;

            const diff = this.startX - this.currentX;
            const threshold = 50; // minimum swipe distance

            if (Math.abs(diff) > threshold) {
                if (diff > 0) {
                    // Swipe left - next slide
                    this.nextSlide();
                } else {
                    // Swipe right - previous slide
                    this.prevSlide();
                }
            }

            this.isDragging = false;
            this.startX = 0;
            this.currentX = 0;
        }
    }">
    {{-- Skip Button --}}
    <div class="absolute top-4 right-4 z-10">
        <button wire:click="skipOnboarding"
            class="btn btn-ghost btn-sm text-base-content/60 hover:text-base-content">
            Lewati
        </button>
    </div>

    {{-- Swiper Container with Touch Events --}}
    <div class="flex-1 flex items-center justify-center px-4 relative"
        @touchstart="handleTouchStart($event)"
        @touchmove="handleTouchMove($event)"
        @touchend="handleTouchEnd()">

        {{-- Slides Container (Horizontal Scroll) --}}
        <div class="w-full max-w-md relative overflow-hidden">
            <div class="flex transition-transform duration-300 ease-out"
                :style="`transform: translateX(-${currentSlide * 100}%)`">
            {{-- Slide 1: Welcome --}}
            <div class="w-full shrink-0 text-center space-y-6 px-4">
                {{-- Header --}}
                <h2 class="text-3xl font-bold text-primary">Yuk, Kenalan Dulu!</h2>

                {{-- Illustration --}}
                <div class="flex justify-center">
                    <div class="bg-linear-to-br from-primary/10 to-primary/5 rounded-3xl p-8 backdrop-blur-sm shadow-lg">
                        <x-icon name="solar.exit-bold-duotone" class="w-32 h-32 text-primary drop-shadow-md" />
                    </div>
                </div>

                {{-- Description --}}
                <p class="text-base-content/70 text-lg leading-relaxed px-2">
                    {{ config('app.name') }}, solusi cuci baju yang bikin hidup kamu lebih gampang.<br>
                    <span class="text-primary font-semibold">Bersih, cepat, harga bersahabat!</span>
                </p>
            </div>

            {{-- Slide 2: Free Pickup & Delivery --}}
            <div class="w-full shrink-0 text-center space-y-6 px-4">
                {{-- Header --}}
                <h2 class="text-3xl font-bold text-accent">Antar Jemput Gratis!</h2>

                {{-- Illustration --}}
                <div class="flex justify-center">
                    <div class="bg-linear-to-br from-accent/10 to-accent/5 rounded-3xl p-8 backdrop-blur-sm shadow-lg">
                        <x-icon name="solar.delivery-bold-duotone" class="w-32 h-32 text-accent drop-shadow-md" />
                    </div>
                </div>

                {{-- Description --}}
                <p class="text-base-content/70 text-lg leading-relaxed px-2">
                    Kurir kami jemput cucian di rumah kamu, terus antar balik lagi kalau udah selesai.<br>
                    <span class="font-semibold text-accent">Kamu tinggal santai aja, beres!</span>
                </p>
            </div>

            {{-- Slide 3: Tracking Real-time --}}
            <div class="w-full shrink-0 text-center space-y-6 px-4">
                {{-- Header --}}
                <h2 class="text-3xl font-bold text-success">Pantau Cucian Kamu!</h2>

                {{-- Illustration --}}
                <div class="flex justify-center">
                    <div class="bg-linear-to-br from-success/10 to-success/5 rounded-3xl p-8 backdrop-blur-sm shadow-lg">
                        <x-icon name="solar.map-point-wave-bold-duotone" class="w-32 h-32 text-success drop-shadow-md" />
                    </div>
                </div>

                {{-- Description --}}
                <p class="text-base-content/70 text-lg leading-relaxed px-2">
                    Cek status cucian kamu dimana dan kapan aja.<br>
                    Dari dijemput sampai diantar balik, semua ada notifikasinya.<br>
                    <span class="font-semibold text-success">Gak perlu khawatir, semua jelas!</span>
                </p>
            </div>

            {{-- Slide 4: Affordable Price --}}
            <div class="w-full shrink-0 text-center space-y-6 px-4">
                {{-- Header --}}
                <h2 class="text-3xl font-bold text-warning">Harga Ramah Kantong!</h2>

                {{-- Illustration --}}
                <div class="flex justify-center">
                    <div class="bg-linear-to-br from-warning/10 to-warning/5 rounded-3xl p-8 backdrop-blur-sm shadow-lg">
                        <x-icon name="solar.wallet-money-bold-duotone" class="w-32 h-32 text-warning drop-shadow-md" />
                    </div>
                </div>

                {{-- Description --}}
                <p class="text-base-content/70 text-lg leading-relaxed px-2">
                    Cuci baju berkualitas tanpa bikin dompet kamu nangis.<br>
                    <span class="text-2xl font-bold text-warning">Mulai 3 ribu aja!</span><br>
                    <span class="font-semibold text-warning">Yuk, cobain sekarang!</span>
                </p>
            </div>
            </div>
        </div>
    </div>

    {{-- Bottom Navigation --}}
    <div class="pb-8 px-4 space-y-6">
        {{-- Pagination Dots (hidden on first slide) --}}
        <div x-show="currentSlide > 0" class="flex justify-center gap-2">
            <template x-for="i in totalSlides" :key="i">
                <button @click="currentSlide = i - 1"
                    :class="currentSlide === i - 1 ? 'w-8 bg-primary' : 'w-2 bg-base-content/20'"
                    class="h-2 rounded-full transition-all duration-300"></button>
            </template>
        </div>

        <div class="grid grid-cols-2 gap-3 max-w-md mx-auto">
            {{-- Back Button (invisible on first slide, but takes space) --}}
            <button @click="prevSlide()"
                :disabled="currentSlide === 0"
                :class="currentSlide === 0 ? 'invisible' : 'visible'"
                class="btn btn-outline gap-2 transition-opacity duration-200">
                <x-icon name="o-arrow-left" class="w-5 h-5" />
                Kembali
            </button>

            {{-- Next Button (visible on slides 1-3) --}}
            <button x-show="currentSlide < totalSlides - 1"
                @click="nextSlide()"
                :class="currentSlide === 0 ? 'col-span-2' : ''"
                class="btn btn-primary">
                <span x-show="currentSlide === 0">Yuk Kenalan</span>
                <span x-show="currentSlide > 0">Lanjut</span>
                <x-icon name="o-arrow-right" class="w-5 h-5" />
            </button>

            {{-- Finish Button (visible only on last slide) --}}
            <button wire:click="finishOnboarding"
                x-show="currentSlide === totalSlides - 1"
                class="btn btn-primary">
                Masuk
                <x-icon name="solar.login-bold-duotone" class="w-5 h-5" />
            </button>
        </div>
    </div>
</div>
