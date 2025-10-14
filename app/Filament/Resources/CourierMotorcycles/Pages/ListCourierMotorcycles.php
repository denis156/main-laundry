<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierMotorcycles\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CourierMotorcycles\CourierMotorcycleResource;
use App\Filament\Resources\CourierMotorcycles\Widgets\StatsOverviewCourierMotorcycle;

class ListCourierMotorcycles extends ListRecords
{
    protected static string $resource = CourierMotorcycleResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewCourierMotorcycle::class,
        ];
    }
}
