<script>
    // ==========================================
    // ORDER NOTIFICATION RINGTONE SYSTEM
    // ==========================================
    document.addEventListener('livewire:init', () => {
        const ringtone = document.getElementById('order-ringtone');
        const STORAGE_KEY = 'kurir_order_previous_count';
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

        // Listen event dari Livewire
        Livewire.on('order-count-updated', (event) => {
            const currentCount = event.count;

            // Get previous count dari localStorage
            const previousCountStr = localStorage.getItem(STORAGE_KEY);
            const previousCount = previousCountStr ? parseInt(previousCountStr) : currentCount;

            // Jika ada pesanan baru (count bertambah)
            if (currentCount > previousCount) {
                // Try to play ringtone
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
                            })
                            .catch(() => {
                                // Try to enable audio on next interaction
                                audioEnabled = false;
                                localStorage.removeItem(AUDIO_ENABLED_KEY);
                            });
                    }
                }
            }

            // Simpan current count ke localStorage
            localStorage.setItem(STORAGE_KEY, currentCount.toString());
        });
    });
</script>
