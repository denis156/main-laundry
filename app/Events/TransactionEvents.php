<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
        return [
            'action' => $this->action, // 'created', 'updated', 'deleted'
            'transaction_id' => $this->transaction->id,
            'invoice_number' => $this->transaction->invoice_number,
            'customer_id' => $this->transaction->customer_id,
            'customer_name' => $this->transaction->customer?->name,
            'customer_village' => $this->transaction->customer?->village_name,
            'courier_id' => $this->transaction->courier_motorcycle_id,
            'pos_id' => $this->transaction->pos_id,
            'service_id' => $this->transaction->service_id,
            'service_name' => $this->transaction->service?->name,
            'workflow_status' => $this->transaction->workflow_status,
            'payment_timing' => $this->transaction->payment_timing,
            'payment_status' => $this->transaction->payment_status,
            'weight' => $this->transaction->weight ? (float) $this->transaction->weight : null,
            'price_per_kg' => (float) $this->transaction->price_per_kg,
            'total_price' => $this->transaction->total_price ? (float) $this->transaction->total_price : null,
            'order_date' => $this->transaction->order_date?->toISOString(),
            'estimated_finish_date' => $this->transaction->estimated_finish_date?->toISOString(),
            'timestamp' => now()->toISOString(),
        ];
    }
}
