<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Exception;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WebPushApi extends Component
{
    /**
     * Subscribe user ke web push notifications
     * Support untuk kurir dan pelanggan
     * Dipanggil dari frontend setelah user grant permission
     */
    public function subscribe(array $subscription): void
    {
        // Cek guard mana yang sedang login
        /** @var \App\Models\CourierMotorcycle|\App\Models\Customer|null $user */
        $user = Auth::guard('courier')->user() ?? Auth::guard('customer')->user();

        if (!$user) {
            $this->dispatch('webpush-error', message: 'User not authenticated');
            return;
        }

        try {
            // Update atau create subscription
            // Package laravel-notification-channels/webpush otomatis handle ini via HasPushSubscriptions trait
            // Method updatePushSubscription() dari trait NotificationChannels\WebPush\HasPushSubscriptions
            $user->updatePushSubscription(
                $subscription['endpoint'],
                $subscription['keys']['p256dh'] ?? null,
                $subscription['keys']['auth'] ?? null
            );

            $this->dispatch('webpush-subscribed');
        } catch (Exception $e) {
            Log::error('Web Push subscription failed: ' . $e->getMessage());
            $this->dispatch('webpush-error', message: 'Failed to subscribe');
        }
    }

    /**
     * Unsubscribe user dari web push notifications
     * Support untuk kurir dan pelanggan
     */
    public function unsubscribe(string $endpoint): void
    {
        // Cek guard mana yang sedang login
        /** @var \App\Models\CourierMotorcycle|\App\Models\Customer|null $user */
        $user = Auth::guard('courier')->user() ?? Auth::guard('customer')->user();

        if (!$user) {
            $this->dispatch('webpush-error', message: 'User not authenticated');
            return;
        }

        try {
            // Delete subscription berdasarkan endpoint
            // Method deletePushSubscription() dari trait NotificationChannels\WebPush\HasPushSubscriptions
            $user->deletePushSubscription($endpoint);

            $this->dispatch('webpush-unsubscribed');
        } catch (Exception $e) {
            Log::error('Web Push unsubscription failed: ' . $e->getMessage());
            $this->dispatch('webpush-error', message: 'Failed to unsubscribe');
        }
    }

    /**
     * Get VAPID public key untuk frontend
     */
    public function getPublicKey(): string
    {
        return config('webpush.vapid.public_key');
    }
}
