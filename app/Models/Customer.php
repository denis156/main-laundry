<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use Filament\Panel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable implements FilamentUser
{
    use HasFactory, SoftDeletes, HasPushSubscriptions, Notifiable;

    protected $fillable = [
        'email',
        'phone',
        'password',
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
     * Customer TIDAK BOLEH mengakses panel admin
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return false;
    }

    /**
     * Relasi one-to-many dengan Transaction
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
