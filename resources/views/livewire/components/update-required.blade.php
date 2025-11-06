{{-- Update Required Modal - Force user untuk update app --}}
<x-modal
    wire:model="showModal"
    title="Pembaruan Tersedia"
    subtitle="Aplikasi perlu diperbarui"
    class="modal-bottom sm:modal-middle"
    persistent
    separator
    class="backdrop-blur"
>
    <div class="space-y-4">
        {{-- Icon Update --}}
        <div class="flex justify-center">
            <div class="p-4 bg-primary/10 rounded-full">
                <x-icon name="o-arrow-path" class="w-12 h-12 text-primary" />
            </div>
        </div>

        {{-- Message --}}
        <div class="text-center">
            <p class="text-base font-medium">
                Versi baru aplikasi telah tersedia!
            </p>
            <p class="text-sm text-base-content/60 mt-2">
                Aplikasi akan memuat ulang untuk menggunakan versi terbaru dengan fitur dan perbaikan terkini.
            </p>
        </div>
    </div>

    <x-slot:actions>
        <x-button
            label="Perbarui Sekarang"
            class="btn-primary w-full"
            wire:click="reloadApp"
            icon="o-arrow-path"
            spinner="reloadApp"
        />
    </x-slot:actions>
</x-modal>

{{-- Listen untuk force reload event dari Livewire --}}
@script
<script>
$wire.on('force-reload-app', async () => {
    console.log('[Update] User clicked update button');

    // Tunggu animasi modal close selesai (300ms untuk Mary UI modal)
    await new Promise(resolve => setTimeout(resolve, 300));

    if (!('serviceWorker' in navigator)) {
        console.log('[Update] Service worker not supported, reloading...');
        window.location.reload();
        return;
    }

    try {
        const registration = await navigator.serviceWorker.getRegistration();

        if (!registration) {
            console.log('[Update] No registration found, reloading...');
            window.location.reload();
            return;
        }

        const waitingWorker = registration.waiting;

        if (!waitingWorker) {
            console.log('[Update] No waiting worker, reloading...');
            window.location.reload();
            return;
        }

        // Kirim message ke WAITING worker (bukan controller!)
        console.log('[Update] Sending SKIP_WAITING to waiting worker...');
        waitingWorker.postMessage({ type: 'SKIP_WAITING' });

        // Listen untuk controllerchange (SW baru jadi active)
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            console.log('[Update] New service worker activated, reloading...');
            window.location.reload();
        });
    } catch (error) {
        console.error('[Update] Error during update:', error);
        window.location.reload();
    }
});
</script>
@endscript
