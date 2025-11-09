<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pembayaran')
                    ->description('Detail transaksi dan kurir yang memproses pembayaran')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Select::make('transaction_id')
                                    ->label('Transaksi')
                                    ->relationship(
                                        'transaction',
                                        'invoice_number',
                                        fn($query, string $operation) => $operation === 'create'
                                            ? $query->where('payment_status', 'unpaid')
                                            : $query
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn($record) => $record->invoice_number . ' - ' . ($record->customer?->data['name'] ?? 'Tanpa Nama')
                                    )
                                    ->searchable(['invoice_number'])
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->placeholder('Pilih transaksi yang belum dibayar')
                                    ->helperText('Hanya menampilkan transaksi dengan status belum bayar')
                                    ->validationAttribute('transaksi')
                                    ->validationMessages([
                                        'required' => 'Transaksi wajib dipilih.',
                                    ])
                                    ->live()
                                    ->afterStateUpdated(function ($set, $state) {
                                        // Auto-fill amount dan courier dari transaction
                                        if ($state) {
                                            $transaction = \App\Models\Transaction::find($state);
                                            if ($transaction) {
                                                $totalPrice = \App\Helper\Database\TransactionHelper::getTotalPrice($transaction);
                                                $set('amount', $totalPrice);
                                                $set('courier_id', $transaction->courier_id);
                                            }
                                        }
                                    })
                                    ->afterStateHydrated(function ($set, $state, $get) {
                                        // Auto-fill courier saat load existing data
                                        if ($state && !$get('courier_id')) {
                                            $transaction = \App\Models\Transaction::find($state);
                                            if ($transaction) {
                                                $set('courier_id', $transaction->courier_id);
                                            }
                                        }
                                    })
                                    ->columnSpanFull(),

                                Select::make('courier_id')
                                    ->label('Kurir')
                                    ->relationship('courier', 'email')
                                    ->getOptionLabelFromRecordUsing(
                                        fn($record) => ($record->data['name'] ?? 'Tanpa Nama') . ' - ' . $record->email
                                    )
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->disabled()
                                    ->dehydrated()
                                    ->placeholder('Auto dari transaksi')
                                    ->helperText('Kurir otomatis terisi dari kurir di transaksi')
                                    ->validationAttribute('kurir')
                                    ->validationMessages([
                                        'required' => 'Kurir wajib dipilih.',
                                    ])
                                    ->columnSpanFull(),

                                TextInput::make('amount')
                                    ->label('Jumlah Pembayaran')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->readOnly()
                                    ->placeholder('0')
                                    ->helperText('Jumlah otomatis terisi dari total pembayaran di transaksi')
                                    ->validationAttribute('jumlah pembayaran')
                                    ->validationMessages([
                                        'required' => 'Jumlah pembayaran wajib diisi.',
                                        'numeric' => 'Jumlah pembayaran harus berupa angka.',
                                    ])
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Detail Pembayaran')
                    ->description('Metode pembayaran, tanggal, dan bukti pembayaran')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                DateTimePicker::make('data.payment_date')
                                    ->label('Tanggal Pembayaran')
                                    ->native(false)
                                    ->required()
                                    ->default(now())
                                    ->seconds(false)
                                    ->displayFormat('d M Y, H:i')
                                    ->timezone('Asia/Makassar')
                                    ->validationAttribute('tanggal pembayaran')
                                    ->validationMessages([
                                        'required' => 'Tanggal pembayaran wajib diisi.',
                                    ])
                                    ->columnSpanFull(),

                                ToggleButtons::make('data.method')
                                    ->label('Metode Pembayaran')
                                    ->options([
                                        'cash' => 'Tunai',
                                        'transfer' => 'Transfer Bank',
                                        'qris' => 'QRIS',
                                    ])
                                    ->colors([
                                        'cash' => 'success',
                                        'transfer' => 'info',
                                        'qris' => 'warning',
                                    ])
                                    ->icons([
                                        'cash' => 'solar-wallet-linear',
                                        'transfer' => 'solar-card-transfer-linear',
                                        'qris' => 'solar-qr-code-linear',
                                    ])
                                    ->grouped()
                                    ->default('cash')
                                    ->required()
                                    ->validationAttribute('metode pembayaran')
                                    ->validationMessages([
                                        'required' => 'Metode pembayaran wajib dipilih.',
                                    ])
                                    ->columnSpanFull(),

                                FileUpload::make('data.proof_url')
                                    ->label('Bukti Pembayaran')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        null,
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->required()
                                    ->maxSize(5120)
                                    ->directory('payment-proofs')
                                    ->visibility('public')
                                    ->helperText('Upload foto/screenshot bukti pembayaran (Maks. 5MB)')
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/webp'])
                                    ->validationAttribute('bukti pembayaran')
                                    ->validationMessages([
                                        'required' => 'Bukti pembayaran wajib diupload.',
                                    ])
                                    ->columnSpanFull(),

                                Textarea::make('data.notes')
                                    ->label('Catatan')
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->placeholder('Catatan tambahan untuk pembayaran ini...')
                                    ->helperText('Opsional - Informasi tambahan tentang pembayaran')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data pembayaran')
                    ->collapsible()
                    ->visible(fn(string $operation): bool => $operation === 'edit')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                            'lg' => 3,
                        ])
                            ->schema([
                                DateTimePicker::make('created_at')
                                    ->label('Dibuat Pada')
                                    ->disabled()
                                    ->native(false),
                                DateTimePicker::make('updated_at')
                                    ->label('Diupdate Pada')
                                    ->disabled()
                                    ->native(false),
                                DateTimePicker::make('deleted_at')
                                    ->label('Dihapus Pada')
                                    ->placeholder('Data Aktif')
                                    ->disabled()
                                    ->native(false),
                            ]),
                    ])
                    ->aside()
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}
