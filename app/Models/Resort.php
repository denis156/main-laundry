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
        'district_code',
        'district_name',
        'village_code',
        'village_name',
        'detail_address',
        'address',
        'phone',
        'pic_name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi one-to-many dengan Pos
     * Resort bisa punya banyak Pos
     */
    public function pos(): HasMany
    {
        return $this->hasMany(Pos::class);
    }
}
