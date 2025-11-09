<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Helper\Database\CustomerHelper;
use App\Helper\Database\TransactionHelper;
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
    public function via(): array
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush(): WebPushMessage
    {
        $customerName = $this->transaction->customer
            ? CustomerHelper::getName($this->transaction->customer)
            : 'Customer';

        // Get service name dari items di JSONB
        $items = TransactionHelper::getItems($this->transaction);
        $serviceName = !empty($items) ? ($items[0]['service_name'] ?? 'Layanan') : 'Layanan';

        $invoiceNumber = $this->transaction->invoice_number;

        return (new WebPushMessage)
            ->title('Pesanan Baru Masuk!')
            ->icon('/image/manifest-icons/main-512x512-notif.png')
            ->badge('/image/manifest-icons/main-512x512-notif.png')
            ->body("Pesanan dari {$customerName} - {$serviceName} ({$invoiceNumber})")
            ->action('Lihat Detail', 'view_transaction')
            ->data([
                'transaction_id' => $this->transaction->id,
                'invoice_number' => $invoiceNumber,
                'customer_name' => $customerName,
                'service_name' => $serviceName,
                'url' => route('kurir.pesanan', ['id' => $this->transaction->id]),
            ])
            ->options([
                'TTL' => 3600, // 1 jam
            ])
            ->vibrate([200, 100, 200, 100, 200]); // Vibration pattern untuk mobile
    }
}
