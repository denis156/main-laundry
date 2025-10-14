<?php

declare(strict_types=1);

namespace App\Filament\Resources\Materials\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Size;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\TernaryFilter;

class MaterialsTable
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
                    TextColumn::make('name')
                        ->label('Nama Bahan')
                        ->searchable()
                        ->weight('semibold')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('type')
                        ->label('Jenis Bahan')
                        ->searchable()
                        ->badge()
                        ->color('info')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('unit')
                        ->label('Satuan')
                        ->searchable()
                        ->alignCenter()
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Stok & Harga', [
                    TextColumn::make('initial_stock')
                        ->label('Stok Awal')
                        ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.'))
                        ->sortable()
                        ->alignCenter()
                        ->fontFamily('mono')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('current_stock')
                        ->label('Stok Sekarang')
                        ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.'))
                        ->sortable()
                        ->alignCenter()
                        ->weight('semibold')
                        ->fontFamily('mono')
                        ->color(fn($record) => $record->minimum_stock && $record->current_stock <= $record->minimum_stock ? 'danger' : 'success')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('minimum_stock')
                        ->label('Stok Minimum')
                        ->formatStateUsing(fn($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                        ->sortable()
                        ->alignCenter()
                        ->fontFamily('mono')
                        ->placeholder('Tidak diset')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('price_per_unit')
                        ->label('Harga/Satuan')
                        ->money('IDR')
                        ->sortable()
                        ->alignEnd()
                        ->fontFamily('mono')
                        ->placeholder('Belum diset')
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Informasi Lainnya', [
                    TextColumn::make('expired_date')
                        ->label('Tanggal Kadaluarsa')
                        ->date('d F Y')
                        ->sortable()
                        ->placeholder('Tidak ada')
                        ->color(fn($state) => $state && $state->isPast() ? 'danger' : 'success')
                        ->weight(fn($state) => $state && $state->isPast() ? 'bold' : 'normal')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('lastUpdatedBy.name')
                        ->label('Terakhir Update Oleh')
                        ->placeholder('Belum ada update')
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
                ColumnGroup::make('Tanggal & Waktu', [
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
                ]),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label('Status Data')
                    ->native(false),
                SelectFilter::make('type')
                    ->label('Jenis Bahan')
                    ->native(false)
                    ->options([
                        'detergen' => 'Detergen',
                        'pewangi' => 'Pewangi',
                        'softener' => 'Softener',
                        'pemutih' => 'Pemutih',
                        'plastik' => 'Plastik',
                        'aksesoris' => 'Aksesoris',
                    ])
                    ->placeholder('Semua jenis bahan'),
                TernaryFilter::make('expiring_soon')
                    ->label('Status Kadaluarsa')
                    ->native(false)
                    ->nullable()
                    ->placeholder('Semua bahan')
                    ->trueLabel('Akan kadaluarsa (30 hari)')
                    ->falseLabel('Masih aman')
                    ->queries(
                        true: fn($query) => $query->whereNotNull('expired_date')
                            ->where('expired_date', '<=', now()->addDays(30)),
                        false: fn($query) => $query->where(function ($q) {
                            $q->whereNull('expired_date')
                                ->orWhere('expired_date', '>', now()->addDays(30));
                        }),
                        blank: fn($query) => $query,
                    ),
            ])
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->size(Size::Medium)
                    ->color('primary')
                    ->icon('solar-filter-linear')
                    ->label('Filter')
                    ->tooltip('Filter data bahan'),
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
                    ->tooltip('Ubah bahan ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('warning')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus bahan ini'),
                ActionGroup::make([
                    ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->color('danger')
                        ->icon('solar-trash-bin-2-linear')
                        ->modalIcon('solar-trash-bin-2-bold')
                        ->tooltip('Hapus selamanya bahan ini'),
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->color('gray')
                        ->icon('solar-refresh-linear')
                        ->modalIcon('solar-refresh-bold')
                        ->tooltip('Pulihkan bahan ini'),
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
                    ->tooltip('Buat bahan baru'),
            ])
            ->striped()
            ->defaultSort('created_at', direction: 'desc');
    }
}
