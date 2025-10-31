@php
    $layoutView = $layout ?? 'components.layouts.kurir';
@endphp

<x-dynamic-component :component="$layoutView">
{{-- Offline Page - Ditampilkan saat tidak ada koneksi internet --}}
<section class="bg-base-100">
    {{-- Header --}}
    <x-header icon="solar.station-minimalistic-line-duotone" icon-classes="text-error w-6 h-6" title="Tidak Ada Koneksi"
        subtitle="Aplikasi tidak dapat terhubung ke server" separator />

    <div class="space-y-4">
        {{-- Status Card --}}
        <x-card class="bg-base-300 shadow" title="Status Koneksi"
            subtitle="Periksa koneksi internet Anda" separator>
            {{-- Icon Offline - Animated --}}
            <div class="flex justify-center mb-6 animate-pulse">
                <div class="bg-error/10 rounded-full p-8 inline-block">
                    <x-icon name="solar.station-minimalistic-line-duotone" class="w-24 h-24 text-error" />
                </div>
            </div>

            {{-- Description --}}
            <p class="text-center text-base-content/70 mb-4 leading-relaxed">
                Maaf, aplikasi tidak dapat terhubung ke server.
                Pastikan Anda terhubung ke internet dan coba lagi.
            </p>

            {{-- Connection Status Indicator --}}
            <div class="flex justify-center">
                <div id="connection-status"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-error/10 text-error text-sm font-medium">
                    <span class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-error"></span>
                    </span>
                    Status: Offline
                </div>
            </div>

            <x-slot:actions separator>
                {{-- Back to Home --}}
                <x-button link="{{ $this->getBerandaRoute() }}" icon="solar.home-bold-duotone" label="Kembali ke Beranda"
                    class="btn-secondary" />

                {{-- Retry Button --}}
                <x-button onclick="window.location.reload()" icon="solar.refresh-bold-duotone" label="Coba Lagi"
                    class="btn-primary" />
            </x-slot:actions>
        </x-card>

        {{-- Tips Card --}}
        <x-card class="bg-base-300 shadow" title="Tips Mengatasi Masalah"
            subtitle="Langkah-langkah untuk mengatasi koneksi offline" separator>
            <ul class="space-y-3">
                <li class="flex items-start gap-3">
                    <div class="shrink-0">
                        <x-icon name="solar.check-circle-bold-duotone" class="w-6 h-6 text-success" />
                    </div>
                    <div>
                        <p class="font-semibold text-base-content">Periksa Koneksi</p>
                        <p class="text-sm text-base-content/70">Pastikan WiFi atau data seluler Anda aktif</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <div class="shrink-0">
                        <x-icon name="solar.check-circle-bold-duotone" class="w-6 h-6 text-success" />
                    </div>
                    <div>
                        <p class="font-semibold text-base-content">Mode Pesawat</p>
                        <p class="text-sm text-base-content/70">Aktifkan mode pesawat, tunggu 5 detik, lalu matikan
                            kembali</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <div class="shrink-0">
                        <x-icon name="solar.check-circle-bold-duotone" class="w-6 h-6 text-success" />
                    </div>
                    <div>
                        <p class="font-semibold text-base-content">Sinyal Jaringan</p>
                        <p class="text-sm text-base-content/70">Pastikan Anda berada di area dengan sinyal yang baik</p>
                    </div>
                </li>
            </ul>
        </x-card>
    </div>
</section>

{{-- Auto-detect koneksi kembali --}}
<script>
    // Check koneksi internet setiap 3 detik
    setInterval(() => {
        if (navigator.onLine) {
            // Koneksi kembali, coba reload
            console.log('[Offline Page] Connection restored, reloading...');
            window.location.reload();
        }
    }, 3000);

    // Update status indicator
    function updateConnectionStatus() {
        const statusEl = document.getElementById('connection-status');
        if (navigator.onLine) {
            statusEl.innerHTML = `
                <span class="relative flex h-3 w-3">
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-success"></span>
                </span>
                Status: Online (Reload...)
            `;
            statusEl.className =
                'inline-flex items-center gap-2 px-4 py-2 rounded-full bg-success/10 text-success text-sm font-medium';

            // Auto reload after 1 second
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            statusEl.innerHTML = `
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-error"></span>
                </span>
                Status: Offline
            `;
            statusEl.className =
                'inline-flex items-center gap-2 px-4 py-2 rounded-full bg-error/10 text-error text-sm font-medium';
        }
    }

    // Listen ke online/offline events
    window.addEventListener('online', updateConnectionStatus);
    window.addEventListener('offline', updateConnectionStatus);

    // Initial check
    updateConnectionStatus();
</script>
</section>
</x-dynamic-component>
