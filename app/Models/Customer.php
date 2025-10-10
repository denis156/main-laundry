<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\MemberService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
    ];

    /**
     * Relasi one-to-one dengan Member
     */
    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    /**
     * Relasi one-to-many dengan Transaction
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Cek apakah customer sudah menjadi member
     */
    public function isMember(): bool
    {
        return $this->member()->exists();
    }

    /**
     * Jadikan customer menjadi member
     */
    public function becomeMember(): Member
    {
        return app(MemberService::class)->createMember($this);
    }
}
