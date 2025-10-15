<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipments';

    protected $fillable = [
        'name',
        'type',
        'brand',
        'serial_number',
        'purchase_price',
        'purchase_date',
        'status',
        'last_maintenance_date',
        'last_maintenance_cost',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'last_maintenance_cost' => 'decimal:2',
        'purchase_date' => 'date',
        'last_maintenance_date' => 'date',
    ];

    public function maintenances(): HasMany
    {
        return $this->hasMany(EquipmentMaintenance::class);
    }
}
