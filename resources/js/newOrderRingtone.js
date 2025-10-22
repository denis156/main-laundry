// ==========================================
// NEW ORDER RINGTONE HANDLER
// ==========================================
// Menangani audio ringtone untuk notifikasi pesanan baru masuk
// File ini hanya fokus pada audio ringtone, bukan dispatch events
document.addEventListener('livewire:init', () => {
    const ringtone = document.getElementById('order-ringtone');
    const AUDIO_ENABLED_KEY = 'kurir_audio_enabled';

    // Check if audio was previously enabled
    let audioEnabled = localStorage.getItem(AUDIO_ENABLED_KEY) === 'true';
    let audioUnlocked = false; // Always start as false, audio context needs to be unlocked every page load
    let pendingRingtone = false;

    // Define events array first so it can be accessed in unlockAudio
    const events = ['click', 'touchstart', 'touchend', 'mousedown', 'keydown'];

    // Function to handle user interaction - defined early for reference
    function handleUserInteraction() {
        if (!audioUnlocked) {
            unlockAudio();
        }
    }

    function unlockAudio() {
        if (!ringtone || audioUnlocked) return;

        console.log('[Ringtone] Unlocking audio context...');

        // Load audio
        ringtone.load();

        // Try to play silent audio to unlock audio context
        ringtone.volume = 0.01;
        ringtone.currentTime = 0;

        const unlockPromise = ringtone.play();

        if (unlockPromise !== undefined) {
            unlockPromise
                .then(() => {
                    // Pause immediately after playing
                    ringtone.pause();
                    ringtone.currentTime = 0;

                    audioUnlocked = true;
                    audioEnabled = true;
                    localStorage.setItem(AUDIO_ENABLED_KEY, 'true');

                    console.log('[Ringtone] Audio unlocked successfully');

                    // Remove event listeners after successful unlock
                    events.forEach(eventName => {
                        document.removeEventListener(eventName, handleUserInteraction);
                    });

                    // If there's a pending ringtone, play it now
                    if (pendingRingtone) {
                        pendingRingtone = false;
                        setTimeout(() => playRingtone(), 100);
                    }
                })
                .catch(() => {
                    console.log('[Ringtone] Audio not unlocked yet, waiting for user interaction...');
                });
        }
    }

    // Try to enable immediately (will fail until user interaction)
    if (ringtone) {
        ringtone.load();
        unlockAudio();
    }

    // Add event listeners that will retry until audio is unlocked
    events.forEach(eventName => {
        document.addEventListener(eventName, handleUserInteraction, { passive: true });
    });

    function playRingtone() {
        console.log('[Ringtone] playRingtone() called');

        if (!ringtone) {
            console.error('[Ringtone] Audio element not found!');
            return;
        }

        // Check if audio is unlocked
        if (!audioUnlocked) {
            console.warn('[Ringtone] Audio not unlocked yet, setting as pending...');
            pendingRingtone = true;

            // Show visual notification to user
            Livewire.dispatch('notify', {
                type: 'warning',
                message: 'Pesanan baru masuk! Klik/tap layar untuk mengaktifkan suara notifikasi.'
            });

            // Try to unlock on next interaction
            unlockAudio();
            return;
        }

        console.log('[Ringtone] Audio element found, attempting to play...');
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
                    console.error('[Ringtone] Cannot play ringtone:', error.name, error.message);
                    console.warn('[Ringtone] Tip: Click/interact with page first to enable audio');

                    // Show visual notification
                    Livewire.dispatch('notify', {
                        type: 'warning',
                        message: 'Pesanan baru masuk! Tap layar untuk mengaktifkan suara.'
                    });

                    // Reset audio state and try again on next interaction
                    audioUnlocked = false;
                    audioEnabled = false;
                    localStorage.removeItem(AUDIO_ENABLED_KEY);

                    // Set as pending to retry
                    pendingRingtone = true;
                });
        }
    }

    // Listen event dari Livewire untuk play ringtone
    // Event ini akan di-trigger dari transactionEvents.js
    Livewire.on('play-order-ringtone', () => {
        console.log('[Ringtone] Play ringtone event received from Livewire');
        playRingtone();
    });
});
