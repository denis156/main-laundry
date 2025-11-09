<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Courier extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'assigned_location_id',
        'data',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi many-to-one dengan Location
     */
    public function assignedLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'assigned_location_id');
    }

    /**
     * Relasi one-to-many dengan Transaction
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'courier_id');
    }

    /**
     * Relasi one-to-many dengan Payment
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'courier_id');
    }
}
