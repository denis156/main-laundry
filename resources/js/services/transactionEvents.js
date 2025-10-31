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
                // Pelanggan: Untuk status update (confirmed)
                if (event.action === 'created' && isRelevantForUser) {
                    logger.log('[Echo] Playing ringtone for NEW order (Kurir)');
                    Livewire.dispatch('play-order-ringtone');

                    // Show browser notification jika permission granted
                    showBrowserNotification(event, 'kurir');
                } else if (event.action === 'updated' && isRelevantForUser) {
                    // Untuk pelanggan: play ringtone ketika order dikonfirmasi
                    if (shouldPlayRingtoneForCustomer(event)) {
                        logger.log('[Echo] Playing ringtone for CONFIRMED order (Pelanggan)');
                        Livewire.dispatch('play-order-ringtone');

                        // Show browser notification jika permission granted
                        showBrowserNotification(event, 'pelanggan');
                    } else {
                        logger.log('[Echo] Skipping ringtone - status update only (not confirmed)');
                    }
                } else if (!isRelevantForUser) {
                    logger.log('[Echo] Skipping ringtone - transaction not relevant for this user');
                }

                // Dispatch Livewire events untuk refresh semua components
                // Kurir components
                Livewire.dispatch('refresh-orders');
                Livewire.dispatch('refresh-new-orders');
                Livewire.dispatch('refresh-stats');

                // Pelanggan components
                Livewire.dispatch('refresh-active-orders');

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
 * Pelanggan: Filter berdasarkan customer_id
 *
 * @param {Object} event - Transaction event dari broadcast
 * @returns {boolean} - True jika harus play ringtone, false jika skip
 */
function shouldPlayRingtone(event) {
    // Untuk pelanggan: cek apakah transaction ini milik customer yang login
    const customerId = window.CUSTOMER_ID;
    if (customerId) {
        const isMyOrder = event.customer_id === customerId;
        logger.log('[Echo] Customer ID:', customerId);
        logger.log('[Echo] Transaction customer ID:', event.customer_id);
        logger.log('[Echo] Is my order:', isMyOrder);
        return isMyOrder;
    }

    // Untuk kurir: filter berdasarkan area POS
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
 * Cek apakah customer harus play ringtone untuk order yang dikonfirmasi
 * Customer hanya play ringtone jika order mereka dikonfirmasi (status berubah ke 'confirmed')
 *
 * @param {Object} event - Transaction event dari broadcast
 * @returns {boolean} - True jika harus play ringtone, false jika skip
 */
function shouldPlayRingtoneForCustomer(event) {
    const customerId = window.CUSTOMER_ID;

    // Hanya untuk pelanggan yang login
    if (!customerId) {
        return false;
    }

    // Cek apakah ini order milik customer yang login
    if (event.customer_id !== customerId) {
        logger.log('[Echo] Not my order, skipping ringtone');
        return false;
    }

    // Cek apakah status berubah ke 'confirmed'
    if (event.workflow_status === 'confirmed') {
        logger.log('[Echo] Order confirmed for customer:', customerId);
        return true;
    }

    logger.log('[Echo] Order status is not confirmed:', event.workflow_status);
    return false;
}

/**
 * Show browser notification untuk pesanan baru atau konfirmasi
 * Jika app sedang tidak fokus, browser notification akan muncul
 * Jika app sedang fokus, skip notification (user sudah lihat di UI)
 *
 * @param {Object} event - Transaction event dari broadcast
 * @param {string} userType - 'kurir' atau 'pelanggan'
 */
function showBrowserNotification(event, userType = 'kurir') {
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
        let title, body, url;
        const serviceName = event.service_name || 'Layanan';
        const invoiceNumber = event.invoice_number || '-';
        const icon = '/image/app.png';
        const badge = '/image/app.png';
        const tag = `transaction-${event.transaction_id}`;

        if (userType === 'pelanggan') {
            // Notification untuk customer (order dikonfirmasi)
            const courierName = event.courier_name || 'Kurir';
            title = 'Pesanan Dikonfirmasi!';
            body = `${courierName} telah menerima pesanan ${serviceName} Anda (${invoiceNumber})`;
            url = `/pelanggan/detail-pesanan/${event.transaction_id}`;
        } else {
            // Notification untuk kurir (order baru)
            const customerName = event.customer_name || 'Customer';
            title = 'Pesanan Baru Masuk!';
            body = `Pesanan dari ${customerName} - ${serviceName} (${invoiceNumber})`;
            url = `/kurir/detail-pesanan/${event.transaction_id}`;
        }

        const notification = new Notification(title, {
            body: body,
            icon: icon,
            badge: badge,
            tag: tag,
            requireInteraction: true, // Notification tidak auto-close
            vibrate: [200, 100, 200, 100, 200],
            data: {
                transaction_id: event.transaction_id,
                url: url
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

        logger.log('[Notification] Browser notification shown for', userType);
    } catch (error) {
        logger.error('[Notification] Failed to show notification:', error);
    }
}
