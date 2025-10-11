<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Customers\Widgets\StatsOverviewCustomer;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewCustomer::class,
        ];
    }
}
