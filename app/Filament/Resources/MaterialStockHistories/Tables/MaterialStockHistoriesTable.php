<?php

declare(strict_types=1);

namespace App\Filament\Resources\MaterialStockHistories\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Size;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Filters\SelectFilter;

class MaterialStockHistoriesTable
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
                ColumnGroup::make('Informasi Bahan', [
                    TextColumn::make('material.name')
                        ->label('Nama Bahan')
                        ->searchable()
                        ->sortable()
                        ->weight('semibold')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('material.type')
                        ->label('Jenis Bahan')
                        ->badge()
                        ->color('info')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('material.unit')
                        ->label('Satuan')
                        ->alignCenter()
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Pergerakan Stock', [
                    TextColumn::make('type')
                        ->label('Tipe')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'in' => 'success',
                            'out' => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'in' => 'Masuk',
                            'out' => 'Keluar',
                            default => $state,
                        })
                        ->alignCenter()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('quantity')
                        ->label('Jumlah')
                        ->sortable()
                        ->alignCenter()
                        ->weight('semibold')
                        ->fontFamily('mono')
                        ->color(fn($record) => $record->type === 'in' ? 'success' : 'danger')
                        ->formatStateUsing(fn($state, $record) => ($record->type === 'in' ? '+' : '-') . number_format((float) $state, 0, ',', '.'))
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('stock_before')
                        ->label('Stock Sebelum')
                        ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.'))
                        ->sortable()
                        ->alignCenter()
                        ->fontFamily('mono')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('stock_after')
                        ->label('Stock Sesudah')
                        ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.'))
                        ->sortable()
                        ->alignCenter()
                        ->weight('semibold')
                        ->fontFamily('mono')
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Informasi Lainnya', [
                    TextColumn::make('notes')
                        ->label('Catatan')
                        ->limit(50)
                        ->tooltip(function (TextColumn $column): ?string {
                            $state = $column->getState();
                            if (strlen($state) > 50) {
                                return $state;
                            }
                            return null;
                        })
                        ->placeholder('Tidak ada catatan')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('createdBy.name')
                        ->label('Dibuat Oleh')
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Tanggal & Waktu', [
                    TextColumn::make('created_at')
                        ->label('Dibuat Pada')
                        ->dateTime('d F Y, H:i')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('updated_at')
                        ->label('Terakhir Diubah')
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Pergerakan')
                    ->native(false)
                    ->options([
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                    ])
                    ->placeholder('Semua tipe'),
                SelectFilter::make('material_id')
                    ->label('Bahan')
                    ->native(false)
                    ->relationship('material', 'name')
                    ->placeholder('Semua bahan')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('created_by')
                    ->label('Dibuat Oleh')
                    ->native(false)
                    ->relationship('createdBy', 'name')
                    ->placeholder('Semua user')
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
                    ->tooltip('Filter data riwayat stock'),
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
                    ->tooltip('Ubah riwayat ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('danger')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus riwayat ini'),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->label('Buat')
                    ->button()
                    ->size(Size::Medium)
                    ->icon('solar-add-circle-linear')
                    ->tooltip('Buat riwayat stock baru'),
            ])
            ->striped()
            ->defaultSort('created_at', direction: 'desc');
    }
}
