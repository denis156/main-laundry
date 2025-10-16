<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pos\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Pos\PosResource;
use App\Filament\Resources\Pos\Widgets\StatsOverviewPos;

class ListPos extends ListRecords
{
    protected static string $resource = PosResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewPos::class,
        ];
    }
}
