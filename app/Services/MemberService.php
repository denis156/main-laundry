<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use App\Models\Member;
use App\Models\MembershipTier;
use Carbon\Carbon;

class MemberService
{
    /**
     * Generate nomor member unik
     * Format: MBR-YYYYMMDD-XXXX
     * Contoh: MBR-20251010-0001
     */
    public function generateMemberNumber(): string
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = "MBR-{$date}-";

        // Ambil nomor member terakhir yang dibuat hari ini
        $lastMember = Member::where('member_number', 'like', $prefix . '%')
            ->orderBy('member_number', 'desc')
            ->first();

        if ($lastMember) {
            $lastNumber = (int) substr($lastMember->member_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Buat member baru dari customer
     */
    public function createMember(Customer $customer): Member
    {
        // Cek apakah customer sudah memiliki member
        if ($customer->member) {
            return $customer->member;
        }

        // Ambil tier awal (tier terendah)
        $initialTier = MembershipTier::where('is_active', true)
            ->orderBy('min_points', 'asc')
            ->first();

        return Member::create([
            'customer_id' => $customer->id,
            'membership_tier_id' => $initialTier?->id,
            'member_number' => $this->generateMemberNumber(),
            'member_since' => Carbon::now(),
            'total_points' => 0,
            'is_active' => true,
        ]);
    }

    /**
     * Update tier member berdasarkan total poin
     */
    public function updateMemberTier(Member $member): void
    {
        $appropriateTier = MembershipTier::where('is_active', true)
            ->where('min_points', '<=', $member->total_points)
            ->orderBy('min_points', 'desc')
            ->first();

        if ($appropriateTier && $member->membership_tier_id !== $appropriateTier->id) {
            $member->update([
                'membership_tier_id' => $appropriateTier->id,
            ]);
        }
    }

    /**
     * Tambah poin ke member
     */
    public function addPoints(Member $member, int $points): void
    {
        $member->increment('total_points', $points);
        $member->refresh();

        // Auto update tier setelah menambah poin
        $this->updateMemberTier($member);
    }

    /**
     * Kurangi poin dari member
     */
    public function deductPoints(Member $member, int $points): void
    {
        $member->decrement('total_points', $points);
        $member->refresh();

        // Auto update tier setelah mengurangi poin
        $this->updateMemberTier($member);
    }

    /**
     * Cek apakah member aktif
     */
    public function isActive(Member $member): bool
    {
        return $member->is_active;
    }

    /**
     * Aktifkan member
     */
    public function activate(Member $member): void
    {
        $member->update(['is_active' => true]);
    }

    /**
     * Nonaktifkan member
     */
    public function deactivate(Member $member): void
    {
        $member->update(['is_active' => false]);
    }

    /**
     * Dapatkan persentase diskon member
     */
    public function getDiscountPercentage(Member $member): float
    {
        if (!$this->isActive($member) || !$member->membershipTier) {
            return 0.0;
        }

        return (float) $member->membershipTier->discount_percentage;
    }
}
