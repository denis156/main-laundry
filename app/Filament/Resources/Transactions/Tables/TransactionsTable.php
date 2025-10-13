<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Tables;

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

class TransactionsTable
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
                ColumnGroup::make('Informasi Transaksi', [
                    TextColumn::make('invoice_number')
                        ->label('No. Invoice')
                        ->searchable()
                        ->weight('semibold')
                        ->fontFamily('mono')
                        ->copyable()
                        ->copyMessage('Invoice disalin!')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('customer.name')
                        ->label('Pelanggan')
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('user.name')
                        ->label('Kasir')
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('status')
                        ->label('Status Order')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'pending' => 'warning',
                            'processing' => 'info',
                            'ready' => 'success',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'pending' => 'Menunggu',
                            'processing' => 'Diproses',
                            'ready' => 'Siap',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                            default => $state,
                        })
                        ->alignCenter()
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('payment_status')
                        ->label('Status Bayar')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'unpaid' => 'danger',
                            'partial' => 'warning',
                            'paid' => 'success',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'unpaid' => 'Belum Bayar',
                            'partial' => 'Cicilan',
                            'paid' => 'Lunas',
                            default => $state,
                        })
                        ->alignCenter()
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Detail Order', [
                    TextColumn::make('total_weight')
                        ->label('Total Berat')
                        ->numeric(decimalPlaces: 2)
                        ->suffix(' Kg')
                        ->sortable()
                        ->alignCenter()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('subtotal')
                        ->label('Subtotal')
                        ->money('IDR')
                        ->sortable()
                        ->alignEnd()
                        ->fontFamily('mono')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('total_discount_amount')
                        ->label('Total Diskon')
                        ->money('IDR')
                        ->sortable()
                        ->alignEnd()
                        ->fontFamily('mono')
                        ->color('success')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('total_price')
                        ->label('Total Bayar')
                        ->money('IDR')
                        ->sortable()
                        ->alignEnd()
                        ->fontFamily('mono')
                        ->weight('semibold')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('paid_amount')
                        ->label('Terbayar')
                        ->money('IDR')
                        ->sortable()
                        ->alignEnd()
                        ->fontFamily('mono')
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
                ColumnGroup::make('Promo & Diskon', [
                    TextColumn::make('promo.name')
                        ->label('Promo')
                        ->placeholder('Tanpa Promo')
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('promo_discount_amount')
                        ->label('Diskon Promo')
                        ->money('IDR')
                        ->placeholder('-')
                        ->sortable()
                        ->alignEnd()
                        ->fontFamily('mono')
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
                ColumnGroup::make('Tanggal & Waktu', [
                    TextColumn::make('order_date')
                        ->label('Tgl Order')
                        ->dateTime('d M Y H:i')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('estimated_finish_date')
                        ->label('Estimasi Selesai')
                        ->dateTime('d M Y H:i')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('actual_finish_date')
                        ->label('Selesai Aktual')
                        ->dateTime('d M Y H:i')
                        ->placeholder('Belum Selesai')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('status')
                    ->label('Status Order')
                    ->native(false)
                    ->options([
                        'pending' => 'Menunggu',
                        'processing' => 'Diproses',
                        'ready' => 'Siap',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->placeholder('Semua status order'),
                SelectFilter::make('payment_status')
                    ->label('Status Pembayaran')
                    ->native(false)
                    ->options([
                        'unpaid' => 'Belum Bayar',
                        'partial' => 'Cicilan',
                        'paid' => 'Lunas',
                    ])
                    ->placeholder('Semua status bayar'),
            ])
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->size(Size::Medium)
                    ->color('primary')
                    ->icon('solar-filter-linear')
                    ->label('Filter')
                    ->tooltip('Filter data transaksi'),
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
                    ->tooltip('Ubah transaksi ini'),
                DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->size(Size::Small)
                    ->color('warning')
                    ->outlined()
                    ->icon('solar-trash-bin-minimalistic-bold')
                    ->modalIcon('solar-trash-bin-minimalistic-bold')
                    ->tooltip('Hapus transaksi ini'),
                ActionGroup::make([
                    ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->color('danger')
                        ->icon('solar-trash-bin-2-linear')
                        ->modalIcon('solar-trash-bin-2-bold')
                        ->tooltip('Hapus selamanya transaksi ini'),
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->color('gray')
                        ->icon('solar-refresh-linear')
                        ->modalIcon('solar-refresh-bold')
                        ->tooltip('Pulihkan transaksi ini'),
                ])
                    ->label('Lainnya')
                    ->color('info')
                    ->icon('solar-menu-dots-circle-bold')
                    ->outlined()
                    ->button()
                    ->size(Size::Small),
            ])
            ->toolbarActions([
                // Action::make('create')
                //     ->label('Buat')
                //     ->button()
                //     ->size(Size::Medium)
                //     ->color('primary')
                //     ->icon('solar-add-circle-linear')
                //     ->url(fn() => route('filament.admin.pages.kasir'))
                //     ->tooltip('Buat transaksi baru di halaman kasir'),
            ])
            ->striped()
            ->defaultSort('order_date', direction: 'desc');
    }
}
