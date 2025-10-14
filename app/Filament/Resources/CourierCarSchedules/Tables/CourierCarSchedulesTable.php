<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierCarSchedules\Tables;

use App\Models\Resort;
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

class CourierCarSchedulesTable
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
                ColumnGroup::make('Informasi Jadwal', [
                    TextColumn::make('trip_date')
                        ->label('Tanggal Perjalanan')
                        ->date('d F Y')
                        ->sortable()
                        ->searchable()
                        ->weight('semibold')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('departure_time')
                        ->label('Waktu Keberangkatan')
                        ->time('H:i')
                        ->sortable()
                        ->fontFamily('mono')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('trip_type')
                        ->label('Jenis Trip')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'pickup' => 'Ambil dari Resort',
                            'delivery' => 'Antar ke Resort',
                            default => $state,
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'pickup' => 'info',
                            'delivery' => 'success',
                            default => 'gray',
                        })
                        ->icon(fn (string $state): string => match ($state) {
                            'pickup' => 'solar-box-linear',
                            'delivery' => 'solar-delivery-linear',
                            default => 'solar-question-circle-linear',
                        })
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('resort_ids')
                        ->label('Resort Dikunjungi')
                        ->getStateUsing(function ($record) {
                            if (empty($record->resort_ids)) {
                                return null;
                            }
                            $resortIds = is_array($record->resort_ids) ? $record->resort_ids : [];
                            return Resort::whereIn('id', $resortIds)->pluck('name')->toArray();
                        })
                        ->listWithLineBreaks()
                        ->bulleted()
                        ->limitList(2)
                        ->expandableLimitedList()
                        ->placeholder('Belum ada resort')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('status')
                        ->label('Status')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'scheduled' => 'Dijadwalkan',
                            'in_progress' => 'Sedang Berlangsung',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                            default => $state,
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'scheduled' => 'info',
                            'in_progress' => 'warning',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                            default => 'gray',
                        })
                        ->icon(fn (string $state): string => match ($state) {
                            'scheduled' => 'solar-calendar-mark-linear',
                            'in_progress' => 'solar-clock-circle-linear',
                            'completed' => 'solar-check-circle-linear',
                            'cancelled' => 'solar-close-circle-linear',
                            default => 'solar-question-circle-linear',
                        })
                        ->toggleable(isToggledHiddenByDefault: false),
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
                SelectFilter::make('trip_type')
                    ->label('Jenis Trip')
                    ->native(false)
                    ->options([
                        'pickup' => 'Ambil dari Resort',
                        'delivery' => 'Antar ke Resort',
                    ])
                    ->placeholder('Semua jenis trip'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->native(false)
                    ->options([
                        'scheduled' => 'Dijadwalkan',
                        'in_progress' => 'Sedang Berlangsung',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
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
                    ->tooltip('Filter data jadwal kurir mobil'),
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
                    ->tooltip('Ubah jadwal ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('warning')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus jadwal ini'),
                ActionGroup::make([
                    ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->color('danger')
                        ->icon('solar-trash-bin-2-linear')
                        ->modalIcon('solar-trash-bin-2-bold')
                        ->tooltip('Hapus selamanya jadwal ini'),
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->color('gray')
                        ->icon('solar-refresh-linear')
                        ->modalIcon('solar-refresh-bold')
                        ->tooltip('Pulihkan jadwal ini'),
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
                    ->tooltip('Buat jadwal baru'),
            ])
            ->striped()
            ->defaultSort('trip_date', direction: 'desc');
    }
}
