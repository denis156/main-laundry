<?php

declare(strict_types=1);

namespace App\Filament\Resources\Equipment\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Equipment\EquipmentResource;
use App\Filament\Resources\Equipment\Widgets\StatsOverviewEquipments;

class ListEquipment extends ListRecords
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewEquipments::class,
        ];
    }
}
