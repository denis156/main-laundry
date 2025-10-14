<?php

namespace App\Filament\Resources\MaterialStockHistories\Pages;

use App\Filament\Resources\MaterialStockHistories\MaterialStockHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMaterialStockHistory extends EditRecord
{
    protected static string $resource = MaterialStockHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
