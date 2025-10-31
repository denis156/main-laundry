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
 * @param {string} swPath - Path ke service worker file (default: auto-detect)
 * @returns {Promise<ServiceWorkerRegistration>}
 */
export function registerServiceWorker(swPath = null) {
    // Jika sudah pernah dipanggil, return promise yang sama
    if (swRegistrationPromise) {
        return swRegistrationPromise;
    }

    // Auto-detect SW path berdasarkan current URL
    if (!swPath) {
        const currentPath = window.location.pathname;
        if (currentPath.startsWith('/kurir')) {
            swPath = '/sw-kurir.js';
        } else if (currentPath.startsWith('/pelanggan')) {
            swPath = '/sw-pelanggan.js';
        } else {
            swPath = '/sw.js'; // fallback
        }
    }

    if ('serviceWorker' in navigator) {
        swRegistrationPromise = navigator.serviceWorker
            .register(swPath)
            .then((registration) => {
                logger.log('[SW] Service Worker registered successfully:', registration.scope);
                logger.log('[SW] Using SW file:', swPath);

                // Setup auto-update checker
                setupAutoUpdateChecker(registration);

                // Check for updates on registration
                registration.update().catch(err => {
                    logger.warn('[SW] Initial update check failed:', err);
                });

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
 * Setup automatic update checker untuk service worker
 * Check for updates setiap 1 jam dan saat page visible
 * @param {ServiceWorkerRegistration} registration
 */
function setupAutoUpdateChecker(registration) {
    logger.log('[SW] Setting up auto-update checker...');

    // Check saat page load apakah ada SW waiting (dari refresh sebelumnya)
    if (registration.waiting) {
        logger.log('[SW] Found waiting service worker on page load, showing update modal...');
        if (window.Livewire) {
            window.Livewire.dispatch('sw-update-available');
        }
    }

    // Check for updates setiap 1 jam
    setInterval(() => {
        logger.log('[SW] Checking for updates (scheduled)...');
        registration.update().catch(err => {
            logger.warn('[SW] Update check failed:', err);
        });
    }, 60 * 60 * 1000); // 1 jam

    // Check for updates saat page menjadi visible (user buka app lagi)
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            logger.log('[SW] Page visible, checking for updates...');
            registration.update().catch(err => {
                logger.warn('[SW] Update check failed:', err);
            });
        }
    });

    // Listen untuk SW updates yang sudah waiting
    registration.addEventListener('updatefound', () => {
        const newWorker = registration.installing;
        logger.log('[SW] New service worker found, installing...');

        newWorker.addEventListener('statechange', () => {
            logger.log('[SW] New SW state:', newWorker.state);

            // Jika SW baru sudah installed dan waiting, tampilkan modal update
            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                logger.log('[SW] New service worker installed! Showing update modal...');

                // Dispatch event ke Livewire untuk show modal update
                if (window.Livewire) {
                    window.Livewire.dispatch('sw-update-available');
                }

                // TIDAK auto-reload lagi, biarkan user klik tombol di modal
                // Modal persistent akan force user untuk update
            }
        });
    });

    logger.log('[SW] Auto-update checker setup completed');
}

/**
 * Setup PWA install prompt handler
 * Capture beforeinstallprompt event dan simpan untuk digunakan nanti
 * ALWAYS register service worker on page load (untuk PWA yang sudah installed)
 */
export function setupPWAInstall() {
    // Prevent duplicate setup
    if (pwaInstallSetupDone) {
        logger.log('[PWA] Install handler already setup, skipping...');
        return;
    }

    logger.log('[PWA] Setting up install handler...');

    // PENTING: Auto-register service worker setiap page load
    // Ini memastikan service worker tetap registered bahkan setelah clear cache
    registerServiceWorker().catch(error => {
        logger.error('[PWA] Failed to register service worker on page load:', error);
    });

    window.addEventListener('beforeinstallprompt', (e) => {
        // PENTING: Prevent default mini-infobar/banner dari muncul IMMEDIATELY
        e.preventDefault();

        // Simpan event untuk trigger nanti
        deferredPrompt = e;

        logger.log('[PWA] beforeinstallprompt event captured and prevented');

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
 * Setelah user accept install, otomatis request notification permission
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

    if (outcome === 'accepted') {
        logger.log('[PWA] User accepted the install prompt');

        // Service worker sudah auto-registered di setupPWAInstall(), tidak perlu register lagi

        // OTOMATIS request notification permission setelah install accepted
        await requestNotificationPermission();
    } else {
        logger.log('[PWA] User dismissed the install prompt');
    }

    // Reset deferred prompt
    deferredPrompt = null;

    return outcome;
}

/**
 * Request notification permission dan subscribe ke web push
 * Dipanggil otomatis setelah user install PWA
 */
async function requestNotificationPermission() {
    logger.log('[Notification] Auto-requesting permission after PWA install...');

    // Check browser support
    if (!('Notification' in window)) {
        logger.warn('[Notification] Browser tidak support notifications');
        return;
    }

    // Check current permission
    const currentPermission = Notification.permission;
    logger.log('[Notification] Current permission:', currentPermission);

    // Jika sudah granted, langsung subscribe
    if (currentPermission === 'granted') {
        logger.log('[Notification] Permission already granted, subscribing...');
        await subscribeToWebPush();
        return;
    }

    // Jika sudah denied, skip (tidak bisa request lagi)
    if (currentPermission === 'denied') {
        logger.warn('[Notification] Permission denied, cannot request again');
        // Toast sudah di-handle di serviceWorker.js, tidak perlu dispatch lagi
        return;
    }

    // Request permission (default state)
    try {
        const permission = await Notification.requestPermission();
        logger.log('[Notification] Permission result:', permission);

        if (permission === 'granted') {
            logger.log('[Notification] Permission granted! Subscribing to web push...');

            // Subscribe ke web push
            await subscribeToWebPush();

            logger.log('[Notification] Web push subscription completed!');
        } else {
            logger.warn('[Notification] Permission denied by user');
            // User denied, silent fail (tidak show toast, sudah jelas dari browser dialog)
        }
    } catch (error) {
        logger.error('[Notification] Failed to request permission:', error);
    }
}

/**
 * Subscribe ke web push notifications
 * Menggunakan webPush service
 */
async function subscribeToWebPush() {
    try {
        // Import subscribeUser dari webPush service
        // Kita akan dispatch event ke webPush service untuk subscribe
        if (window.Livewire) {
            logger.log('[Notification] Dispatching subscribe event to webPush service...');
            // Trigger web push subscription via Livewire event
            window.Livewire.dispatch('auto-subscribe-webpush');
        }
    } catch (error) {
        logger.error('[Notification] Failed to subscribe to web push:', error);
    }
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
