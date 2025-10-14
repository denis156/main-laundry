<?php

declare(strict_types=1);

namespace App\Filament\Resources\Materials\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Materials\MaterialResource;
use App\Filament\Resources\Materials\Widgets\StatsOverviewMaterials;

class ListMaterials extends ListRecords
{
    protected static string $resource = MaterialResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewMaterials::class,
        ];
    }
}
