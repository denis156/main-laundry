<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Helper\Database\CustomerHelper;
use App\Helper\Database\CourierHelper;
use App\Helper\Database\TransactionHelper;
use App\Helper\StatusTransactionHelper;
use App\Models\Transaction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactionsTable extends TableWidget
{
    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Transaction::query()
                ->with(['customer', 'courier', 'location'])
                ->latest()
                ->limit(10)
            )
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->getStateUsing(fn (Transaction $record): string =>
                        $record->customer ? CustomerHelper::getName($record->customer) : '-'
                    )
                    ->searchable(['customers.data->name'])
                    ->sortable(),

                TextColumn::make('courier_name')
                    ->label('Kurir')
                    ->getStateUsing(fn (Transaction $record): string =>
                        $record->courier ? CourierHelper::getName($record->courier) : '-'
                    )
                    ->searchable(['couriers.data->name']),

                TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->badge()
                    ->color('info'),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->getStateUsing(fn (Transaction $record): string =>
                        TransactionHelper::getFormattedTotalPrice($record)
                    )
                    ->sortable(),

                TextColumn::make('workflow_status')
                    ->label('Status Workflow')
                    ->formatStateUsing(fn (string $state): string =>
                        StatusTransactionHelper::getStatusText($state)
                    )
                    ->badge()
                    ->color(fn (string $state): string =>
                        StatusTransactionHelper::getStatusBadgeColor($state)
                    ),

                TextColumn::make('payment_timing')
                    ->label('Waktu Bayar')
                    ->getStateUsing(fn (Transaction $record): string =>
                        TransactionHelper::getPaymentTimingText($record)
                    )
                    ->badge()
                    ->color('gray'),

                TextColumn::make('payment_status')
                    ->label('Status Bayar')
                    ->formatStateUsing(fn (string $state): string =>
                        $state === 'paid' ? 'Lunas' : 'Belum Bayar'
                    )
                    ->badge()
                    ->color(fn (string $state): string =>
                        $state === 'paid' ? 'success' : 'danger'
                    ),

                TextColumn::make('order_date')
                    ->label('Tanggal Order')
                    ->getStateUsing(fn (Transaction $record): string =>
                        TransactionHelper::getFormattedOrderDate($record)
                    )
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
