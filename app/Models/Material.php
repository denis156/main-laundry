<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'unit',
        'initial_stock',
        'current_stock',
        'minimum_stock',
        'price_per_unit',
        'expired_date',
        'last_updated_by',
    ];

    protected $casts = [
        'initial_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'expired_date' => 'date',
    ];

    public function lastUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function stockHistories(): HasMany
    {
        return $this->hasMany(MaterialStockHistory::class);
    }
}
