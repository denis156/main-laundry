<?php

declare(strict_types=1);

namespace App\Helper;

use App\Models\Transaction;
use Carbon\Carbon;

/**
 * Order Rate Limiter Helper
 *
 * Helper untuk membatasi jumlah order (rate limiting) dan deteksi bot.
 * Melindungi sistem dari spam order dan abuse.
 *
 * Limit yang diterapkan:
 * - Maksimal 6 order per IP per jam
 * - Maksimal 5 order per nomor telepon per hari
 * - Minimum 3 detik antara load form dan submit (bot detection)
 *
 * @package App\Helper
 */
class OrderRateLimiterHelper
{
    /**
     * Maximum orders per IP per hour
     */
    private const MAX_ORDERS_PER_IP_PER_HOUR = 6;

    /**
     * Maximum orders per phone per day
     */
    private const MAX_ORDERS_PER_PHONE_PER_DAY = 5;

    /**
     * Minimum seconds between form load and submission (untuk detect bot)
     */
    private const MIN_SUBMISSION_SECONDS = 3;

    /**
     * Check if IP address has exceeded rate limit
     *
     * @param string $ip Alamat IP yang akan dicek
     * @return bool True jika masih dalam batas, false jika exceed
     */
    public static function checkIpRateLimit(string $ip): bool
    {
        $hourAgo = Carbon::now()->subHour();

        $orderCount = Transaction::where('customer_ip', $ip)
            ->where('created_at', '>=', $hourAgo)
            ->count();

        return $orderCount < self::MAX_ORDERS_PER_IP_PER_HOUR;
    }

    /**
     * Check if phone number has exceeded rate limit
     *
     * @param string $phone Nomor telepon yang akan dicek
     * @return bool True jika masih dalam batas, false jika exceed
     */
    public static function checkPhoneRateLimit(string $phone): bool
    {
        $dayAgo = Carbon::now()->subDay();

        $orderCount = Transaction::whereHas('customer', function ($query) use ($phone) {
            $query->where('phone', $phone);
        })
            ->where('created_at', '>=', $dayAgo)
            ->count();

        return $orderCount < self::MAX_ORDERS_PER_PHONE_PER_DAY;
    }

    /**
     * Check if submission was too fast (possible bot)
     *
     * @param int $formLoadedTimestamp Unix timestamp when form was loaded
     * @return bool True jika suspicious (terlalu cepat), false jika valid
     */
    public static function isSubmissionTooFast(int $formLoadedTimestamp): bool
    {
        if ($formLoadedTimestamp <= 0) {
            // Jika form_loaded_at tidak valid, anggap suspicious
            return true;
        }

        $secondsElapsed = now()->timestamp - $formLoadedTimestamp;

        // Jika elapsed time negatif (form loaded in future), anggap suspicious
        if ($secondsElapsed < 0) {
            return true;
        }

        return $secondsElapsed < self::MIN_SUBMISSION_SECONDS;
    }

    /**
     * Get remaining orders for IP address
     *
     * @param string $ip Alamat IP
     * @return int Jumlah order yang masih bisa dilakukan
     */
    public static function getRemainingOrdersForIp(string $ip): int
    {
        $hourAgo = Carbon::now()->subHour();

        $orderCount = Transaction::where('customer_ip', $ip)
            ->where('created_at', '>=', $hourAgo)
            ->count();

        return max(0, self::MAX_ORDERS_PER_IP_PER_HOUR - $orderCount);
    }

    /**
     * Get remaining orders for phone number
     *
     * @param string $phone Nomor telepon
     * @return int Jumlah order yang masih bisa dilakukan
     */
    public static function getRemainingOrdersForPhone(string $phone): int
    {
        $dayAgo = Carbon::now()->subDay();

        $orderCount = Transaction::whereHas('customer', function ($query) use ($phone) {
            $query->where('phone', $phone);
        })
            ->where('created_at', '>=', $dayAgo)
            ->count();

        return max(0, self::MAX_ORDERS_PER_PHONE_PER_DAY - $orderCount);
    }

    /**
     * Check all rate limits at once
     *
     * @param string $ip Alamat IP
     * @param string $phone Nomor telepon
     * @param int $formLoadedTimestamp Unix timestamp when form was loaded
     * @return array ['passed' => bool, 'errors' => array]
     */
    public static function checkAllLimits(string $ip, string $phone, int $formLoadedTimestamp): array
    {
        $errors = [];

        // Check IP rate limit
        if (!self::checkIpRateLimit($ip)) {
            $errors[] = 'Terlalu banyak pesanan dari alamat IP Anda. Silakan coba lagi dalam 1 jam.';
        }

        // Check phone rate limit
        if (!self::checkPhoneRateLimit($phone)) {
            $errors[] = 'Nomor telepon Anda telah mencapai batas maksimal pesanan hari ini. Silakan coba lagi besok.';
        }

        // Check submission speed (bot detection)
        if (self::isSubmissionTooFast($formLoadedTimestamp)) {
            $errors[] = 'Pengiriman form terlalu cepat. Silakan isi form dengan lebih teliti.';
        }

        return [
            'passed' => empty($errors),
            'errors' => $errors,
        ];
    }
}
