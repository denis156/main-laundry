<?php

namespace App\Filament\Resources\MaterialStockHistories\Pages;

use App\Filament\Resources\MaterialStockHistories\MaterialStockHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaterialStockHistories extends ListRecords
{
    protected static string $resource = MaterialStockHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
