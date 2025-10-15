<?php

declare(strict_types=1);

namespace App\Filament\Resources\MaterialStockHistories\Widgets;

use App\Models\MaterialStockHistory;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewMaterialStockHistories extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Riwayat Stock Bahan';

    protected ?string $description = 'Ringkasan pergerakan stock bahan';

    protected function getStats(): array
    {
        // Total riwayat stock
        $totalHistories = MaterialStockHistory::count();

        // Total stock masuk
        $totalStockIn = (float) MaterialStockHistory::where('type', 'in')
            ->sum('quantity');

        // Total stock keluar
        $totalStockOut = (float) MaterialStockHistory::where('type', 'out')
            ->sum('quantity');

        // Jumlah transaksi bulan ini
        $thisMonthTransactions = MaterialStockHistory::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        return [
            Stat::make('Total Riwayat', number_format($totalHistories, 0, ',', '.') . ' Transaksi')
                ->description('Total semua pergerakan stock')
                ->descriptionIcon('solar-clipboard-list-bold-duotone')
                ->color('primary'),

            Stat::make('Stock Masuk', number_format($totalStockIn, 0, ',', '.') . ' Unit')
                ->description('Total bahan yang masuk')
                ->descriptionIcon('solar-import-bold-duotone')
                ->color('success'),

            Stat::make('Stock Keluar', number_format($totalStockOut, 0, ',', '.') . ' Unit')
                ->description('Total bahan yang keluar')
                ->descriptionIcon('solar-export-bold-duotone')
                ->color('danger'),

            Stat::make('Transaksi Bulan Ini', number_format($thisMonthTransactions, 0, ',', '.') . ' Transaksi')
                ->description('Pergerakan di ' . now()->format('F Y'))
                ->descriptionIcon('solar-calendar-bold-duotone')
                ->color('info'),
        ];
    }
}
