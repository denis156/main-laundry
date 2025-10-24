// ==========================================
// TRANSACTION EVENTS HANDLER (Real-time with Reverb)
// ==========================================
// Menangani event transaction dari Laravel Echo
// dan dispatch Livewire events untuk refresh components

import { logger } from './utils/logger.js';

document.addEventListener('livewire:init', () => {
    // Listen Echo channel untuk real-time transaction notifications
    if (window.Echo) {
        logger.log('[Echo] Subscribing to transactions channel...');

        window.Echo.channel('transactions')
            .listen('.transaction.event', (event) => {
                logger.log('[Echo] Transaction event received:', event);
                logger.log('[Echo] Action:', event.action);

                // Check apakah transaction ini relevan untuk kurir yang login
                const isRelevantForCourier = shouldPlayRingtone(event);
                logger.log('[Echo] Is relevant for this courier:', isRelevantForCourier);

                // Dispatch event untuk play ringtone HANYA untuk orderan baru (created)
                if (event.action === 'created' && isRelevantForCourier) {
                    logger.log('[Echo] Playing ringtone for NEW order');
                    Livewire.dispatch('play-order-ringtone');
                } else if (event.action === 'updated' && isRelevantForCourier) {
                    logger.log('[Echo] Skipping ringtone - status update only (not new order)');
                } else if (!isRelevantForCourier) {
                    logger.log('[Echo] Skipping ringtone - transaction outside courier area');
                }

                // Dispatch Livewire events to refresh all components
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
 * Check apakah transaction harus membunyikan ringtone untuk kurir ini
 * Logika sama dengan TransactionAreaFilter::isCustomerInPosArea() di backend
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

    // Check apakah village customer ada di area POS kurir
    const isInArea = posArea.includes(customerVillage);
    logger.log('[Echo] Customer village:', customerVillage);
    logger.log('[Echo] POS covers areas:', posArea);
    logger.log('[Echo] Is customer in area:', isInArea);

    return isInArea;
}
