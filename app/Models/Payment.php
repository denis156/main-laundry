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
        'courier_id',
        'amount',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'data' => 'array',
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
     * Relasi many-to-one dengan Courier
     */
    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }
}
