<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClothingTypes\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ClothingTypes\ClothingTypeResource;

class ListClothingTypes extends ListRecords
{
    protected static string $resource = ClothingTypeResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            // TODO: StatsOverviewClothingType::class,
        ];
    }
}
