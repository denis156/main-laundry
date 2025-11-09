<?php

declare(strict_types=1);

namespace App\Filament\Resources\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Resources\ResourceResource;

class ListResources extends ListRecords
{
    protected static string $resource = ResourceResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            // TODO: StatsOverviewResource::class,
        ];
    }
}
