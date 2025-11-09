<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'is_featured',
        'sort_order',
        'data',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
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
}
