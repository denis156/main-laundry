<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\TransactionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'member_id',
        'promo_id',
        'user_id',
        'total_weight',
        'subtotal',
        'member_discount_amount',
        'member_discount_percentage',
        'promo_discount_amount',
        'total_discount_amount',
        'total_price',
        'points_earned',
        'status',
        'payment_status',
        'paid_amount',
        'notes',
        'order_date',
        'estimated_finish_date',
        'actual_finish_date',
    ];

    protected function casts(): array
    {
        return [
            'total_weight' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'member_discount_amount' => 'decimal:2',
            'member_discount_percentage' => 'decimal:2',
            'promo_discount_amount' => 'decimal:2',
            'total_discount_amount' => 'decimal:2',
            'total_price' => 'decimal:2',
            'points_earned' => 'integer',
            'paid_amount' => 'decimal:2',
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
     * Relasi many-to-one dengan Member
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Relasi many-to-one dengan Promo
     */
    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    /**
     * Relasi many-to-one dengan User (kasir)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi one-to-many dengan TransactionDetail
     */
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
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
