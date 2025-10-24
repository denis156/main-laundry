<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'notes',
        'order_date',
        'estimated_finish_date',
        'actual_finish_date',
        'tracking_token',
        'customer_ip',
        'customer_user_agent',
        'form_loaded_at',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'price_per_kg' => 'decimal:2',
            'total_price' => 'decimal:2',
            'order_date' => 'datetime',
            'estimated_finish_date' => 'datetime',
            'actual_finish_date' => 'datetime',
            'form_loaded_at' => 'datetime',
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
     * Accessor: Format order_date ke format Indonesia
     * Contoh output: "25 Jan 2025, 14:30"
     */
    protected function formattedOrderDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order_date?->format('d M Y, H:i') ?? '-'
        );
    }

    /**
     * Accessor: Get payment timing text
     * on_pickup -> "Bayar Saat Jemput"
     * on_delivery -> "Bayar Saat Antar"
     */
    protected function paymentTimingText(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->payment_timing === 'on_pickup' ? 'Bayar Saat Jemput' : 'Bayar Saat Antar'
        );
    }

    /**
     * Accessor: Format price_per_kg ke format Rupiah
     * Contoh output: "Rp 5.000"
     */
    protected function formattedPricePerKg(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format((float) ($this->price_per_kg ?? 0), 0, ',', '.')
        );
    }

    /**
     * Accessor: Format total_price ke format Rupiah
     * Contoh output: "Rp 50.000"
     */
    protected function formattedTotalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format((float) ($this->total_price ?? 0), 0, ',', '.')
        );
    }
}
