<?php

declare(strict_types=1);

namespace App\Models;

use App\Helper\Database\CourierHelper;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use NotificationChannels\WebPush\PushSubscription;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Courier extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, HasPushSubscriptions;

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

    /**
     * Relasi polymorphic one-to-many dengan PushSubscription
     */
    public function pushSubscriptions(): MorphMany
    {
        return $this->morphMany(PushSubscription::class, 'subscribable');
    }

    /**
     * Get Filament avatar URL
     * Required untuk compatibility dengan Filament components
     */
    public function getFilamentAvatarUrl(): string
    {
        return CourierHelper::getFilamentAvatarUrl($this);
    }

    /**
     * Alias untuk assignedLocation (backward compatibility)
     */
    public function assignedPos(): BelongsTo
    {
        return $this->assignedLocation();
    }
}
