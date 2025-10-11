<?php

declare(strict_types=1);

namespace App\Filament\Resources\Members\Tables;

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

class MembersTable
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
                ColumnGroup::make('Informasi Member', [
                    TextColumn::make('member_number')
                        ->label('Nomor Member')
                        ->searchable()
                        ->fontFamily('mono')
                        ->weight('semibold')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('customer.name')
                        ->label('Nama Pelanggan')
                        ->searchable()
                        ->weight('medium')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('membershipTier.name')
                        ->label('Tier Membership')
                        ->searchable()
                        ->badge()
                        ->color('success')
                        ->alignCenter()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('total_points')
                        ->label('Total Poin')
                        ->numeric()
                        ->sortable()
                        ->fontFamily('mono')
                        ->alignCenter()
                        ->toggleable(isToggledHiddenByDefault: false),
                    IconColumn::make('is_active')
                        ->label('Status Aktif')
                        ->alignCenter()
                        ->boolean()
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Tanggal & Waktu', [
                    TextColumn::make('member_since')
                        ->label('Member Sejak')
                        ->since()
                        ->badge()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
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
                    ->label('Status Member')
                    ->native(false)
                    ->nullable()
                    ->placeholder('Semua member')
                    ->trueLabel('Member aktif')
                    ->falseLabel('Member tidak aktif')
                    ->queries(
                        true: fn($query) => $query->where('is_active', true),
                        false: fn($query) => $query->where('is_active', false),
                        blank: fn($query) => $query,
                    )
            ])
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->size(Size::Medium)
                    ->color('primary')
                    ->icon('solar-filter-linear')
                    ->label('Filter')
                    ->tooltip('Filter data member'),
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
                    ->tooltip('Ubah member ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('warning')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus member ini'),
                ActionGroup::make([
                    ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->color('danger')
                        ->icon('solar-trash-bin-2-linear')
                        ->modalIcon('solar-trash-bin-2-bold')
                        ->tooltip('Hapus selamanya member ini'),
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->color('gray')
                        ->icon('solar-refresh-linear')
                        ->modalIcon('solar-refresh-bold')
                        ->tooltip('Pulihkan member ini'),
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
                    ->tooltip('Buat member baru'),
            ])
            ->striped()
            ->defaultSort('created_at', direction: 'desc');
    }
}
