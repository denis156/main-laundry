<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Member;

class PointService
{
    /**
     * Rate perhitungan poin
     * Contoh: Setiap Rp 10.000 yang dibelanjakan = 1 poin
     */
    private const POINTS_PER_AMOUNT = 10000;

    /**
     * Hitung poin yang didapat dari nominal transaksi
     */
    public function calculatePointsFromAmount(float $amount): int
    {
        if ($amount <= 0) {
            return 0;
        }

        return (int) floor($amount / self::POINTS_PER_AMOUNT);
    }

    /**
     * Berikan poin ke member dari transaksi
     */
    public function awardPoints(Member $member, float $transactionAmount): int
    {
        $points = $this->calculatePointsFromAmount($transactionAmount);

        if ($points > 0) {
            $member->increment('total_points', $points);
            $member->refresh();
        }

        return $points;
    }

    /**
     * Tukar poin untuk diskon
     * Contoh: 100 poin = Rp 10.000 diskon
     */
    public function redeemPoints(Member $member, int $points): float
    {
        if ($points <= 0 || $points > $member->total_points) {
            return 0.0;
        }

        // Rate konversi: 1 poin = Rp 100
        $discountAmount = $points * 100;

        $member->decrement('total_points', $points);
        $member->refresh();

        return (float) $discountAmount;
    }

    /**
     * Dapatkan nilai poin dalam rupiah
     */
    public function getPointsValue(int $points): float
    {
        return (float) ($points * 100);
    }

    /**
     * Dapatkan poin yang dibutuhkan untuk nominal diskon tertentu
     */
    public function getRequiredPoints(float $discountAmount): int
    {
        return (int) ceil($discountAmount / 100);
    }

    /**
     * Cek apakah member memiliki poin yang cukup
     */
    public function hasEnoughPoints(Member $member, int $requiredPoints): bool
    {
        return $member->total_points >= $requiredPoints;
    }
}
