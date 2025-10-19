<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pos\Tables;

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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;

class PosTable
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
                ColumnGroup::make('Informasi Pos', [
                    TextColumn::make('name')
                        ->label('Nama Pos')
                        ->searchable()
                        ->weight('semibold')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('resort.name')
                        ->label('Resort Induk')
                        ->searchable()
                        ->badge()
                        ->color('primary')
                        ->placeholder('Pos Mandiri')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('pic_name')
                        ->label('Penanggung Jawab')
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('phone')
                        ->label('Telepon')
                        ->searchable()
                        ->fontFamily('mono')
                        ->toggleable(isToggledHiddenByDefault: false),
                    IconColumn::make('is_active')
                        ->label('Status Aktif')
                        ->boolean()
                        ->alignCenter()
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Wilayah', [
                    TextColumn::make('district_name')
                        ->label('Kecamatan')
                        ->searchable()
                        ->placeholder('-')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('village_name')
                        ->label('Kelurahan')
                        ->searchable()
                        ->placeholder('-')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('address')
                        ->label('Alamat Lengkap')
                        ->searchable()
                        ->placeholder('-')
                        ->limit(50)
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('area')
                        ->label('Area Layanan')
                        ->searchable()
                        ->bulleted()
                        ->limitList(2)
                        ->expandableLimitedList()
                        ->listWithLineBreaks()
                        ->placeholder('Belum ada area')
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
                    ->label('Status Pos')
                    ->native(false)
                    ->nullable()
                    ->placeholder('Semua pos')
                    ->trueLabel('Pos aktif')
                    ->falseLabel('Pos tidak aktif')
                    ->queries(
                        true: fn($query) => $query->where('is_active', true),
                        false: fn($query) => $query->where('is_active', false),
                        blank: fn($query) => $query,
                    ),
                SelectFilter::make('resort_id')
                    ->label('Resort Induk')
                    ->native(false)
                    ->relationship('resort', 'name')
                    ->placeholder('Semua pos')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('has_resort')
                    ->label('Tipe Pos')
                    ->native(false)
                    ->nullable()
                    ->placeholder('Semua tipe')
                    ->trueLabel('Terkait resort')
                    ->falseLabel('Pos mandiri')
                    ->queries(
                        true: fn($query) => $query->whereNotNull('resort_id'),
                        false: fn($query) => $query->whereNull('resort_id'),
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
                    ->tooltip('Filter data pos'),
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
                    ->tooltip('Ubah pos ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('warning')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus pos ini'),
                ActionGroup::make([
                    ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->color('danger')
                        ->icon('solar-trash-bin-2-linear')
                        ->modalIcon('solar-trash-bin-2-bold')
                        ->tooltip('Hapus selamanya pos ini'),
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->color('gray')
                        ->icon('solar-refresh-linear')
                        ->modalIcon('solar-refresh-bold')
                        ->tooltip('Pulihkan pos ini'),
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
                    ->tooltip('Buat pos baru'),
            ])
            ->striped()
            ->defaultSort('created_at', direction: 'desc');
    }
}
