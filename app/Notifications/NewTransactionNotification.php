<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewTransactionNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Transaction $transaction
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $customerName = $this->transaction->customer?->name ?? 'Customer';
        $serviceName = $this->transaction->service?->name ?? 'Layanan';
        $invoiceNumber = $this->transaction->invoice_number;

        return (new WebPushMessage)
            ->title('Pesanan Baru Masuk!')
            ->icon('/image/app.png')
            ->badge('/image/app.png')
            ->body("Pesanan dari {$customerName} - {$serviceName} ({$invoiceNumber})")
            ->action('Lihat Detail', 'view_transaction')
            ->data([
                'transaction_id' => $this->transaction->id,
                'invoice_number' => $invoiceNumber,
                'customer_name' => $customerName,
                'service_name' => $serviceName,
                'url' => route('kurir.detail-pesanan', ['id' => $this->transaction->id]),
            ])
            ->options([
                'TTL' => 3600, // 1 jam
            ])
            ->vibrate([200, 100, 200, 100, 200]); // Vibration pattern untuk mobile
    }
}
