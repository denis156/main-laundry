<?php

declare(strict_types=1);

namespace App\Filament\Resources\Resources\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Size;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ForceDeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use App\Helper\Database\ResourceHelper;

class ResourcesTable
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
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'equipment' => 'Peralatan',
                        'material' => 'Material',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'equipment' => 'info',
                        'material' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'equipment' => 'solar-box-minimalistic-linear',
                        'material' => 'solar-jar-of-pills-2-linear',
                        default => '',
                    })
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->weight('semibold')
                    ->sortable(),

                // Equipment specific columns
                TextColumn::make('data.brand')
                    ->label('Merek')
                    ->default('-')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('data.status')
                    ->label('Kondisi')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'baik' => 'Baik',
                        'rusak' => 'Rusak',
                        'maintenance' => 'Maintenance',
                        default => '-',
                    })
                    ->color(fn(?string $state): string => match ($state) {
                        'baik' => 'success',
                        'rusak' => 'danger',
                        'maintenance' => 'warning',
                        default => 'gray',
                    })
                    ->default('-')
                    ->toggleable(),

                // Material specific columns
                TextColumn::make('data.unit')
                    ->label('Satuan')
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'kg' => 'Kilogram (kg)',
                        'liter' => 'Liter (L)',
                        'pcs' => 'Pieces (pcs)',
                        'box' => 'Box',
                        'pack' => 'Pack',
                        default => $state ?? '-',
                    })
                    ->default('-')
                    ->toggleable(),
                TextColumn::make('data.stocks.current')
                    ->label('Stok Saat Ini')
                    ->numeric()
                    ->default(0)
                    ->getStateUsing(fn($record) => $record->data['stocks']['current'] ?? 0)
                    ->badge()
                    ->color(fn($record): string => ResourceHelper::isLowStock($record) ? 'danger' : 'success')
                    ->toggleable(),
                TextColumn::make('data.stocks.minimum')
                    ->label('Stok Min')
                    ->numeric()
                    ->default(0)
                    ->getStateUsing(fn($record) => $record->data['stocks']['minimum'] ?? 0)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable()
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
                TrashedFilter::make()
                    ->label('Status Data')
                    ->native(false),
                SelectFilter::make('type')
                    ->label('Jenis Sumber Daya')
                    ->options([
                        'equipment' => 'Peralatan',
                        'material' => 'Material',
                    ])
                    ->native(false),
                SelectFilter::make('is_active')
                    ->label('Status Aktif')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif',
                    ])
                    ->native(false),
            ])
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->size(Size::Medium)
                    ->color('primary')
                    ->icon('solar-filter-linear')
                    ->label('Filter')
                    ->tooltip('Filter data sumber daya'),
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
                    ->tooltip('Ubah sumber daya ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('warning')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus sumber daya ini'),
                ActionGroup::make([
                    ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->color('danger')
                        ->icon('solar-trash-bin-2-linear')
                        ->modalIcon('solar-trash-bin-2-bold')
                        ->tooltip('Hapus selamanya sumber daya ini'),
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->color('gray')
                        ->icon('solar-refresh-linear')
                        ->modalIcon('solar-refresh-bold')
                        ->tooltip('Pulihkan sumber daya ini'),
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
                    ->tooltip('Buat sumber daya baru'),
            ])
            ->striped()
            ->defaultSort('created_at', direction: 'desc');
    }
}
