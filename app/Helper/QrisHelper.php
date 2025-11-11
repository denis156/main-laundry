<?php

declare(strict_types=1);

namespace App\Helper;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class QrisHelper
{
    /**
     * Convert QRIS Static to Dynamic dengan nominal
     */
    public static function generateDynamicQris(float $amount, ?string $feeType = null, ?float $feeAmount = null): string
    {
        try {
            // Ambil QRIS static dari config
            $qrisStatic = config('qrisconvert.static_code');

            if (empty($qrisStatic)) {
                throw new Exception('QRIS_STATIC not configured in environment');
            }

            // Convert ke format yang tepat (tanpa newline)
            $qrisStatic = trim(str_replace(["\n", "\r"], '', $qrisStatic));

            // Convert ke dinamis
            $dynamicQris = self::convertStaticToDynamic($qrisStatic, (string) $amount, $feeType, $feeAmount);

            return $dynamicQris;
        } catch (Exception $e) {
            Log::error('QRIS Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate QR Code image dan simpan ke storage
     */
    public static function generateQrCodeImage(string $qrisData, string $filename): string
    {
        try {
            // Get QR code config
            $qrConfig = config('qrisconvert.qr_code');
            $storageConfig = config('qrisconvert.storage');

            // Generate QR Code menggunakan Simple QrCode
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                ->size($qrConfig['size'])
                ->errorCorrection($qrConfig['error_correction'])
                ->margin($qrConfig['margin'])
                ->generate($qrisData);

            // Save ke storage
            $path = $storageConfig['path'] . "/{$filename}";
            Storage::disk('public')->put($path, $qrCode);

            return $path;
        } catch (Exception $e) {
            Log::error('QR Code Image Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate QR Code untuk payment dengan nominal dari transaction
     */
    public static function generatePaymentQrCode(float $amount, int $transactionId): array
    {
        try {
            // Generate dynamic QRIS
            $dynamicQris = self::generateDynamicQris($amount);

            // Generate filename unik
            $filename = 'payment-' . $transactionId . '-' . time() . '.png';

            // Generate dan save QR Code image
            $imagePath = self::generateQrCodeImage($dynamicQris, $filename);

            return [
                'qris_data' => $dynamicQris,
                'image_path' => $imagePath,
                'image_url' => asset('storage/' . $imagePath),
                'amount' => $amount,
            ];
        } catch (Exception $e) {
            Log::error('Payment QR Code Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Convert static QRIS to dynamic dengan nominal
     * Logic dari config/qrisconvert.php
     */
    private static function convertStaticToDynamic(string $qris, string $amount, ?string $feeType = null, ?float $feeAmount = null): string
    {
        // Remove last 4 characters (CRC)
        $qris = substr($qris, 0, -4);

        // Change from static to dynamic
        $step1 = str_replace("010211", "010212", $qris);

        // Split at merchant info
        $step2 = explode("5802ID", $step1);

        // Build amount string
        $amountStr = "54" . sprintf("%02d", strlen($amount)) . $amount;

        // Handle fee jika ada
        $feeStr = '';
        if ($feeType && $feeAmount) {
            if ($feeType === 'rupiah') {
                $feeStr = "55020256" . sprintf("%02d", strlen((string)$feeAmount)) . $feeAmount;
            } elseif ($feeType === 'persen') {
                $feeStr = "55020357" . sprintf("%02d", strlen((string)$feeAmount)) . $feeAmount;
            }
        }

        // Combine all parts
        $uang = $amountStr . $feeStr . "5802ID";

        // Final QRIS string
        $fix = trim($step2[0]) . $uang . trim($step2[1]);

        // Add CRC16
        $fix .= self::calculateCRC16($fix);

        return $fix;
    }

    /**
     * Calculate CRC16 checksum
     */
    private static function calculateCRC16(string $str): string
    {
        $crc = 0xFFFF;
        $strlen = strlen($str);

        for ($c = 0; $c < $strlen; $c++) {
            $crc ^= ord(substr($str, $c, 1)) << 8;

            for ($i = 0; $i < 8; $i++) {
                if ($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc = $crc << 1;
                }
            }
        }

        $hex = $crc & 0xFFFF;
        $hex = strtoupper(dechex($hex));

        return str_pad($hex, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Validate QRIS static format
     */
    public static function validateQrisStatic(string $qris): bool
    {
        // Basic validation - check if it starts with correct prefix
        return str_starts_with($qris, '000201010211');
    }

    /**
     * Format amount untuk display
     */
    public static function formatAmount(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}