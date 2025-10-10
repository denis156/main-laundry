<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipTier extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'min_points',
        'discount_percentage',
        'color',
        'benefits',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'min_points' => 'integer',
            'discount_percentage' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'benefits' => 'array',
        ];
    }

    /**
     * Relasi one-to-many dengan Member
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
