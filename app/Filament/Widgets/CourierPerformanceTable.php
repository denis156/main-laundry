<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Helper\Database\CourierHelper;
use App\Models\Courier;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class CourierPerformanceTable extends TableWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Courier::query()->with('assignedLocation'))
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->getStateUsing(fn (Courier $record): string => CourierHelper::getFilamentAvatarUrl($record))
                    ->defaultImageUrl(url('/images/defaults-avatar.png')),

                TextColumn::make('name')
                    ->label('Nama Kurir')
                    ->getStateUsing(fn (Courier $record): string => CourierHelper::getName($record))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->getStateUsing(fn (Courier $record): ?string => CourierHelper::getPhone($record))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('vehicle_number')
                    ->label('Nomor Kendaraan')
                    ->getStateUsing(fn (Courier $record): ?string => CourierHelper::getVehicleNumber($record))
                    ->searchable(),

                TextColumn::make('assignedLocation.name')
                    ->label('Lokasi Ditugaskan')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->getStateUsing(fn (Courier $record): string => CourierHelper::isActive($record) ? 'Aktif' : 'Nonaktif')
                    ->badge()
                    ->color(fn (Courier $record): string => CourierHelper::isActive($record) ? 'success' : 'danger'),

                TextColumn::make('transactions_count')
                    ->label('Total Transaksi')
                    ->counts('transactions')
                    ->sortable(),

                TextColumn::make('payments_count')
                    ->label('Total Payment')
                    ->counts('payments')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
