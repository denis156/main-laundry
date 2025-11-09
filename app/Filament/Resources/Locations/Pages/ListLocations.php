<?php

declare(strict_types=1);

namespace App\Filament\Resources\Locations\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Locations\LocationResource;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            // StatsOverviewLocation::class, // Bisa ditambahkan nanti
        ];
    }
}
