<?php

declare(strict_types=1);

namespace App\Filament\Resources\Locations\Tables;

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

class LocationsTable
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
                ColumnGroup::make('Informasi Lokasi', [
                    TextColumn::make('type')
                        ->label('Tipe')
                        ->searchable()
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'resort' => 'success',
                            'pos' => 'info',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'resort' => 'Resort',
                            'pos' => 'POS',
                            default => $state,
                        })
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('name')
                        ->label('Nama Lokasi')
                        ->searchable()
                        ->weight('semibold')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('parent.name')
                        ->label('Resort Induk')
                        ->searchable()
                        ->badge()
                        ->color('primary')
                        ->placeholder('Lokasi Mandiri')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('data.contact.pic_name')
                        ->label('Penanggung Jawab')
                        ->searchable()
                        ->placeholder('-')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('data.contact.phone')
                        ->label('Telepon')
                        ->searchable()
                        ->fontFamily('mono')
                        ->placeholder('-')
                        ->formatStateUsing(fn(?string $state): string => $state ? '+62' . $state : '-')
                        ->toggleable(isToggledHiddenByDefault: false),
                    IconColumn::make('is_active')
                        ->label('Status Aktif')
                        ->boolean()
                        ->alignCenter()
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Wilayah', [
                    TextColumn::make('data.location.district_name')
                        ->label('Kecamatan')
                        ->searchable()
                        ->placeholder('-')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('data.location.village_name')
                        ->label('Kel/Desa')
                        ->searchable()
                        ->placeholder('-')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('data.location.address')
                        ->label('Alamat Lengkap')
                        ->searchable()
                        ->placeholder('-')
                        ->limit(50)
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('data.coverage_area')
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
                SelectFilter::make('type')
                    ->label('Tipe Lokasi')
                    ->native(false)
                    ->options([
                        'resort' => 'Resort',
                        'pos' => 'POS',
                    ])
                    ->placeholder('Semua tipe'),
                TernaryFilter::make('is_active')
                    ->label('Status Lokasi')
                    ->native(false)
                    ->nullable()
                    ->placeholder('Semua lokasi')
                    ->trueLabel('Lokasi aktif')
                    ->falseLabel('Lokasi tidak aktif')
                    ->queries(
                        true: fn($query) => $query->where('is_active', true),
                        false: fn($query) => $query->where('is_active', false),
                        blank: fn($query) => $query,
                    ),
                SelectFilter::make('parent_id')
                    ->label('Resort Induk')
                    ->native(false)
                    ->relationship('parent', 'name')
                    ->placeholder('Semua lokasi')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('has_parent')
                    ->label('Tipe POS')
                    ->native(false)
                    ->nullable()
                    ->placeholder('Semua POS')
                    ->trueLabel('Terkait resort')
                    ->falseLabel('POS mandiri')
                    ->queries(
                        true: fn($query) => $query->where('type', 'pos')->whereNotNull('parent_id'),
                        false: fn($query) => $query->where('type', 'pos')->whereNull('parent_id'),
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
                    ->tooltip('Filter data lokasi'),
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
                    ->tooltip('Ubah lokasi ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('warning')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus lokasi ini'),
                ActionGroup::make([
                    ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->color('danger')
                        ->icon('solar-trash-bin-2-linear')
                        ->modalIcon('solar-trash-bin-2-bold')
                        ->tooltip('Hapus selamanya lokasi ini'),
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->color('gray')
                        ->icon('solar-refresh-linear')
                        ->modalIcon('solar-refresh-bold')
                        ->tooltip('Pulihkan lokasi ini'),
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
                    ->tooltip('Buat lokasi baru'),
            ])
            ->striped()
            ->defaultSort('created_at', direction: 'desc');
    }
}
