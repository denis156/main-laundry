<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClothingType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'data',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
