<header
    class="navbar bg-base-300/80 backdrop-blur-md border-b-4 border-b-primary shadow-lg rounded-b-2xl px-8 sticky top-0 z-50"
    x-data="navbar">
    <div class="flex-1">
        <div class="flex items-center text-primary gap-1">
            <x-icon class="h-6 md:h-8 lg:h-10" name="mdi.washing-machine" />
            <h1 class="text-md md:text-lg lg:text-2xl font-bold">{{ config('app.name') }}</h1>
        </div>
    </div>
    {{-- Desktop Menu (lg dan ke atas) --}}
    <div class="flex-none hidden lg:block">
        <div class="flex items-center gap-6">
            @foreach($menuItems as $section => $label)
                <a href="#{{ $section }}"
                   @click.prevent="scrollToSection('{{ $section }}')"
                   class="link text-xs md:text-sm lg:text-md font-semibold transition-colors duration-300"
                   :class="activeSection === '{{ $section }}' ? 'link-primary' : 'link-secondary link-hover'">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Mobile Menu (kurang dari lg) --}}
    <div class="dropdown dropdown-end lg:hidden">
        <div tabindex="0" role="button" class="btn btn-outline btn-sm btn-primary btn-circle">
            <x-icon class="h-6" name="mdi.menu" />
        </div>
        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-50 w-52 p-2 shadow-lg">
            @foreach($menuItems as $section => $label)
                <li>
                    <a href="#{{ $section }}"
                       @click.prevent="scrollToSection('{{ $section }}')"
                       class="link font-semibold transition-colors duration-300"
                       :class="activeSection === '{{ $section }}' ? 'link-primary' : 'link-hover link-secondary'">
                        {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</header>

@script
<script>
Alpine.data('navbar', () => ({
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
