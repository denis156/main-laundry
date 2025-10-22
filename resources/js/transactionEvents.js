// ==========================================
// TRANSACTION EVENTS HANDLER (Real-time with Reverb)
// ==========================================
// Menangani event transaction dari Laravel Echo
// dan dispatch Livewire events untuk refresh components

document.addEventListener('livewire:init', () => {
    // Listen Echo channel untuk real-time transaction notifications
    if (window.Echo) {
        console.log('[Echo] Subscribing to transactions channel...');

        window.Echo.channel('transactions')
            .listen('.transaction.event', (event) => {
                console.log('[Echo] Transaction event received:', event);
                console.log('[Echo] Action:', event.action);

                // Dispatch event untuk play ringtone (akan ditangani oleh newOrderRingtone.js)
                if (event.action === 'created' || event.action === 'updated') {
                    Livewire.dispatch('play-order-ringtone');
                }

                // Dispatch Livewire events to refresh all components
                // Semua component akan refresh otomatis tanpa polling
                Livewire.dispatch('refresh-orders');
                Livewire.dispatch('refresh-new-orders');
                Livewire.dispatch('refresh-chart');
                Livewire.dispatch('refresh-dashboard');
                Livewire.dispatch('refresh-stats');

                console.log('[Echo] All components refreshed for action:', event.action);
            });

        console.log('[Echo] Successfully subscribed to transactions channel');
    } else {
        console.error('[Echo] Echo not found! Make sure Laravel Echo is loaded.');
    }
});
