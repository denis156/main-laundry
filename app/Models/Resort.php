<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resort extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'pic_name',
        'area_coverage',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'area_coverage' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi one-to-many dengan CourierMotorcycle
     */
    public function courierMotorcycles(): HasMany
    {
        return $this->hasMany(CourierMotorcycle::class, 'assigned_resort_id');
    }

    /**
     * Relasi one-to-many dengan Transaction
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
