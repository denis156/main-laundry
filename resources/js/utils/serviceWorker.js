// ==========================================
// SERVICE WORKER & PWA INSTALL HANDLER
// ==========================================
// Menangani registrasi service worker dan install PWA prompt

import { logger } from './logger.js';

let deferredPrompt = null;
let swRegistrationPromise = null;
let pwaInstallSetupDone = false; // Flag untuk prevent duplicate setup

/**
 * Register service worker
 * Service worker akan di-cache untuk offline functionality
 * @returns {Promise<ServiceWorkerRegistration>}
 */
export function registerServiceWorker() {
    // Jika sudah pernah dipanggil, return promise yang sama
    if (swRegistrationPromise) {
        return swRegistrationPromise;
    }

    if ('serviceWorker' in navigator) {
        swRegistrationPromise = navigator.serviceWorker
            .register('/sw.js')
            .then((registration) => {
                logger.log('[SW] Service Worker registered successfully:', registration.scope);
                return registration;
            })
            .catch((error) => {
                logger.error('[SW] Service Worker registration failed:', error);
                swRegistrationPromise = null; // Reset agar bisa retry
                throw error;
            });

        return swRegistrationPromise;
    } else {
        logger.warn('[SW] Service Worker not supported in this browser');
        return Promise.reject(new Error('Service Worker not supported'));
    }
}

/**
 * Setup PWA install prompt handler
 * Capture beforeinstallprompt event dan simpan untuk digunakan nanti
 */
export function setupPWAInstall() {
    // Prevent duplicate setup
    if (pwaInstallSetupDone) {
        logger.log('[PWA] Install handler already setup, skipping...');
        return;
    }

    logger.log('[PWA] Setting up install handler...');

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent default mini-infobar dari muncul di mobile
        e.preventDefault();

        // Simpan event untuk trigger nanti
        deferredPrompt = e;

        logger.log('[PWA] beforeinstallprompt event captured');

        // Dispatch event ke Livewire untuk show FAB button
        if (window.Livewire) {
            window.Livewire.dispatch('pwa-installable');
        }
    });

    // Listen untuk event setelah app ter-install
    window.addEventListener('appinstalled', () => {
        logger.log('[PWA] App installed successfully');
        deferredPrompt = null;

        // Dispatch event ke Livewire
        if (window.Livewire) {
            window.Livewire.dispatch('pwa-installed');
        }
    });

    pwaInstallSetupDone = true;
    logger.log('[PWA] Install handler setup completed');
}

/**
 * Trigger PWA install prompt
 * Function ini akan dipanggil dari Livewire component
 * @returns {Promise<string>} - 'accepted' atau 'dismissed'
 */
export async function promptPWAInstall() {
    if (!deferredPrompt) {
        logger.warn('[PWA] Install prompt not available');
        return 'unavailable';
    }

    // Show install prompt
    deferredPrompt.prompt();

    // Wait for user response
    const { outcome } = await deferredPrompt.userChoice;

    logger.log('[PWA] User choice:', outcome);

    // Jika user accept, baru register service worker
    if (outcome === 'accepted') {
        logger.log('[PWA] User accepted, registering service worker...');
        await registerServiceWorker();
    } else {
        logger.log('[PWA] User dismissed, service worker NOT registered');
    }

    // Reset deferred prompt
    deferredPrompt = null;

    return outcome;
}

/**
 * Check apakah PWA sudah ter-install atau sedang berjalan dalam standalone mode
 * @returns {boolean}
 */
export function isPWAInstalled() {
    // Check jika app berjalan dalam standalone mode
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches;

    // Check iOS standalone mode
    const isIOSStandalone = ('standalone' in window.navigator) && window.navigator.standalone;

    return isStandalone || isIOSStandalone;
}

/**
 * Check apakah PWA bisa di-install (deferred prompt tersedia)
 * @returns {boolean}
 */
export function canInstallPWA() {
    return deferredPrompt !== null;
}

// Expose functions ke window object agar bisa dipanggil dari Livewire
window.pwaInstall = {
    prompt: promptPWAInstall,
    isInstalled: isPWAInstalled,
    canInstall: canInstallPWA
};
