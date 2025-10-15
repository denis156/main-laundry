<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pos extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pos';

    protected $fillable = [
        'resort_id',
        'name',
        'address',
        'phone',
        'pic_name',
        'area',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi many-to-one dengan Resort
     * Pos bisa terikat dengan Resort atau berdiri sendiri (resort_id null)
     */
    public function resort(): BelongsTo
    {
        return $this->belongsTo(Resort::class);
    }

    /**
     * Relasi one-to-many dengan CourierMotorcycle
     */
    public function courierMotorcycles(): HasMany
    {
        return $this->hasMany(CourierMotorcycle::class, 'assigned_pos_id');
    }

    /**
     * Relasi one-to-many dengan Transaction
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'pos_id');
    }
}
