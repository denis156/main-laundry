<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\MemberService;
use App\Services\PointService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'customer_id',
        'membership_tier_id',
        'member_number',
        'member_since',
        'total_points',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'member_since' => 'date',
            'total_points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi many-to-one dengan Customer
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relasi many-to-one dengan MembershipTier
     */
    public function membershipTier(): BelongsTo
    {
        return $this->belongsTo(MembershipTier::class);
    }

    /**
     * Relasi one-to-many dengan Transaction
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Tambah poin member melalui service
     */
    public function addPoints(int $points): void
    {
        app(MemberService::class)->addPoints($this, $points);
    }

    /**
     * Kurangi poin member melalui service
     */
    public function deductPoints(int $points): void
    {
        app(MemberService::class)->deductPoints($this, $points);
    }

    /**
     * Update tier member berdasarkan poin melalui service
     */
    public function updateTier(): void
    {
        app(MemberService::class)->updateMemberTier($this);
    }

    /**
     * Dapatkan persentase diskon melalui service
     */
    public function getDiscountPercentage(): float
    {
        return app(MemberService::class)->getDiscountPercentage($this);
    }

    /**
     * Aktifkan member melalui service
     */
    public function activate(): void
    {
        app(MemberService::class)->activate($this);
    }

    /**
     * Nonaktifkan member melalui service
     */
    public function deactivate(): void
    {
        app(MemberService::class)->deactivate($this);
    }

    /**
     * Cek apakah member aktif melalui service
     */
    public function isActive(): bool
    {
        return app(MemberService::class)->isActive($this);
    }

    /**
     * Dapatkan nilai poin dalam rupiah melalui service
     */
    public function getPointsValue(): float
    {
        return app(PointService::class)->getPointsValue($this->total_points);
    }

    /**
     * Tukar poin untuk diskon melalui service
     */
    public function redeemPoints(int $points): float
    {
        return app(PointService::class)->redeemPoints($this, $points);
    }
}
