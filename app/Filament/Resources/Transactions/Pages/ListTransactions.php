<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Filament\Resources\Transactions\Widgets\StatsOverviewTransactions;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewTransactions::class,
        ];
    }
}
