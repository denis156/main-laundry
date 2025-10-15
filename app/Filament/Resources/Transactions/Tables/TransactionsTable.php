<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Size;
use Filament\Actions\ActionGroup;
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
                    TextColumn::make('service.name')
                        ->label('Layanan')
                        ->searchable()
                        ->badge()
                        ->color('info')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('courierMotorcycle.name')
                        ->label('Kurir Motor')
                        ->searchable()
                        ->placeholder('Belum ditugaskan')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('pos.name')
                        ->label('Pos')
                        ->searchable()
                        ->placeholder('Belum di pos')
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('workflow_status')
                        ->label('Status Workflow')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'pending_confirmation' => 'gray',
                            'confirmed' => 'info',
                            'picked_up' => 'warning',
                            'at_loading_post' => 'warning',
                            'in_washing' => 'primary',
                            'washing_completed' => 'success',
                            'out_for_delivery' => 'warning',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'pending_confirmation' => 'Menunggu Konfirmasi',
                            'confirmed' => 'Terkonfirmasi',
                            'picked_up' => 'Sudah Dijemput',
                            'at_loading_post' => 'Di Pos',
                            'in_washing' => 'Sedang Dicuci',
                            'washing_completed' => 'Cucian Selesai',
                            'out_for_delivery' => 'Dalam Pengiriman',
                            'delivered' => 'Terkirim',
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
                            'paid' => 'success',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'unpaid' => 'Belum Bayar',
                            'paid' => 'Lunas',
                            default => $state,
                        })
                        ->alignCenter()
                        ->searchable()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('payment_timing')
                        ->label('Waktu Bayar')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'on_pickup' => 'warning',
                            'on_delivery' => 'info',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn(string $state): string => match ($state) {
                            'on_pickup' => 'Saat Jemput',
                            'on_delivery' => 'Saat Antar',
                            default => $state,
                        })
                        ->alignCenter()
                        ->toggleable(isToggledHiddenByDefault: true),
                ]),
                ColumnGroup::make('Detail Order', [
                    TextColumn::make('weight')
                        ->label('Berat')
                        ->numeric(decimalPlaces: 2)
                        ->suffix(' Kg')
                        ->sortable()
                        ->alignCenter()
                        ->placeholder('Belum ditimbang')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('price_per_kg')
                        ->label('Harga/Kg')
                        ->money('IDR')
                        ->sortable()
                        ->alignEnd()
                        ->fontFamily('mono')
                        ->toggleable(isToggledHiddenByDefault: false),
                    TextColumn::make('total_price')
                        ->label('Total Bayar')
                        ->money('IDR')
                        ->sortable()
                        ->alignEnd()
                        ->fontFamily('mono')
                        ->weight('semibold')
                        ->toggleable(isToggledHiddenByDefault: false),
                ]),
                ColumnGroup::make('Pembayaran', [
                    TextColumn::make('paid_at')
                        ->label('Dibayar Pada')
                        ->dateTime('d M Y H:i')
                        ->placeholder('Belum dibayar')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    TextColumn::make('payment_proof_url')
                        ->label('Bukti Bayar')
                        ->placeholder('Tidak ada')
                        ->formatStateUsing(fn($state) => $state ? 'Ada' : 'Tidak ada')
                        ->badge()
                        ->color(fn($state) => $state ? 'success' : 'gray')
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
                SelectFilter::make('workflow_status')
                    ->label('Status Workflow')
                    ->native(false)
                    ->options([
                        'pending_confirmation' => 'Menunggu Konfirmasi',
                        'confirmed' => 'Terkonfirmasi',
                        'picked_up' => 'Sudah Dijemput',
                        'at_loading_post' => 'Di Pos',
                        'in_washing' => 'Sedang Dicuci',
                        'washing_completed' => 'Cucian Selesai',
                        'out_for_delivery' => 'Dalam Pengiriman',
                        'delivered' => 'Terkirim',
                        'cancelled' => 'Dibatalkan',
                    ])
                    ->placeholder('Semua status workflow'),
                SelectFilter::make('payment_status')
                    ->label('Status Pembayaran')
                    ->native(false)
                    ->options([
                        'unpaid' => 'Belum Bayar',
                        'paid' => 'Lunas',
                    ])
                    ->placeholder('Semua status bayar'),
                SelectFilter::make('payment_timing')
                    ->label('Waktu Pembayaran')
                    ->native(false)
                    ->options([
                        'on_pickup' => 'Saat Jemput',
                        'on_delivery' => 'Saat Antar',
                    ])
                    ->placeholder('Semua waktu bayar'),
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
                // Tidak ada create action karena transaksi dibuat dari landing page
            ])
            ->striped()
            ->defaultSort('order_date', direction: 'desc');
    }
}
