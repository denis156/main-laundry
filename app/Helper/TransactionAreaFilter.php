<?php

declare(strict_types=1);

namespace App\Helper;

use App\Models\Pos;
use Illuminate\Database\Eloquent\Builder;

/**
 * Transaction Area Filter Helper
 *
 * Helper untuk memfilter transaksi berdasarkan area layanan POS.
 * POS memiliki area layanan yang berisi daftar kelurahan (villages).
 * Transaksi akan difilter berdasarkan village_name dari customer.
 *
 * @package App\Helper
 */
class TransactionAreaFilter
{
    /**
     * Apply filter berdasarkan area POS ke query transaksi
     *
     * Method ini akan memfilter transaksi sehingga hanya menampilkan transaksi
     * yang customer-nya berada di kelurahan yang ter-cover oleh POS.
     *
     * Logika filter:
     * - Jika POS tidak punya area atau area kosong: tidak ada filter (tampilkan semua)
     * - Jika POS punya area: hanya tampilkan transaksi dengan customer di kelurahan tersebut
     * - Include juga customer yang village_name-nya NULL (backward compatibility)
     *
     * @param Builder $query Query builder untuk Transaction
     * @param Pos|null $pos POS yang akan digunakan untuk filter area
     * @return Builder Query yang sudah difilter
     *
     * @example
     * ```php
     * $query = Transaction::query();
     * TransactionAreaFilter::applyFilter($query, $assignedPos);
     * $transactions = $query->get();
     * ```
     */
    public static function applyFilter(Builder $query, ?Pos $pos): Builder
    {
        // Jika tidak ada POS atau POS tidak punya area, return query tanpa filter
        if (!$pos || empty($pos->area)) {
            return $query;
        }

        // Filter transaksi berdasarkan area layanan POS
        $query->whereHas('customer', function ($customerQuery) use ($pos) {
            $customerQuery->where(function ($subQuery) use ($pos) {
                // Loop setiap kelurahan di area POS
                foreach ($pos->area as $kelurahan) {
                    // Customer village_name harus match dengan salah satu kelurahan
                    $subQuery->orWhere('village_name', $kelurahan);
                }

                // ATAU customer belum punya village_name (backward compatibility)
                // Untuk customer lama yang belum ada data kelurahan
                $subQuery->orWhereNull('village_name');
            });
        });

        return $query;
    }

    /**
     * Apply filter berdasarkan area POS ke query transaksi (strict mode)
     *
     * Sama seperti applyFilter(), tapi TIDAK include customer dengan village_name NULL.
     * Mode ini lebih strict dan hanya menampilkan transaksi dengan village_name yang jelas.
     *
     * @param Builder $query Query builder untuk Transaction
     * @param Pos|null $pos POS yang akan digunakan untuk filter area
     * @return Builder Query yang sudah difilter
     *
     * @example
     * ```php
     * $query = Transaction::query();
     * TransactionAreaFilter::applyFilterStrict($query, $assignedPos);
     * $transactions = $query->get();
     * ```
     */
    public static function applyFilterStrict(Builder $query, ?Pos $pos): Builder
    {
        // Jika tidak ada POS atau POS tidak punya area, return query tanpa filter
        if (!$pos || empty($pos->area)) {
            return $query;
        }

        // Filter transaksi berdasarkan area layanan POS (strict mode)
        $query->whereHas('customer', function ($customerQuery) use ($pos) {
            $customerQuery->where(function ($subQuery) use ($pos) {
                // Loop setiap kelurahan di area POS
                foreach ($pos->area as $kelurahan) {
                    // Customer village_name harus match dengan salah satu kelurahan
                    $subQuery->orWhere('village_name', $kelurahan);
                }
                // Strict mode: TIDAK include customer dengan village_name NULL
            });
        });

        return $query;
    }

    /**
     * Check apakah customer berada di area layanan POS
     *
     * Helper method untuk mengecek apakah seorang customer masuk dalam
     * area layanan POS tertentu berdasarkan village_name.
     *
     * @param string|null $villageNameCustomer Nama kelurahan customer
     * @param Pos|null $pos POS yang akan dicek
     * @param bool $includeNull Apakah include customer dengan village_name NULL? (default: true)
     * @return bool True jika customer di area POS, false jika tidak
     *
     * @example
     * ```php
     * $customer = Customer::find(1);
     * $inArea = TransactionAreaFilter::isCustomerInPosArea(
     *     $customer->village_name,
     *     $assignedPos
     * );
     * ```
     */
    public static function isCustomerInPosArea(?string $villageNameCustomer, ?Pos $pos, bool $includeNull = true): bool
    {
        // Jika tidak ada POS atau POS tidak punya area, return true (tidak ada filter)
        if (!$pos || empty($pos->area)) {
            return true;
        }

        // Jika customer tidak punya village_name
        if (is_null($villageNameCustomer)) {
            return $includeNull; // Return sesuai parameter includeNull
        }

        // Check apakah village_name customer ada di area POS
        return in_array($villageNameCustomer, $pos->area, true);
    }

    /**
     * Get daftar kelurahan yang ter-cover oleh POS
     *
     * Helper method untuk mendapatkan daftar kelurahan yang dilayani POS.
     * Berguna untuk display atau validation.
     *
     * @param Pos|null $pos POS yang akan dicek
     * @return array Array berisi nama-nama kelurahan
     *
     * @example
     * ```php
     * $villages = TransactionAreaFilter::getPosVillages($assignedPos);
     * // Output: ['Kelurahan A', 'Kelurahan B']
     * ```
     */
    public static function getPosVillages(?Pos $pos): array
    {
        if (!$pos || empty($pos->area)) {
            return [];
        }

        return $pos->area;
    }

    /**
     * Get count transaksi per kelurahan untuk POS tertentu
     *
     * Helper method untuk mendapatkan statistik jumlah transaksi
     * per kelurahan di area layanan POS.
     *
     * @param Pos|null $pos POS yang akan dicek
     * @param string|null $workflowStatus Filter berdasarkan workflow_status (opsional)
     * @return array Array dengan key = village_name, value = count
     *
     * @example
     * ```php
     * $stats = TransactionAreaFilter::getTransactionCountByVillage($assignedPos);
     * // Output: ['Kelurahan A' => 5, 'Kelurahan B' => 3, 'null' => 2]
     * ```
     */
    public static function getTransactionCountByVillage(?Pos $pos, ?string $workflowStatus = null): array
    {
        if (!$pos || empty($pos->area)) {
            return [];
        }

        $query = \App\Models\Transaction::query()
            ->whereHas('customer', function ($customerQuery) use ($pos) {
                $customerQuery->where(function ($subQuery) use ($pos) {
                    foreach ($pos->area as $kelurahan) {
                        $subQuery->orWhere('village_name', $kelurahan);
                    }
                    $subQuery->orWhereNull('village_name');
                });
            })
            ->with('customer:id,village_name');

        // Filter berdasarkan workflow_status jika ada
        if ($workflowStatus) {
            $query->where('workflow_status', $workflowStatus);
        }

        $transactions = $query->get();

        // Group by village_name dan count
        $stats = [];
        foreach ($transactions as $transaction) {
            $villageName = $transaction->customer?->village_name ?? 'null';
            $stats[$villageName] = ($stats[$villageName] ?? 0) + 1;
        }

        return $stats;
    }
}
