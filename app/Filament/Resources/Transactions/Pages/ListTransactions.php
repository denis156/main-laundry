<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Transactions\TransactionResource;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            // TODO: StatsOverviewTransaction::class,
        ];
    }
}
