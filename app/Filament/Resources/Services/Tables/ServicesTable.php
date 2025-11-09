<?php

declare(strict_types=1);

namespace App\Filament\Resources\Services\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Size;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use App\Helper\Database\ServiceHelper;

class ServicesTable
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
                ColumnGroup::make('Informasi Layanan', [
                    TextColumn::make('name')
                        ->label('Nama Layanan')
                        ->searchable()
                        ->weight('semibold')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('data.pricing.price_per_kg')
                        ->label('Harga/Kg')
                        ->sortable()
                        ->alignCenter()
                        ->fontFamily('mono')
                        ->getStateUsing(fn($record) => ServiceHelper::getFormattedPrice($record))
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('data.duration_hours')
                        ->label('Durasi')
                        ->numeric()
                        ->sortable()
                        ->alignCenter()
                        ->getStateUsing(fn($record) => ServiceHelper::getFormattedDuration($record))
                        ->toggleable(isToggledHiddenByDefault: false),
                    IconColumn::make('is_featured')
                        ->label('Unggulan')
                        ->boolean()
                        ->alignCenter()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('sort_order')
                        ->label('Urutan')
                        ->numeric()
                        ->sortable()
                        ->alignCenter()
                        ->toggleable(isToggledHiddenByDefault: false),
                    IconColumn::make('is_active')
                        ->label('Status Aktif')
                        ->boolean()
                        ->alignCenter()
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
                TernaryFilter::make('is_active')
                    ->label('Status Layanan')
                    ->native(false)
                    ->nullable()
                    ->placeholder('Semua layanan')
                    ->trueLabel('Layanan aktif')
                    ->falseLabel('Layanan tidak aktif')
                    ->queries(
                        true: fn($query) => $query->where('is_active', true),
                        false: fn($query) => $query->where('is_active', false),
                        blank: fn($query) => $query,
                    ),
                TernaryFilter::make('is_featured')
                    ->label('Layanan Unggulan')
                    ->native(false)
                    ->nullable()
                    ->placeholder('Semua layanan')
                    ->trueLabel('Layanan unggulan')
                    ->falseLabel('Layanan biasa')
                    ->queries(
                        true: fn($query) => $query->where('is_featured', true),
                        false: fn($query) => $query->where('is_featured', false),
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
                    ->tooltip('Filter data layanan'),
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
                    ->tooltip('Ubah layanan ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('warning')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus layanan ini'),
                ActionGroup::make([
                    ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->color('danger')
                        ->icon('solar-trash-bin-2-linear')
                        ->modalIcon('solar-trash-bin-2-bold')
                        ->tooltip('Hapus selamanya layanan ini'),
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->color('gray')
                        ->icon('solar-refresh-linear')
                        ->modalIcon('solar-refresh-bold')
                        ->tooltip('Pulihkan layanan ini'),
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
                    ->tooltip('Buat layanan baru'),
            ])
            ->striped()
            ->defaultSort('sort_order', direction: 'asc');
    }
}
