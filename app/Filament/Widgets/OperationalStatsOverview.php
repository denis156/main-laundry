<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Equipment;
use App\Models\Material;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OperationalStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Statistik Operasional & Inventori';

    protected ?string $description = 'Monitoring pembayaran, peralatan, dan stok material.';

    protected function getStats(): array
    {
        return [
            // 4. Pembayaran Pending (Unpaid)
            Stat::make('Pembayaran Pending', $this->getUnpaidTransactionsCount() . ' Transaksi')
                ->description('Total: Rp ' . number_format($this->getUnpaidTransactionsAmount(), 0, ',', '.'))
                ->descriptionIcon('solar-card-bold-duotone')
                ->color('danger'),

            // 5. Equipment Butuh Maintenance
            Stat::make('Equipment Maintenance', $this->getEquipmentMaintenanceCount() . ' Unit')
                ->description($this->getEquipmentMaintenanceDescription())
                ->descriptionIcon($this->getEquipmentMaintenanceCount() > 0 ? 'solar-danger-bold-duotone' : 'solar-shield-check-bold-duotone')
                ->color($this->getEquipmentMaintenanceCount() > 0 ? 'warning' : 'success'),

            // 6. Material Stok Menipis
            Stat::make('Material Stok Menipis', $this->getLowStockMaterialsCount() . ' Item')
                ->description($this->getLowStockMaterialsDescription())
                ->descriptionIcon($this->getLowStockMaterialsCount() > 0 ? 'solar-box-minimalistic-bold-duotone' : 'solar-shield-check-bold-duotone')
                ->color($this->getLowStockMaterialsCount() > 0 ? 'danger' : 'success'),
        ];
    }

    /**
     * Hitung jumlah transaksi yang belum dibayar
     */
    private function getUnpaidTransactionsCount(): int
    {
        return Transaction::where('payment_status', 'unpaid')
            ->whereNotIn('workflow_status', ['cancelled'])
            ->count();
    }

    /**
     * Hitung total amount transaksi yang belum dibayar
     */
    private function getUnpaidTransactionsAmount(): float
    {
        return (float) Transaction::where('payment_status', 'unpaid')
            ->whereNotIn('workflow_status', ['cancelled'])
            ->sum('total_price');
    }

    /**
     * Hitung equipment yang butuh maintenance
     */
    private function getEquipmentMaintenanceCount(): int
    {
        return Equipment::whereIn('status', ['rusak', 'maintenance'])
            ->count();
    }

    /**
     * Deskripsi status equipment
     */
    private function getEquipmentMaintenanceDescription(): string
    {
        $rusak = Equipment::where('status', 'rusak')->count();
        $maintenance = Equipment::where('status', 'maintenance')->count();

        $parts = [];
        if ($rusak > 0) {
            $parts[] = $rusak . ' rusak';
        }
        if ($maintenance > 0) {
            $parts[] = $maintenance . ' maintenance';
        }

        return !empty($parts) ? implode(', ', $parts) : 'Semua equipment dalam kondisi baik';
    }

    /**
     * Hitung material yang stoknya menipis
     */
    private function getLowStockMaterialsCount(): int
    {
        return Material::whereColumn('current_stock', '<=', 'minimum_stock')
            ->orWhereNull('minimum_stock')
            ->where('current_stock', '<=', 0)
            ->count();
    }

    /**
     * Deskripsi material stok menipis
     */
    private function getLowStockMaterialsDescription(): string
    {
        $count = $this->getLowStockMaterialsCount();

        if ($count === 0) {
            return 'Semua stok aman';
        }

        return $count > 1 ? 'Perlu restock segera' : 'Perlu restock';
    }
}
