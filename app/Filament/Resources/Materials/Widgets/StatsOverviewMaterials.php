<?php

declare(strict_types=1);

namespace App\Filament\Resources\Materials\Widgets;

use App\Models\Material;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewMaterials extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Bahan';

    protected ?string $description = 'Ringkasan status bahan dan inventori';

    protected function getStats(): array
    {
        // Total bahan
        $totalMaterials = Material::count();

        // Total nilai inventori (current_stock * price_per_unit)
        $totalInventoryValue = Material::whereNotNull('price_per_unit')
            ->get()
            ->sum(function ($material) {
                return $material->current_stock * $material->price_per_unit;
            });

        // Bahan yang stok nya menipis (current_stock <= minimum_stock)
        $lowStockCount = Material::whereNotNull('minimum_stock')
            ->whereRaw('current_stock <= minimum_stock')
            ->count();

        // Bahan yang expired atau akan expired dalam 30 hari
        $expiringCount = Material::whereNotNull('expired_date')
            ->where('expired_date', '<=', now()->addDays(30))
            ->count();

        return [
            Stat::make('Total Bahan', number_format($totalMaterials, 0, ',', '.') . ' Item')
                ->description('Total jenis bahan tersedia')
                ->descriptionIcon('solar-box-bold-duotone')
                ->color('primary'),

            Stat::make('Nilai Inventori', 'Rp ' . number_format($totalInventoryValue, 0, ',', '.'))
                ->description('Total nilai bahan saat ini')
                ->descriptionIcon('solar-wallet-money-bold-duotone')
                ->color('success'),

            Stat::make('Stok Menipis', number_format($lowStockCount, 0, ',', '.') . ' Item')
                ->description($lowStockCount > 0 ? 'Perlu segera restok!' : 'Stok aman')
                ->descriptionIcon($lowStockCount > 0 ? 'solar-danger-bold-duotone' : 'solar-shield-check-bold-duotone')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),

            Stat::make('Hampir/Sudah Kadaluarsa', number_format($expiringCount, 0, ',', '.') . ' Item')
                ->description($expiringCount > 0 ? 'Dalam 30 hari ke depan' : 'Tidak ada yang kadaluarsa')
                ->descriptionIcon($expiringCount > 0 ? 'solar-clock-circle-bold-duotone' : 'solar-check-circle-bold-duotone')
                ->color($expiringCount > 0 ? 'warning' : 'success'),
        ];
    }
}
