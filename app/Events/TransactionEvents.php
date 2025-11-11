<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use App\Helper\Database\CourierHelper;
use Illuminate\Queue\SerializesModels;
use App\Helper\Database\CustomerHelper;
use App\Helper\Database\TransactionHelper;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TransactionEvents implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Transaction $transaction Transaction yang di-event
     * @param string $action Action yang terjadi: 'created', 'updated', 'deleted'
     */
    public function __construct(
        public Transaction $transaction,
        public string $action
    ) {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // Public channel untuk semua kurir
            new Channel('transactions'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'transaction.event';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $customer = $this->transaction->customer;
        $courier = $this->transaction->courier;
        $items = TransactionHelper::getItems($this->transaction);
        $defaultAddress = $customer ? CustomerHelper::getDefaultAddress($customer) : null;

        return [
            'action' => $this->action, // 'created', 'updated', 'deleted'
            'transaction_id' => $this->transaction->id,
            'invoice_number' => $this->transaction->invoice_number,
            'customer_id' => $this->transaction->customer_id,
            'customer_name' => $customer ? CustomerHelper::getName($customer) : null,
            'customer_village' => $defaultAddress['village_name'] ?? null,
            'courier_id' => $this->transaction->courier_id,
            'courier_name' => $courier ? CourierHelper::getName($courier) : null,
            'location_id' => $this->transaction->location_id,
            'location_name' => $this->transaction->location?->name,
            'service_name' => !empty($items) ? ($items[0]['service_name'] ?? null) : null,
            'workflow_status' => $this->transaction->workflow_status,
            'payment_timing' => TransactionHelper::getPaymentTiming($this->transaction),
            'payment_status' => $this->transaction->payment_status,
            'items' => $items,
            'total_price' => TransactionHelper::getTotalPrice($this->transaction),
            'created_at' => $this->transaction->created_at?->toISOString(),
            'updated_at' => $this->transaction->updated_at?->toISOString(),
            'timestamp' => now()->toISOString(),
        ];
    }
}
