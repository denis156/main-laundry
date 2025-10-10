<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_transaction',
        'max_discount',
        'usage_limit',
        'usage_count',
        'usage_per_user',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_transaction' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'usage_limit' => 'integer',
            'usage_count' => 'integer',
            'usage_per_user' => 'integer',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi one-to-many dengan Transaction
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Cek apakah promo masih valid (aktif, belum expired, belum habis kuota)
     */
    public function isValid(): bool
    {
        $now = now();
        return $this->is_active
            && $now->between($this->valid_from, $this->valid_until)
            && ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }

    /**
     * Cek apakah promo bisa digunakan untuk nominal transaksi tertentu
     */
    public function canBeUsedFor(float $amount): bool
    {
        return $this->isValid() && $amount >= $this->min_transaction;
    }

    /**
     * Hitung nominal diskon berdasarkan type promo (percentage/fixed)
     */
    public function calculateDiscount(float $amount): float
    {
        if (!$this->canBeUsedFor($amount)) {
            return 0.0;
        }

        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;

            // Terapkan max discount jika ada
            if ($this->max_discount !== null && $discount > $this->max_discount) {
                $discount = (float) $this->max_discount;
            }

            return $discount;
        }

        // Diskon fixed
        return (float) $this->value;
    }

    /**
     * Tambah counter penggunaan promo
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }
}
