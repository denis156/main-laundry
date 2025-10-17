<nav class="navbar bg-base-100/94 sticky top-0 z-50 shadow-sm border-b border-base-200 px-4 lg:px-8" x-data="nav">
    {{-- Mobile Menu & Logo --}}
    <div class="navbar-start">
        <div class="dropdown">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle lg:hidden">
                <x-icon class="h-6 w-6" name="mdi.menu" />
            </div>
            <ul tabindex="0" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-50 mt-3 w-52 p-2 shadow-lg">
                @foreach($menuItems as $section => $label)
                    <li>
                        <a href="#{{ $section }}"
                           @click.prevent="scrollToSection('{{ $section }}')"
                           class="transition-colors duration-300"
                           :class="activeSection === '{{ $section }}' ? 'text-primary font-semibold' : 'hover:text-primary'">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <a href="/" class="btn btn-ghost normal-case text-xl px-2">
            <img src="{{ asset('image/logo.png') }}" alt="Main Laundry Logo" class="h-14 w-auto">
        </a>
    </div>

    {{-- Desktop Menu --}}
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1">
            @foreach($menuItems as $section => $label)
                <li>
                    <a href="#{{ $section }}"
                       @click.prevent="scrollToSection('{{ $section }}')"
                       class="font-medium transition-colors duration-300"
                       :class="activeSection === '{{ $section }}' ? 'text-primary font-bold' : 'hover:text-primary'">
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- CTA Button --}}
    <div class="navbar-end">
        <a href="#pesan"
            class="btn btn-accent btn-sm lg:btn-md rounded-full gap-2 hover:scale-105 transition-transform shadow-md hover:shadow-lg">
            <x-icon class="h-4 w-4 lg:h-5 lg:w-5" name="mdi.phone" />
            <span class="hidden sm:inline">Pesan Sekarang</span>
            <span class="sm:hidden">Pesan</span>
        </a>
    </div>
</nav>

@script
<script>
Alpine.data('nav', () => ({
    activeSection: @entangle('activeSection'),
    sections: @js(array_keys($menuItems)),

    scrollToSection(sectionId) {
        // Simple: langsung scroll, biar scroll detection yang handle snake effect
        const section = document.getElementById(sectionId);
        if (section) {
            section.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    },

    updateActiveSection() {
        const scrollPos = window.scrollY + 100;
        let detectedSection = null;

        // Detect section yang seharusnya aktif
        for (let i = this.sections.length - 1; i >= 0; i--) {
            const section = document.getElementById(this.sections[i]);
            if (section && section.offsetTop <= scrollPos) {
                detectedSection = this.sections[i];
                break;
            }
        }

        if (detectedSection && this.activeSection !== detectedSection) {
            const currentIndex = this.sections.indexOf(this.activeSection);
            const targetIndex = this.sections.indexOf(detectedSection);
            const gap = Math.abs(targetIndex - currentIndex);

            if (gap <= 1) {
                // Adjacent atau same, langsung update
                this.activeSection = detectedSection;
            } else {
                // Snake effect: step by step
                const direction = targetIndex > currentIndex ? 1 : -1;
                this.activeSection = this.sections[currentIndex + direction];
            }
        }
    },

    init() {
        // Simple scroll listener
        let lastUpdate = 0;
        window.addEventListener('scroll', () => {
            const now = Date.now();
            if (now - lastUpdate > 80) {
                this.updateActiveSection();
                lastUpdate = now;
            }
        }, { passive: true });

        // Initial detection
        setTimeout(() => this.updateActiveSection(), 100);
    }
}));
</script>
@endscript
