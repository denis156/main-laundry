// ==========================================
// ORDER NOTIFICATION RINGTONE SYSTEM (Real-time with Reverb)
// ==========================================
document.addEventListener('livewire:init', () => {
    const ringtone = document.getElementById('order-ringtone');
    const AUDIO_ENABLED_KEY = 'kurir_audio_enabled';

    // Check if audio was previously enabled
    let audioEnabled = localStorage.getItem(AUDIO_ENABLED_KEY) === 'true';

    function enableAudio() {
        if (!audioEnabled && ringtone) {
            ringtone.load();
            audioEnabled = true;
            localStorage.setItem(AUDIO_ENABLED_KEY, 'true');

            // Test play sound untuk confirm audio works
            ringtone.volume = 0.5;
            ringtone.currentTime = 0;
            ringtone.play().catch(() => {});
        }
    }

    // Try to enable immediately
    if (ringtone) {
        ringtone.load();
    }

    // Enable audio saat user interact dengan page
    const events = ['click', 'touchstart', 'keydown', 'scroll'];
    events.forEach(eventName => {
        document.addEventListener(eventName, enableAudio, { once: true, passive: true });
    });

    function playRingtone() {
        if (ringtone) {
            ringtone.currentTime = 0;
            ringtone.volume = 1.0;

            const playPromise = ringtone.play();

            if (playPromise !== undefined) {
                playPromise
                    .then(() => {
                        if (!audioEnabled) {
                            audioEnabled = true;
                            localStorage.setItem(AUDIO_ENABLED_KEY, 'true');
                        }
                        console.log('[Ringtone] Played successfully');
                    })
                    .catch((error) => {
                        console.warn('[Ringtone] Cannot play ringtone:', error);
                        // Try to enable audio on next interaction
                        audioEnabled = false;
                        localStorage.removeItem(AUDIO_ENABLED_KEY);
                    });
            }
        }
    }

    // Listen event dari Livewire untuk play ringtone
    Livewire.on('play-order-ringtone', () => {
        console.log('[Ringtone] Play ringtone event received from Livewire');
        playRingtone();
    });

    // Listen Echo channel untuk real-time order notifications
    if (window.Echo) {
        console.log('[Echo] Subscribing to kurir-orders channel...');

        window.Echo.channel('kurir-orders')
            .listen('.new-transaction', (event) => {
                console.log('[Echo] New transaction received:', event);

                // Play ringtone
                playRingtone();

                // Dispatch Livewire event to refresh data
                Livewire.dispatch('refresh-orders');
            });

        console.log('[Echo] Successfully subscribed to kurir-orders channel');
    } else {
        console.error('[Echo] Echo not found! Make sure Laravel Echo is loaded.');
    }
});
