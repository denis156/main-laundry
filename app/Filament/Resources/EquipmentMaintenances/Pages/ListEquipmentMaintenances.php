<?php

declare(strict_types=1);

namespace App\Filament\Resources\EquipmentMaintenances\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\EquipmentMaintenances\EquipmentMaintenanceResource;
use App\Filament\Resources\EquipmentMaintenances\Widgets\StatsOverviewEquipmentMaintenance;

class ListEquipmentMaintenances extends ListRecords
{
    protected static string $resource = EquipmentMaintenanceResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewEquipmentMaintenance::class,
        ];
    }
}
