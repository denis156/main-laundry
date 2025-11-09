<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Customers\CustomerResource;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            // TODO: StatsOverviewCustomer::class,
        ];
    }
}
