<?php

declare(strict_types=1);

namespace App\Livewire\Kurir\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WebPushApi extends Component
{
    /**
     * Subscribe kurir ke web push notifications
     * Dipanggil dari frontend setelah user grant permission
     */
    public function subscribe(array $subscription): void
    {
        $courier = Auth::guard('courier')->user();

        if (!$courier) {
            $this->dispatch('webpush-error', message: 'User not authenticated');
            return;
        }

        try {
            // Update atau create subscription
            // Package laravel-notification-channels/webpush otomatis handle ini via HasPushSubscriptions trait
            $courier->updatePushSubscription(
                $subscription['endpoint'],
                $subscription['keys']['p256dh'] ?? null,
                $subscription['keys']['auth'] ?? null
            );

            $this->dispatch('webpush-subscribed');
        } catch (\Exception $e) {
            \Log::error('Web Push subscription failed: ' . $e->getMessage());
            $this->dispatch('webpush-error', message: 'Failed to subscribe');
        }
    }

    /**
     * Unsubscribe kurir dari web push notifications
     */
    public function unsubscribe(string $endpoint): void
    {
        $courier = Auth::guard('courier')->user();

        if (!$courier) {
            $this->dispatch('webpush-error', message: 'User not authenticated');
            return;
        }

        try {
            // Delete subscription berdasarkan endpoint
            $courier->deletePushSubscription($endpoint);

            $this->dispatch('webpush-unsubscribed');
        } catch (\Exception $e) {
            \Log::error('Web Push unsubscription failed: ' . $e->getMessage());
            $this->dispatch('webpush-error', message: 'Failed to unsubscribe');
        }
    }

    /**
     * Get VAPID public key untuk frontend
     */
    public function getPublicKey(): string
    {
        return config('webpush.vapid.public_key', '');
    }
}
