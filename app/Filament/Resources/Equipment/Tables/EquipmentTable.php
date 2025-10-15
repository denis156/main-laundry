<?php

declare(strict_types=1);

namespace App\Filament\Resources\Equipment\Tables;

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

class EquipmentTable
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
                    TextColumn::make('name')
                        ->label('Nama Peralatan')
                        ->searchable()
                        ->weight('semibold')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('type')
                        ->label('Jenis')
                        ->searchable()
                        ->badge()
                        ->color('info')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('brand')
                        ->label('Merk')
                        ->searchable()
                        ->placeholder('Tidak ada merk')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('serial_number')
                        ->label('Nomor Seri')
                        ->searchable()
                        ->fontFamily('mono')
                        ->placeholder('Tidak ada nomor seri')
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Pembelian', [
                    TextColumn::make('purchase_price')
                        ->label('Harga Beli')
                        ->money('IDR')
                        ->sortable()
                        ->alignEnd()
                        ->fontFamily('mono')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('purchase_date')
                        ->label('Tanggal Beli')
                        ->date('d F Y')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Status & Perawatan', [
                    TextColumn::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'baik' => 'success',
                            'maintenance' => 'warning',
                            'rusak' => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'baik' => 'Baik',
                            'maintenance' => 'Dalam Perawatan',
                            'rusak' => 'Rusak',
                            default => $state,
                        })
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('last_maintenance_date')
                        ->label('Perawatan Terakhir')
                        ->date('d F Y')
                        ->sortable()
                        ->placeholder('Belum pernah')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('last_maintenance_cost')
                        ->label('Biaya Perawatan Terakhir')
                        ->money('IDR')
                        ->sortable()
                        ->alignEnd()
                        ->fontFamily('mono')
                        ->placeholder('Rp 0')
                        ->toggleable(isToggledHiddenByDefault: false),
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
                    ->label('Jenis Peralatan')
                    ->native(false)
                    ->options([
                        'mesin cuci' => 'Mesin Cuci',
                        'setrika' => 'Setrika',
                        'pengering' => 'Pengering',
                    ])
                    ->placeholder('Semua jenis'),
                SelectFilter::make('status')
                    ->label('Status Kondisi')
                    ->native(false)
                    ->options([
                        'baik' => 'Baik',
                        'maintenance' => 'Dalam Perawatan',
                        'rusak' => 'Rusak',
                    ])
                    ->placeholder('Semua status'),
            ])
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->size(Size::Medium)
                    ->color('primary')
                    ->icon('solar-filter-linear')
                    ->label('Filter')
                    ->tooltip('Filter data peralatan'),
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
                    ->tooltip('Ubah peralatan ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('warning')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus peralatan ini'),
                ActionGroup::make([
                    ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->color('danger')
                        ->icon('solar-trash-bin-2-linear')
                        ->modalIcon('solar-trash-bin-2-bold')
                        ->tooltip('Hapus selamanya peralatan ini'),
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->color('gray')
                        ->icon('solar-refresh-linear')
                        ->modalIcon('solar-refresh-bold')
                        ->tooltip('Pulihkan peralatan ini'),
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
                    ->tooltip('Buat peralatan baru'),
            ])
            ->striped()
            ->defaultSort('created_at', direction: 'desc');
    }
}
