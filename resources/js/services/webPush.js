// ==========================================
// WEB PUSH NOTIFICATION SERVICE
// ==========================================
// Menangani web push notifications untuk mobile apps (kurir & pelanggan)
// PENTING: Web Push bekerja via Service Worker, jadi tetap bisa menerima
// notifikasi bahkan saat app tertutup (background notifications)

import { logger } from '../utils/logger.js';

// Helper untuk convert VAPID key dari base64 ke Uint8Array
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

/**
 * Initialize web push notifications
 * Auto-subscribe jika user sudah pernah grant permission
 */
export async function initWebPush() {
    logger.log('[WebPush] Initializing...');

    // Check browser support
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        logger.warn('[WebPush] Browser tidak support web push notifications');
        return;
    }

    // PENTING: Skip jika user belum authenticated (VAPID key tidak tersedia)
    // VAPID key hanya di-set untuk authenticated user
    if (!window.VAPID_PUBLIC_KEY) {
        logger.log('[WebPush] VAPID key not available - skipping (user not authenticated)');
        return;
    }

    try {
        // Tunggu service worker ready
        const registration = await navigator.serviceWorker.ready;
        logger.log('[WebPush] Service worker ready');

        // Check existing subscription
        const existingSubscription = await registration.pushManager.getSubscription();

        if (existingSubscription) {
            logger.log('[WebPush] Subscription sudah ada, re-registering...');
            await sendSubscriptionToServer(existingSubscription);
        } else {
            logger.log('[WebPush] Belum ada subscription');
            // Tidak auto-subscribe, tunggu user request
        }

        // Check permission status
        const permission = Notification.permission;
        logger.log('[WebPush] Permission status:', permission);

        if (permission === 'granted' && !existingSubscription) {
            logger.log('[WebPush] Permission granted tapi belum subscribe, auto-subscribing...');
            await subscribeUser();
        }

    } catch (error) {
        logger.error('[WebPush] Initialization failed:', error);
    }
}

/**
 * Request permission dan subscribe user
 * Dipanggil saat user klik tombol "Enable Notifications" atau auto-subscribe dari PWA install
 * @param {boolean} showNotification - Show toast notification atau tidak (default: true)
 */
export async function subscribeUser(showNotification = true) {
    logger.log('[WebPush] Subscribing user...');

    try {
        // Check permission dulu
        let permission = Notification.permission;

        // Jika belum granted, request permission
        if (permission !== 'granted') {
            logger.log('[WebPush] Requesting permission...');
            permission = await Notification.requestPermission();
            logger.log('[WebPush] Permission result:', permission);

            if (permission !== 'granted') {
                logger.warn('[WebPush] Permission denied');
                // Silent fail, user sudah tau dari browser dialog
                return false;
            }
        }

        // Get service worker registration
        const registration = await navigator.serviceWorker.ready;

        // Get VAPID public key dari server
        const publicKey = await getVapidPublicKey();

        if (!publicKey) {
            throw new Error('VAPID public key tidak tersedia');
        }

        const convertedVapidKey = urlBase64ToUint8Array(publicKey);

        // Subscribe to push
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: convertedVapidKey
        });

        logger.log('[WebPush] Subscription created:', subscription);

        // Send subscription to server
        await sendSubscriptionToServer(subscription);

        logger.log('[WebPush] Subscribe success!');
        // Success sudah di-handle via toast di FabInstallApp component
        return true;

    } catch (error) {
        logger.error('[WebPush] Subscribe failed:', error);
        // Error handling, silent fail (sudah log di console)
        return false;
    }
}

/**
 * Unsubscribe user dari web push
 */
export async function unsubscribeUser() {
    logger.log('[WebPush] Unsubscribing...');

    try {
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();

        if (!subscription) {
            logger.warn('[WebPush] Tidak ada subscription aktif');
            return true;
        }

        // Unsubscribe dari browser
        await subscription.unsubscribe();
        logger.log('[WebPush] Unsubscribed from browser');

        // Delete dari server
        await deleteSubscriptionFromServer(subscription.endpoint);

        logger.log('[WebPush] Unsubscribe success!');
        return true;

    } catch (error) {
        logger.error('[WebPush] Unsubscribe failed:', error);
        return false;
    }
}

/**
 * Get VAPID public key dari server
 */
async function getVapidPublicKey() {
    // Prioritas 1: Ambil dari window variable (paling cepat)
    if (window.VAPID_PUBLIC_KEY) {
        logger.log('[WebPush] Using VAPID key from window variable');
        return window.VAPID_PUBLIC_KEY;
    }

    // Prioritas 2: Ambil dari Livewire component (fallback)
    try {
        const webPushComponent = Livewire.find('web-push-api');

        if (webPushComponent) {
            logger.log('[WebPush] Getting VAPID key from Livewire component...');
            const response = await webPushComponent.call('getPublicKey');
            return response || null;
        }
    } catch (error) {
        logger.error('[WebPush] Failed to get VAPID key from component:', error);
    }

    return null;
}

/**
 * Send subscription ke server
 */
async function sendSubscriptionToServer(subscription) {
    logger.log('[WebPush] Sending subscription to server...');

    try {
        const subscriptionData = {
            endpoint: subscription.endpoint,
            keys: {
                p256dh: arrayBufferToBase64(subscription.getKey('p256dh')),
                auth: arrayBufferToBase64(subscription.getKey('auth'))
            }
        };

        // Find WebPushApi Livewire component dan call method
        const webPushComponent = Livewire.find('web-push-api');

        if (!webPushComponent) {
            logger.warn('[WebPush] WebPushApi component not found, skipping server save');
            return;
        }

        await webPushComponent.call('subscribe', subscriptionData);

        logger.log('[WebPush] Subscription sent to server');
    } catch (error) {
        logger.error('[WebPush] Failed to send subscription:', error);
        throw error;
    }
}

/**
 * Delete subscription dari server
 */
async function deleteSubscriptionFromServer(endpoint) {
    logger.log('[WebPush] Deleting subscription from server...');

    try {
        // Find WebPushApi Livewire component dan call method
        const webPushComponent = Livewire.find('web-push-api');

        if (!webPushComponent) {
            logger.warn('[WebPush] WebPushApi component not found, skipping server delete');
            return;
        }

        await webPushComponent.call('unsubscribe', endpoint);
        logger.log('[WebPush] Subscription deleted from server');
    } catch (error) {
        logger.error('[WebPush] Failed to delete subscription:', error);
        throw error;
    }
}

/**
 * Convert ArrayBuffer to Base64
 */
function arrayBufferToBase64(buffer) {
    const bytes = new Uint8Array(buffer);
    let binary = '';
    for (let i = 0; i < bytes.byteLength; i++) {
        binary += String.fromCharCode(bytes[i]);
    }
    return window.btoa(binary);
}

/**
 * Check subscription status
 */
export async function getSubscriptionStatus() {
    try {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            return {
                supported: false,
                permission: 'default',
                subscribed: false
            };
        }

        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();

        return {
            supported: true,
            permission: Notification.permission,
            subscribed: !!subscription
        };
    } catch (error) {
        logger.error('[WebPush] Failed to get subscription status:', error);
        return {
            supported: false,
            permission: 'default',
            subscribed: false
        };
    }
}

// Auto-initialize saat Livewire ready
document.addEventListener('livewire:init', () => {
    logger.log('[WebPush] Livewire initialized, starting web push...');
    initWebPush();

    // Listen untuk events dari Livewire component
    Livewire.on('webpush-subscribed', () => {
        logger.log('[WebPush] Subscribed event received');
    });

    Livewire.on('webpush-unsubscribed', () => {
        logger.log('[WebPush] Unsubscribed event received');
    });

    Livewire.on('webpush-error', (event) => {
        logger.error('[WebPush] Error event received:', event.message);
    });

    // Listen untuk auto-subscribe event dari PWA install
    Livewire.on('auto-subscribe-webpush', async () => {
        logger.log('[WebPush] Auto-subscribe event received from PWA install');
        // showNotification = false karena sudah di-handle di serviceWorker.js
        await subscribeUser(false);
    });
});

// Export untuk digunakan di tempat lain
export default {
    init: initWebPush,
    subscribe: subscribeUser,
    unsubscribe: unsubscribeUser,
    getStatus: getSubscriptionStatus
};
