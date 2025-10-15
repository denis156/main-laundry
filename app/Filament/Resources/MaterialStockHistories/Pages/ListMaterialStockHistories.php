<?php

declare(strict_types=1);

namespace App\Filament\Resources\MaterialStockHistories\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\MaterialStockHistories\MaterialStockHistoryResource;
use App\Filament\Resources\MaterialStockHistories\Widgets\StatsOverviewMaterialStockHistories;

class ListMaterialStockHistories extends ListRecords
{
    protected static string $resource = MaterialStockHistoryResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewMaterialStockHistories::class,
        ];
    }
}
