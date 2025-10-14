<?php

declare(strict_types=1);

namespace App\Filament\Resources\Resorts\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Resorts\ResortResource;
use App\Filament\Resources\Resorts\Widgets\StatsOverviewResorts;

class ListResorts extends ListRecords
{
    protected static string $resource = ResortResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewResorts::class,
        ];
    }
}
