<?php

declare(strict_types=1);

namespace App\Filament\Resources\Couriers\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Couriers\CourierResource;

class ListCouriers extends ListRecords
{
    protected static string $resource = CourierResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            // TODO: StatsOverviewCourier::class,
        ];
    }
}
