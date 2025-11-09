<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Payments\PaymentResource;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            // TODO: StatsOverviewPayment::class,
        ];
    }
}
