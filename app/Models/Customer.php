<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Panel;
use App\Helper\Database\CustomerHelper;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable implements FilamentUser
{
    use HasFactory, SoftDeletes, Notifiable, HasPushSubscriptions;

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
     * Boot method untuk auto-fill addresses data yang kosong
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($customer) {
            CustomerHelper::autoFillCustomerAddresses($customer);
        });
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

    /**
     * Get Filament avatar URL
     */
    public function getFilamentAvatarUrl(): string
    {
        return CustomerHelper::getFilamentAvatarUrl($this);
    }

    /**
     * Get customer default address formatted string
     */
    public function getAddressAttribute(): ?string
    {
        $address = CustomerHelper::getDefaultAddress($this);
        if (!$address) {
            return null;
        }

        $parts = [];
        if (!empty($address['detail_address'])) {
            $parts[] = $address['detail_address'];
        }
        if (!empty($address['village_name'])) {
            $parts[] = $address['village_name'];
        }
        if (!empty($address['district_name'])) {
            $parts[] = $address['district_name'];
        }

        return !empty($parts) ? implode(', ', $parts) : null;
    }

    /**
     * Get customer name from JSONB
     * Using Laravel's Attribute accessor pattern
     */
    protected function name(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => CustomerHelper::getName($this),
        );
    }
}
