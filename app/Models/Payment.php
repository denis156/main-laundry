<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'courier_motorcycle_id',
        'amount',
        'payment_proof_url',
        'payment_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'datetime',
        ];
    }

    /**
     * Relasi many-to-one dengan Transaction
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Relasi many-to-one dengan CourierMotorcycle
     */
    public function courierMotorcycle(): BelongsTo
    {
        return $this->belongsTo(CourierMotorcycle::class);
    }

    /**
     * Accessor: Format payment_date ke format Indonesia
     * Contoh output: "25 Jan 2025, 14:30"
     */
    protected function formattedPaymentDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->payment_date?->format('d M Y, H:i') ?? '-'
        );
    }

    /**
     * Accessor: Format amount ke format Rupiah
     * Contoh output: "Rp 50.000"
     */
    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format((float) ($this->amount ?? 0), 0, ',', '.')
        );
    }
}
