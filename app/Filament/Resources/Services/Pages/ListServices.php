<?php

declare(strict_types=1);

namespace App\Filament\Resources\Services\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Services\Widgets\StatsOverviewServices;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewServices::class,
        ];
    }
}
