<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierMotorcycles\Tables;

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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;

class CourierMotorcyclesTable
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
                ColumnGroup::make('Informasi Kurir', [
                    ImageColumn::make('avatar_url')
                        ->label('Foto')
                        ->circular()
                        ->getStateUsing(fn($record) => $record->getFilamentAvatarUrl())
                        ->alignCenter(),
                    TextColumn::make('name')
                        ->label('Nama Kurir')
                        ->searchable()
                        ->weight('semibold')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('email')
                        ->label('Email')
                        ->searchable()
                        ->fontFamily('mono')
                        ->copyable()
                        ->copyMessage('Email disalin!')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('phone')
                        ->label('Telepon')
                        ->searchable()
                        ->fontFamily('mono')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('vehicle_number')
                        ->label('Nomor Kendaraan')
                        ->searchable()
                        ->fontFamily('mono')
                        ->badge()
                        ->color('info')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('assignedResort.name')
                        ->label('Resort Ditugaskan')
                        ->searchable()
                        ->badge()
                        ->color('primary')
                        ->placeholder('Belum ditugaskan')
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
                    ->label('Status Kurir')
                    ->native(false)
                    ->nullable()
                    ->placeholder('Semua kurir')
                    ->trueLabel('Kurir aktif')
                    ->falseLabel('Kurir tidak aktif')
                    ->queries(
                        true: fn($query) => $query->where('is_active', true),
                        false: fn($query) => $query->where('is_active', false),
                        blank: fn($query) => $query,
                    ),
                SelectFilter::make('assigned_resort_id')
                    ->label('Resort')
                    ->native(false)
                    ->relationship('assignedResort', 'name')
                    ->placeholder('Semua resort')
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
                    ->tooltip('Filter data kurir motor'),
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
                    ->tooltip('Ubah kurir ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('warning')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus kurir ini'),
                ActionGroup::make([
                    ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->color('danger')
                        ->icon('solar-trash-bin-2-linear')
                        ->modalIcon('solar-trash-bin-2-bold')
                        ->tooltip('Hapus selamanya kurir ini'),
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->color('gray')
                        ->icon('solar-refresh-linear')
                        ->modalIcon('solar-refresh-bold')
                        ->tooltip('Pulihkan kurir ini'),
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
                    ->tooltip('Buat kurir motor baru'),
            ])
            ->striped()
            ->defaultSort('created_at', direction: 'desc');
    }
}
