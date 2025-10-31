// ==========================================
// TRANSACTION EVENTS HANDLER (Real-time with Reverb)
// ==========================================
// Menangani event transaction dari Laravel Echo
// dan dispatch Livewire events untuk refresh components
//
// PENTING: File ini HANYA handle real-time events via WebSocket (Echo/Reverb)
// Data transaksi TIDAK di-cache oleh Service Worker
// Jika offline, transaksi tidak akan muncul sampai online kembali

import { logger } from '../utils/logger.js';

document.addEventListener('livewire:init', () => {
    // Listen channel Echo untuk real-time transaction notifications
    if (window.Echo) {
        logger.log('[Echo] Subscribing to transactions channel...');

        window.Echo.channel('transactions')
            .listen('.transaction.event', (event) => {
                logger.log('[Echo] Transaction event received:', event);
                logger.log('[Echo] Action:', event.action);

                // Cek apakah transaction ini relevan untuk user yang login
                // Untuk kurir: Filter berdasarkan area (TransactionAreaFilter.php)
                // Untuk pelanggan: Filter berdasarkan customer_id
                const isRelevantForUser = shouldPlayRingtone(event);
                logger.log('[Echo] Is relevant for this user:', isRelevantForUser);

                // Dispatch event untuk play ringtone dan show notification
                // Kurir: HANYA untuk orderan baru (created)
                // Pelanggan: Untuk status update (confirmed) - akan diimplementasi nanti
                if (event.action === 'created' && isRelevantForUser) {
                    logger.log('[Echo] Playing ringtone for NEW order');
                    Livewire.dispatch('play-order-ringtone');

                    // Show browser notification jika permission granted
                    showBrowserNotification(event);
                } else if (event.action === 'updated' && isRelevantForUser) {
                    logger.log('[Echo] Skipping ringtone - status update only (not new order)');
                } else if (!isRelevantForUser) {
                    logger.log('[Echo] Skipping ringtone - transaction not relevant for this user');
                }

                // Dispatch Livewire events untuk refresh semua components
                // Semua component akan refresh otomatis tanpa polling
                Livewire.dispatch('refresh-orders');
                Livewire.dispatch('refresh-new-orders');
                Livewire.dispatch('refresh-stats');

                logger.log('[Echo] All components refreshed for action:', event.action);
            });

        logger.log('[Echo] Successfully subscribed to transactions channel');
    } else {
        logger.error('[Echo] Echo not found! Make sure Laravel Echo is loaded.');
    }
});

/**
 * Cek apakah transaction harus membunyikan ringtone untuk user ini
 * Kurir: Logika sama dengan TransactionAreaFilter::isCustomerInPosArea() di backend
 * Pelanggan: Filter berdasarkan customer_id (akan diimplementasi)
 *
 * @param {Object} event - Transaction event dari broadcast
 * @returns {boolean} - True jika harus play ringtone, false jika skip
 */
function shouldPlayRingtone(event) {
    // Ambil data area POS dari global config (di-set dari mobile.blade.php)
    const posArea = window.COURIER_POS_AREA || [];
    const customerVillage = event.customer_village;

    // Jika tidak ada area filter (POS tidak punya area atau kosong), play ringtone untuk semua
    if (!posArea || posArea.length === 0) {
        logger.log('[Echo] No area filter configured, playing ringtone for all transactions');
        return true;
    }

    // Jika customer tidak punya village_name, include (backward compatibility)
    // Sesuai dengan TransactionAreaFilter.php line 61
    if (!customerVillage || customerVillage === null) {
        logger.log('[Echo] Customer has no village, including for backward compatibility');
        return true;
    }

    // Cek apakah village customer ada di area POS kurir
    const isInArea = posArea.includes(customerVillage);
    logger.log('[Echo] Customer village:', customerVillage);
    logger.log('[Echo] POS covers areas:', posArea);
    logger.log('[Echo] Is customer in area:', isInArea);

    return isInArea;
}

/**
 * Show browser notification untuk pesanan baru
 * Jika app sedang tidak fokus, browser notification akan muncul
 * Jika app sedang fokus, skip notification (user sudah lihat di UI)
 *
 * @param {Object} event - Transaction event dari broadcast
 */
function showBrowserNotification(event) {
    logger.log('[Notification] Checking browser notification...');

    // Cek apakah browser support Notification API
    if (!('Notification' in window)) {
        logger.warn('[Notification] Browser tidak support Notification API');
        return;
    }

    // Cek permission
    if (Notification.permission !== 'granted') {
        logger.warn('[Notification] Notification permission not granted');
        return;
    }

    // Jika app sedang fokus, skip notification (user sudah lihat di UI)
    if (!document.hidden) {
        logger.log('[Notification] App is in focus, skipping notification');
        return;
    }

    // Create notification
    try {
        const customerName = event.customer_name || 'Customer';
        const serviceName = event.service_name || 'Layanan';
        const invoiceNumber = event.invoice_number || '-';

        const title = 'Pesanan Baru Masuk!';
        const body = `Pesanan dari ${customerName} - ${serviceName} (${invoiceNumber})`;
        const icon = '/image/app.png';
        const badge = '/image/app.png';
        const tag = `transaction-${event.transaction_id}`;

        const notification = new Notification(title, {
            body: body,
            icon: icon,
            badge: badge,
            tag: tag,
            requireInteraction: true, // Notification tidak auto-close
            vibrate: [200, 100, 200, 100, 200],
            data: {
                transaction_id: event.transaction_id,
                url: `/kurir/detail-pesanan/${event.transaction_id}`
            }
        });

        // Handle notification click
        notification.onclick = function(e) {
            e.preventDefault();
            window.focus();

            // Navigate ke detail pesanan
            if (notification.data && notification.data.url) {
                window.location.href = notification.data.url;
            }

            notification.close();
        };

        logger.log('[Notification] Browser notification shown');
    } catch (error) {
        logger.error('[Notification] Failed to show notification:', error);
    }
}
