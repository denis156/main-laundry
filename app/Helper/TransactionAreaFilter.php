<?php

declare(strict_types=1);

namespace App\Helper;

use App\Helper\Database\CustomerHelper;
use App\Helper\Database\LocationHelper;
use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;

/**
 * Transaction Area Filter Helper
 *
 * Helper untuk memfilter transaksi berdasarkan area layanan Location (POS/Resort).
 * Location memiliki coverage_area yang berisi daftar kecamatan & kelurahan.
 * Transaksi akan difilter berdasarkan address data dari customer (di JSONB).
 *
 * @package App\Helper
 */
class TransactionAreaFilter
{
    /**
     * Apply filter berdasarkan area Location ke query transaksi
     *
     * Method ini akan memfilter transaksi sehingga hanya menampilkan transaksi
     * yang customer-nya berada di area yang ter-cover oleh Location.
     *
     * Logika filter:
     * - Jika Location tidak punya coverage_area atau kosong: tidak ada filter (tampilkan semua)
     * - Jika Location punya coverage_area: hanya tampilkan transaksi dengan customer di area tersebut
     * - Include juga customer yang belum ada address data (backward compatibility)
     *
     * @param Builder $query Query builder untuk Transaction
     * @param Location|null $location Location (POS/Resort) yang akan digunakan untuk filter area
     * @return Builder Query yang sudah difilter
     *
     * @example
     * ```php
     * $query = Transaction::query();
     * TransactionAreaFilter::applyFilter($query, $assignedLocation);
     * $transactions = $query->get();
     * ```
     */
    public static function applyFilter(Builder $query, ?Location $location): Builder
    {
        // Jika tidak ada Location, return query tanpa filter
        if (!$location) {
            return $query;
        }

        $coverageArea = LocationHelper::getCoverageArea($location);

        // Jika tidak punya coverage area, return query tanpa filter
        if (empty($coverageArea)) {
            return $query;
        }

        // Filter transaksi berdasarkan area layanan Location
        $query->whereHas('customer', function ($customerQuery) use ($coverageArea) {
            $customerQuery->where(function ($subQuery) use ($coverageArea) {
                // Loop setiap district di coverage area
                foreach ($coverageArea as $district) {
                    $districtCode = $district['district_code'] ?? null;
                    $villages = $district['villages'] ?? [];

                    if ($districtCode && !empty($villages)) {
                        // Filter by district & villages in JSONB data
                        $subQuery->orWhere(function ($addressQuery) use ($districtCode, $villages) {
                            $addressQuery->whereJsonContains('data->addresses', [
                                ['district_code' => $districtCode]
                            ]);
                            // TODO: Add village filtering when needed
                        });
                    }
                }

                // ATAU customer belum punya address data (backward compatibility)
                $subQuery->orWhereNull('data->addresses');
            });
        });

        return $query;
    }

    /**
     * Apply filter berdasarkan area Location ke query transaksi (strict mode)
     *
     * Sama seperti applyFilter(), tapi TIDAK include customer tanpa address data.
     * Mode ini lebih strict dan hanya menampilkan transaksi dengan address yang jelas.
     *
     * @param Builder $query Query builder untuk Transaction
     * @param Location|null $location Location yang akan digunakan untuk filter area
     * @return Builder Query yang sudah difilter
     *
     * @example
     * ```php
     * $query = Transaction::query();
     * TransactionAreaFilter::applyFilterStrict($query, $assignedLocation);
     * $transactions = $query->get();
     * ```
     */
    public static function applyFilterStrict(Builder $query, ?Location $location): Builder
    {
        // Jika tidak ada Location, return query tanpa filter
        if (!$location) {
            return $query;
        }

        $coverageArea = LocationHelper::getCoverageArea($location);

        // Jika tidak punya coverage area, return query tanpa filter
        if (empty($coverageArea)) {
            return $query;
        }

        // Filter transaksi berdasarkan area layanan Location (strict mode)
        $query->whereHas('customer', function ($customerQuery) use ($coverageArea) {
            $customerQuery->where(function ($subQuery) use ($coverageArea) {
                // Loop setiap district di coverage area
                foreach ($coverageArea as $district) {
                    $districtCode = $district['district_code'] ?? null;
                    $villages = $district['villages'] ?? [];

                    if ($districtCode && !empty($villages)) {
                        $subQuery->orWhere(function ($addressQuery) use ($districtCode, $villages) {
                            $addressQuery->whereJsonContains('data->addresses', [
                                ['district_code' => $districtCode]
                            ]);
                        });
                    }
                }
                // Strict mode: TIDAK include customer tanpa address data
            });
        });

        return $query;
    }

    /**
     * Check apakah customer berada di area layanan Location
     *
     * Helper method untuk mengecek apakah seorang customer masuk dalam
     * area layanan Location tertentu berdasarkan address data di JSONB.
     *
     * @param \App\Models\Customer $customer Customer yang akan dicek
     * @param Location|null $location Location yang akan dicek
     * @param bool $includeNull Apakah include customer tanpa address data? (default: true)
     * @return bool True jika customer di area Location, false jika tidak
     *
     * @example
     * ```php
     * $customer = Customer::find(1);
     * $inArea = TransactionAreaFilter::isCustomerInLocationArea($customer, $assignedLocation);
     * ```
     */
    public static function isCustomerInLocationArea(\App\Models\Customer $customer, ?Location $location, bool $includeNull = true): bool
    {
        // Jika tidak ada Location, return true (tidak ada filter)
        if (!$location) {
            return true;
        }

        $coverageArea = LocationHelper::getCoverageArea($location);

        // Jika tidak punya coverage area, return true (tidak ada filter)
        if (empty($coverageArea)) {
            return true;
        }

        $customerAddresses = CustomerHelper::getAddresses($customer);

        // Jika customer tidak punya address data
        if (empty($customerAddresses)) {
            return $includeNull;
        }

        // Check apakah ada address customer yang match dengan coverage area
        foreach ($customerAddresses as $address) {
            $districtCode = $address['district_code'] ?? null;

            foreach ($coverageArea as $district) {
                if ($district['district_code'] === $districtCode) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get daftar coverage area dari Location
     *
     * Helper method untuk mendapatkan coverage area yang dilayani Location.
     * Berguna untuk display atau validation.
     *
     * @param Location|null $location Location yang akan dicek
     * @return array Array berisi coverage area
     *
     * @example
     * ```php
     * $areas = TransactionAreaFilter::getLocationCoverageArea($assignedLocation);
     * // Output: [['district_code' => '74.71.01', 'district_name' => 'Kec A', 'villages' => [...]]]
     * ```
     */
    public static function getLocationCoverageArea(?Location $location): array
    {
        if (!$location) {
            return [];
        }

        return LocationHelper::getCoverageArea($location);
    }

    /**
     * Get count transaksi per district untuk Location tertentu
     *
     * Helper method untuk mendapatkan statistik jumlah transaksi
     * per kecamatan di area layanan Location.
     *
     * @param Location|null $location Location yang akan dicek
     * @param string|null $workflowStatus Filter berdasarkan workflow_status (opsional)
     * @return array Array dengan key = district_name, value = count
     *
     * @example
     * ```php
     * $stats = TransactionAreaFilter::getTransactionCountByDistrict($assignedLocation);
     * // Output: ['Kec A' => 5, 'Kec B' => 3, 'no_address' => 2]
     * ```
     */
    public static function getTransactionCountByDistrict(?Location $location, ?string $workflowStatus = null): array
    {
        if (!$location) {
            return [];
        }

        $coverageArea = LocationHelper::getCoverageArea($location);

        if (empty($coverageArea)) {
            return [];
        }

        $query = \App\Models\Transaction::query()->with('customer');

        // Filter berdasarkan workflow_status jika ada
        if ($workflowStatus) {
            $query->where('workflow_status', $workflowStatus);
        }

        $transactions = $query->get();

        // Group by district dan count
        $stats = [];
        foreach ($transactions as $transaction) {
            $customer = $transaction->customer;
            if (!$customer) {
                continue;
            }

            $addresses = CustomerHelper::getAddresses($customer);

            if (empty($addresses)) {
                $stats['no_address'] = ($stats['no_address'] ?? 0) + 1;
                continue;
            }

            $defaultAddress = CustomerHelper::getDefaultAddress($customer);
            $districtName = $defaultAddress['district_name'] ?? 'unknown';
            $stats[$districtName] = ($stats[$districtName] ?? 0) + 1;
        }

        return $stats;
    }
}
