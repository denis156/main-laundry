// ==========================================
// NEW ORDER RINGTONE HANDLER
// ==========================================
// Menangani audio ringtone untuk notifikasi pesanan baru masuk
// File ini hanya fokus pada audio ringtone, bukan dispatch events

import { logger } from './utils/logger.js';

document.addEventListener('livewire:init', () => {
    const ringtone = document.getElementById('order-ringtone');
    const AUDIO_ENABLED_KEY = 'kurir_audio_enabled';

    // Cek apakah audio sudah pernah di-enable sebelumnya
    let audioEnabled = localStorage.getItem(AUDIO_ENABLED_KEY) === 'true';
    let audioUnlocked = false; // Selalu mulai dari false, audio context perlu di-unlock setiap page load
    let pendingRingtone = false;

    // Definisikan array events terlebih dahulu agar bisa diakses di unlockAudio
    // Tambahkan lebih banyak event untuk unlock audio lebih cepat
    const events = ['click', 'touchstart', 'touchend', 'mousedown', 'keydown', 'scroll', 'mousemove', 'touchmove'];

    // Fungsi untuk handle interaksi user - didefinisikan lebih awal untuk referensi
    function handleUserInteraction() {
        if (!audioUnlocked) {
            unlockAudio();
        }
    }

    function unlockAudio() {
        if (!ringtone || audioUnlocked) return;

        logger.log('[Ringtone] Unlocking audio context...');

        // Load audio
        ringtone.load();

        // Coba play audio dengan volume kecil untuk unlock audio context
        ringtone.volume = 0.01;
        ringtone.currentTime = 0;

        const unlockPromise = ringtone.play();

        if (unlockPromise !== undefined) {
            unlockPromise
                .then(() => {
                    // Pause segera setelah playing
                    ringtone.pause();
                    ringtone.currentTime = 0;

                    audioUnlocked = true;
                    audioEnabled = true;
                    localStorage.setItem(AUDIO_ENABLED_KEY, 'true');

                    logger.log('[Ringtone] Audio unlocked successfully');

                    // Hapus event listeners setelah unlock berhasil
                    events.forEach(eventName => {
                        document.removeEventListener(eventName, handleUserInteraction);
                    });

                    // Jika ada pending ringtone, play sekarang
                    if (pendingRingtone) {
                        pendingRingtone = false;
                        setTimeout(() => playRingtone(), 100);
                    }
                })
                .catch(() => {
                    logger.log('[Ringtone] Audio not unlocked yet, waiting for user interaction...');
                });
        }
    }

    // Coba enable segera (akan gagal sampai ada user interaction)
    if (ringtone) {
        ringtone.load();
        unlockAudio();
    }

    // Tambahkan event listeners yang akan retry sampai audio ter-unlock
    // Gunakan once: true untuk click/touch agar lebih efficient
    document.addEventListener('click', handleUserInteraction, { passive: true, once: false });
    document.addEventListener('touchstart', handleUserInteraction, { passive: true, once: false });
    document.addEventListener('touchend', handleUserInteraction, { passive: true, once: false });
    document.addEventListener('mousedown', handleUserInteraction, { passive: true, once: false });
    document.addEventListener('keydown', handleUserInteraction, { passive: true, once: false });

    // Gunakan once: true untuk scroll/mousemove karena sering triggered
    document.addEventListener('scroll', handleUserInteraction, { passive: true, once: true });
    document.addEventListener('mousemove', handleUserInteraction, { passive: true, once: true });
    document.addEventListener('touchmove', handleUserInteraction, { passive: true, once: true });

    // Coba unlock saat Livewire navigasi ke halaman baru
    Livewire.hook('navigated', () => {
        if (!audioUnlocked) {
            logger.log('[Ringtone] Livewire navigated, attempting unlock...');
            unlockAudio();
        }
    });

    // Coba unlock saat page menjadi visible (untuk PWA atau tab switching)
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && !audioUnlocked) {
            logger.log('[Ringtone] Page became visible, attempting unlock...');
            unlockAudio();
        }
    });

    function playRingtone() {
        logger.log('[Ringtone] playRingtone() called');

        if (!ringtone) {
            logger.error('[Ringtone] Audio element not found!');
            return;
        }

        // Cek apakah audio sudah ter-unlock
        if (!audioUnlocked) {
            logger.warn('[Ringtone] Audio not unlocked yet, setting as pending...');
            pendingRingtone = true;

            // Tampilkan notifikasi visual ke user
            Livewire.dispatch('notify', {
                type: 'warning',
                message: 'Pesanan baru masuk! Klik/tap layar untuk mengaktifkan suara notifikasi.'
            });

            // Coba unlock di interaksi berikutnya
            unlockAudio();
            return;
        }

        logger.log('[Ringtone] Audio element found, attempting to play...');
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
                    logger.log('[Ringtone] Played successfully');
                })
                .catch((error) => {
                    logger.error('[Ringtone] Cannot play ringtone:', error.name, error.message);
                    logger.warn('[Ringtone] Tip: Click/interact with page first to enable audio');

                    // Tampilkan notifikasi visual
                    Livewire.dispatch('notify', {
                        type: 'warning',
                        message: 'Pesanan baru masuk! Tap layar untuk mengaktifkan suara.'
                    });

                    // Reset audio state dan coba lagi di interaksi berikutnya
                    audioUnlocked = false;
                    audioEnabled = false;
                    localStorage.removeItem(AUDIO_ENABLED_KEY);

                    // Set sebagai pending untuk retry
                    pendingRingtone = true;
                });
        }
    }

    // Listen event dari Livewire untuk play ringtone
    // Event ini akan di-trigger dari transactionEvents.js
    Livewire.on('play-order-ringtone', () => {
        logger.log('[Ringtone] Play ringtone event received from Livewire');
        playRingtone();
    });
});
