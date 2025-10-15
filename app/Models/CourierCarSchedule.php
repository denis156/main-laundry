<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourierCarSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'courier_cars_schedules';

    protected $fillable = [
        'trip_date',
        'departure_time',
        'trip_type',
        'pos_ids',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'trip_date' => 'date',
            'pos_ids' => 'array',
        ];
    }
}
