<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\TransactionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'service_id',
        'courier_motorcycle_id',
        'pos_id',
        'weight',
        'price_per_kg',
        'total_price',
        'workflow_status',
        'payment_timing',
        'payment_status',
        'payment_proof_url',
        'paid_at',
        'notes',
        'order_date',
        'estimated_finish_date',
        'actual_finish_date',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'price_per_kg' => 'decimal:2',
            'total_price' => 'decimal:2',
            'paid_at' => 'datetime',
            'order_date' => 'datetime',
            'estimated_finish_date' => 'datetime',
            'actual_finish_date' => 'datetime',
        ];
    }

    /**
     * Relasi many-to-one dengan Customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relasi many-to-one dengan Service
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relasi many-to-one dengan CourierMotorcycle
     */
    public function courierMotorcycle(): BelongsTo
    {
        return $this->belongsTo(CourierMotorcycle::class);
    }

    /**
     * Relasi many-to-one dengan Pos
     */
    public function pos(): BelongsTo
    {
        return $this->belongsTo(Pos::class);
    }

    /**
     * Relasi one-to-many dengan Payment
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Update status transaksi melalui service
     */
    public function updateStatus(string $status): void
    {
        app(TransactionService::class)->updateStatus($this, $status);
    }

    /**
     * Update status pembayaran melalui service
     */
    public function updatePaymentStatus(): void
    {
        app(TransactionService::class)->updatePaymentStatus($this);
    }

    /**
     * Dapatkan sisa pembayaran melalui service
     */
    public function getRemainingPayment(): float
    {
        return app(TransactionService::class)->getRemainingPayment($this);
    }

    /**
     * Cek apakah sudah lunas melalui service
     */
    public function isFullyPaid(): bool
    {
        return app(TransactionService::class)->isFullyPaid($this);
    }

    /**
     * Batalkan transaksi melalui service
     */
    public function cancel(): void
    {
        app(TransactionService::class)->cancelTransaction($this);
    }
}
