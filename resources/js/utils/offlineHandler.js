// ==========================================
// OFFLINE HANDLER
// ==========================================
// Menangani behavior aplikasi saat offline
// - Disable Livewire prefetch saat offline
// - Suppress error messages
// - Redirect ke offline page jika navigate

import { logger } from './logger.js';

/**
 * Setup offline handler untuk Livewire
 * Disable prefetch dan handle navigation errors saat offline
 */
export function setupOfflineHandler() {
    logger.log('[Offline] Setting up offline handler...');

    // Listen ke online/offline events
    window.addEventListener('online', handleOnline);
    window.addEventListener('offline', handleOffline);

    // Check initial state
    if (!navigator.onLine) {
        handleOffline();
    }

    // Handle Livewire errors saat offline
    document.addEventListener('livewire:init', () => {
        // Disable Livewire prefetch saat offline
        if (!navigator.onLine) {
            logger.log('[Offline] Disabling Livewire prefetch (offline mode)');
            disableLivewirePrefetch();
        }

        // Listen ke navigation errors
        window.addEventListener('livewire:navigate', (event) => {
            if (!navigator.onLine) {
                logger.log('[Offline] Navigation blocked - redirecting to offline page');
                // Prevent navigation
                event.preventDefault();
                // Redirect ke offline page
                window.location.href = '/kurir/offline';
            }
        });
    });

    logger.log('[Offline] Offline handler setup completed');
}

/**
 * Handle saat koneksi online
 */
function handleOnline() {
    logger.log('[Offline] Connection restored - Online');

    // Re-enable Livewire prefetch
    enableLivewirePrefetch();

    // Show toast notification (jika ada)
    if (window.Livewire) {
        window.Livewire.dispatch('connection-restored');
    }
}

/**
 * Handle saat koneksi offline
 */
function handleOffline() {
    logger.log('[Offline] Connection lost - Offline');

    // Disable Livewire prefetch
    disableLivewirePrefetch();

    // Show toast notification (jika ada)
    if (window.Livewire) {
        window.Livewire.dispatch('connection-lost');
    }
}

/**
 * Disable Livewire prefetch
 * Menghilangkan hover prefetch untuk menghindari failed fetch errors
 */
function disableLivewirePrefetch() {
    // Remove wire:navigate.hover dari semua links
    const links = document.querySelectorAll('[wire\\:navigate]');
    links.forEach(link => {
        // Store original attribute
        const wireNavigate = link.getAttribute('wire:navigate');
        link.setAttribute('data-wire-navigate-original', wireNavigate);

        // Disable prefetch by removing wire:navigate temporarily
        // Livewire akan tetap navigate saat click, tapi tidak prefetch
        link.removeAttribute('wire:navigate');

        // Add custom class untuk styling
        link.classList.add('navigate-offline-mode');
    });

    logger.log('[Offline] Livewire prefetch disabled for all links');
}

/**
 * Enable Livewire prefetch
 * Restore wire:navigate untuk semua links
 */
function enableLivewirePrefetch() {
    const links = document.querySelectorAll('[data-wire-navigate-original]');
    links.forEach(link => {
        // Restore original attribute
        const originalValue = link.getAttribute('data-wire-navigate-original');
        link.setAttribute('wire:navigate', originalValue);
        link.removeAttribute('data-wire-navigate-original');

        // Remove custom class
        link.classList.remove('navigate-offline-mode');
    });

    logger.log('[Offline] Livewire prefetch enabled for all links');
}

/**
 * Suppress uncaught promise errors dari Livewire saat offline
 * Livewire prefetch akan throw "Failed to fetch" saat offline
 */
window.addEventListener('unhandledrejection', (event) => {
    // Check jika error dari Livewire fetch saat offline
    if (!navigator.onLine && event.reason?.message?.includes('Failed to fetch')) {
        logger.log('[Offline] Suppressed Livewire fetch error (offline mode)');
        // Prevent error dari muncul di console
        event.preventDefault();
    }
});
