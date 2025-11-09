<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierCarSchedules\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Size;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ForceDeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;

class CourierCarSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Index')
                    ->label('No.')
                    ->rowIndex()
                    ->weight('bold')
                    ->alignCenter(),
                TextColumn::make('trip_date')
                    ->label('Tanggal Trip')
                    ->date('d M Y')
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('trip_type')
                    ->label('Jenis Trip')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pickup' => 'Penjemputan',
                        'delivery' => 'Pengantaran',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pickup' => 'info',
                        'delivery' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'pickup' => 'solar-box-linear',
                        'delivery' => 'solar-delivery-linear',
                        default => '',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'scheduled' => 'Terjadwal',
                        'in_progress' => 'Berlangsung',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'scheduled' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('data.departure_time')
                    ->label('Waktu Berangkat')
                    ->default('-')
                    ->toggleable(),
                TextColumn::make('data.driver_info.name')
                    ->label('Sopir')
                    ->default('-')
                    ->toggleable(),
                TextColumn::make('data.driver_info.vehicle_number')
                    ->label('No. Kendaraan')
                    ->default('-')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Terhapus Sejak')
                    ->placeholder('Data Aktif')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('trip_type')
                    ->label('Jenis Trip')
                    ->options([
                        'pickup' => 'Penjemputan',
                        'delivery' => 'Pengantaran',
                    ])
                    ->native(false),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'scheduled' => 'Terjadwal',
                        'in_progress' => 'Sedang Berlangsung',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->native(false)
                    ->multiple(),
                TrashedFilter::make()
                    ->label('Status Data')
                    ->native(false),
            ])
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->size(Size::Medium)
                    ->color('primary')
                    ->icon('solar-filter-linear')
                    ->label('Filter')
                    ->tooltip('Filter data jadwal'),
            )
            ->columnManagerTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->size(Size::Medium)
                    ->color('primary')
                    ->icon('solar-align-horizonta-spacing-linear')
                    ->label('Kolom')
                    ->tooltip('Kelola kolom tampilan'),
            )
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->button()
                    ->size(Size::Small)
                    ->color('success')
                    ->outlined()
                    ->icon('solar-pen-new-round-bold')
                    ->tooltip('Ubah jadwal ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('warning')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus jadwal ini'),
                ActionGroup::make([
                    ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->color('danger')
                        ->icon('solar-trash-bin-2-linear')
                        ->modalIcon('solar-trash-bin-2-bold')
                        ->tooltip('Hapus selamanya jadwal ini'),
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->color('gray')
                        ->icon('solar-refresh-linear')
                        ->modalIcon('solar-refresh-bold')
                        ->tooltip('Pulihkan jadwal ini'),
                ])
                    ->label('Lainnya')
                    ->color('info')
                    ->icon('solar-menu-dots-circle-bold')
                    ->outlined()
                    ->button()
                    ->size(Size::Small),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->label('Buat')
                    ->button()
                    ->size(Size::Medium)
                    ->icon('solar-add-circle-linear')
                    ->tooltip('Buat jadwal baru'),
            ])
            ->striped()
            ->defaultSort('trip_date', direction: 'desc');
    }
}
