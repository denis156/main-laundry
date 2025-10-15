<?php

declare(strict_types=1);

namespace App\Filament\Resources\EquipmentMaintenances\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Size;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Filters\SelectFilter;

class EquipmentMaintenancesTable
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
                ColumnGroup::make('Informasi Peralatan', [
                    TextColumn::make('equipment.name')
                        ->label('Nama Peralatan')
                        ->searchable()
                        ->sortable()
                        ->weight('semibold')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('equipment.type')
                        ->label('Jenis Peralatan')
                        ->badge()
                        ->color('info')
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Detail Perawatan', [
                    TextColumn::make('maintenance_date')
                        ->label('Tanggal Perawatan')
                        ->date('d F Y')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('cost')
                        ->label('Biaya')
                        ->money('IDR')
                        ->sortable()
                        ->alignEnd()
                        ->fontFamily('mono')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('performed_by')
                        ->label('Dilakukan Oleh')
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Deskripsi & Waktu', [
                    TextColumn::make('description')
                        ->label('Deskripsi')
                        ->limit(50)
                        ->tooltip(function (TextColumn $column): ?string {
                            $state = $column->getState();
                            if (strlen($state) > 50) {
                                return $state;
                            }
                            return null;
                        })
                        ->placeholder('Tidak ada deskripsi')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('created_at')
                        ->label('Dibuat Pada')
                        ->dateTime('d F Y, H:i')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('updated_at')
                        ->label('Terakhir Diubah')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
            ])
            ->filters([
                SelectFilter::make('equipment_id')
                    ->label('Peralatan')
                    ->native(false)
                    ->relationship('equipment', 'name')
                    ->placeholder('Semua peralatan')
                    ->searchable()
                    ->preload(),
            ])
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->size(Size::Medium)
                    ->color('primary')
                    ->icon('solar-filter-linear')
                    ->label('Filter')
                    ->tooltip('Filter data perawatan'),
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
                    ->tooltip('Ubah perawatan ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('danger')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus perawatan ini'),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->label('Buat')
                    ->button()
                    ->size(Size::Medium)
                    ->icon('solar-add-circle-linear')
                    ->tooltip('Buat perawatan baru'),
            ])
            ->striped()
            ->defaultSort('maintenance_date', direction: 'desc');
    }
}
