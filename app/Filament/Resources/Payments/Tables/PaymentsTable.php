<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Size;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;

class PaymentsTable
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
                ColumnGroup::make('Informasi Pembayaran', [
                    TextColumn::make('transaction.invoice_number')
                        ->label('No. Invoice')
                        ->searchable()
                        ->sortable()
                        ->weight('semibold')
                        ->fontFamily('mono')
                        ->copyable()
                        ->copyMessage('Invoice disalin!')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('transaction.customer.name')
                        ->label('Nama Customer')
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('courierMotorcycle.name')
                        ->label('Kurir Motor')
                        ->searchable()
                        ->sortable()
                        ->badge()
                        ->color('info')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('amount')
                        ->label('Jumlah Pembayaran')
                        ->money('IDR')
                        ->sortable()
                        ->alignEnd()
                        ->fontFamily('mono')
                        ->weight('semibold')
                        ->color('success')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('payment_date')
                        ->label('Tanggal Pembayaran')
                        ->dateTime('d F Y, H:i')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    ImageColumn::make('payment_proof_url')
                        ->label('Bukti Pembayaran')
                        ->disk('public')
                        ->imageWidth(60)
                        ->imageHeight(60)
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
                SelectFilter::make('courier_motorcycle_id')
                    ->label('Kurir Motor')
                    ->native(false)
                    ->relationship('courierMotorcycle', 'name')
                    ->placeholder('Semua kurir motor')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('transaction.customer_id')
                    ->label('Customer')
                    ->native(false)
                    ->relationship('transaction.customer', 'name')
                    ->placeholder('Semua customer')
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
                    ->tooltip('Filter data pembayaran'),
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
                    ->tooltip('Ubah pembayaran ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('warning')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus pembayaran ini'),
                ActionGroup::make([
                    ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->color('danger')
                        ->icon('solar-trash-bin-2-linear')
                        ->modalIcon('solar-trash-bin-2-bold')
                        ->tooltip('Hapus selamanya pembayaran ini'),
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->color('gray')
                        ->icon('solar-refresh-linear')
                        ->modalIcon('solar-refresh-bold')
                        ->tooltip('Pulihkan pembayaran ini'),
                ])
                    ->label('Lainnya')
                    ->color('info')
                    ->icon('solar-menu-dots-circle-bold')
                    ->outlined()
                    ->button()
                    ->size(Size::Small),
            ])
            ->striped()
            ->defaultSort('payment_date', direction: 'desc');
    }
}
