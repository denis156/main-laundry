<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Equipment;
use App\Models\EquipmentMaintenance;

class EquipmentMaintenanceObserver
{
    /**
     * Handle the EquipmentMaintenance "created" event.
     * Update equipment status to maintenance and update last maintenance info
     */
    public function created(EquipmentMaintenance $equipmentMaintenance): void
    {
        $equipment = Equipment::find($equipmentMaintenance->equipment_id);

        if ($equipment) {
            $equipment->update([
                'status' => 'maintenance',
                'last_maintenance_date' => $equipmentMaintenance->maintenance_date,
                'last_maintenance_cost' => $equipmentMaintenance->cost,
            ]);
        }
    }

    /**
     * Handle the EquipmentMaintenance "updated" event.
     * Update equipment last maintenance info if this is the latest maintenance
     */
    public function updated(EquipmentMaintenance $equipmentMaintenance): void
    {
        $equipment = Equipment::find($equipmentMaintenance->equipment_id);

        if ($equipment) {
            // Cek apakah ini maintenance terakhir untuk equipment ini
            $latestMaintenance = EquipmentMaintenance::where('equipment_id', $equipmentMaintenance->equipment_id)
                ->orderBy('maintenance_date', 'desc')
                ->first();

            // Jika maintenance ini adalah yang terakhir, update equipment
            if ($latestMaintenance && $latestMaintenance->id === $equipmentMaintenance->id) {
                $equipment->update([
                    'last_maintenance_date' => $equipmentMaintenance->maintenance_date,
                    'last_maintenance_cost' => $equipmentMaintenance->cost,
                ]);
            }
        }
    }

    /**
     * Handle the EquipmentMaintenance "deleted" event.
     * Update equipment maintenance info based on remaining maintenances
     */
    public function deleted(EquipmentMaintenance $equipmentMaintenance): void
    {
        $equipment = Equipment::find($equipmentMaintenance->equipment_id);

        if ($equipment) {
            // Cari maintenance terakhir yang tersisa
            $latestMaintenance = EquipmentMaintenance::where('equipment_id', $equipmentMaintenance->equipment_id)
                ->orderBy('maintenance_date', 'desc')
                ->first();

            if ($latestMaintenance) {
                // Jika masih ada maintenance lain, update ke maintenance terakhir
                $equipment->update([
                    'last_maintenance_date' => $latestMaintenance->maintenance_date,
                    'last_maintenance_cost' => $latestMaintenance->cost,
                ]);
            } else {
                // Jika tidak ada maintenance lagi, reset data maintenance
                $equipment->update([
                    'status' => 'baik',
                    'last_maintenance_date' => null,
                    'last_maintenance_cost' => null,
                ]);
            }
        }
    }
}
