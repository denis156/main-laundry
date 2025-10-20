<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreatedTransaction implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Transaction $transaction)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast ke channel kurir untuk notifikasi real-time
        return [
            new Channel('kurir-orders'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-transaction';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'action' => 'created',
            'transaction_id' => $this->transaction->id,
            'invoice_number' => $this->transaction->invoice_number,
            'customer_id' => $this->transaction->customer_id,
            'customer_name' => $this->transaction->customer?->name,
            'customer_village' => $this->transaction->customer?->village_name,
            'service_id' => $this->transaction->service_id,
            'service_name' => $this->transaction->service?->name,
            'workflow_status' => $this->transaction->workflow_status,
            'payment_timing' => $this->transaction->payment_timing,
            'payment_status' => $this->transaction->payment_status,
            'price_per_kg' => (float) $this->transaction->price_per_kg,
            'order_date' => $this->transaction->order_date?->toISOString(),
            'estimated_finish_date' => $this->transaction->estimated_finish_date?->toISOString(),
        ];
    }
}
