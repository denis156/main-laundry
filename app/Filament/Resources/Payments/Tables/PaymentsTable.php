<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Size;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\ForceDeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use App\Helper\Database\PaymentHelper;

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
                TextColumn::make('transaction.invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->weight('semibold')
                    ->copyable()
                    ->copyMessage('Invoice disalin!')
                    ->copyMessageDuration(1500)
                    ->url(fn($record) => $record->transaction ? route('filament.admin.resources.transaksi.edit', ['record' => $record->transaction_id]) : null),
                TextColumn::make('transaction.customer.phone')
                    ->label('Pelanggan')
                    ->searchable()
                    ->getStateUsing(fn($record) => $record->transaction?->customer?->data['name'] ?? '-'),
                TextColumn::make('courier.email')
                    ->label('Kurir')
                    ->searchable()
                    ->getStateUsing(fn($record) => $record->courier?->data['name'] ?? '-'),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('data.method')
                    ->label('Metode')
                    ->badge()
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        default => $state ?? 'Tunai',
                    })
                    ->color(fn(?string $state): string => match ($state) {
                        'cash' => 'success',
                        'transfer' => 'info',
                        'qris' => 'warning',
                        default => 'gray',
                    }),
                ImageColumn::make('data.proof_url')
                    ->label('Bukti')
                    ->circular()
                    ->defaultImageUrl(url('/images/no-image.png'))
                    ->toggleable(),
                TextColumn::make('data.payment_date')
                    ->label('Tanggal Bayar')
                    ->dateTime('d M Y, H:i')
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
                SelectFilter::make('data.method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer Bank',
                        'qris' => 'QRIS',
                    ])
                    ->native(false)
                    ->multiple(),
                TrashedFilter::make()
                    ->label('Status Data')
                    ->native(false),
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
            ->toolbarActions([
                CreateAction::make()
                    ->label('Buat')
                    ->button()
                    ->size(Size::Medium)
                    ->icon('solar-add-circle-linear')
                    ->tooltip('Buat pembayaran baru'),
            ])
            ->striped()
            ->defaultSort('created_at', direction: 'desc');
    }
}
