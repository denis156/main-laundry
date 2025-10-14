<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DateTimePicker;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pembayaran')
                    ->description('Data lengkap pembayaran termasuk transaksi, kurir, jumlah, dan bukti pembayaran')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Select::make('transaction_id')
                                    ->label('Transaksi')
                                    ->relationship('transaction', 'invoice_number')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->invoice_number . " - " . $record->customer->name)
                                    ->validationAttribute('transaksi')
                                    ->validationMessages([
                                        'required' => 'Transaksi wajib dipilih.',
                                    ])
                                    ->placeholder('Pilih transaksi')
                                    ->helperText('Pilih transaksi yang akan dibayar'),

                                Select::make('courier_motorcycle_id')
                                    ->label('Kurir Motor')
                                    ->relationship('courierMotorcycle', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->validationAttribute('kurir motor')
                                    ->validationMessages([
                                        'required' => 'Kurir motor wajib dipilih.',
                                    ])
                                    ->placeholder('Pilih kurir motor')
                                    ->helperText('Kurir motor yang mengupload bukti pembayaran'),

                                TextInput::make('amount')
                                    ->label('Jumlah Pembayaran')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->maxValue(99999999.99)
                                    ->step(0.01)
                                    ->validationAttribute('jumlah pembayaran')
                                    ->validationMessages([
                                        'required' => 'Jumlah pembayaran wajib diisi.',
                                        'numeric' => 'Jumlah pembayaran harus berupa angka.',
                                        'min' => 'Jumlah pembayaran minimal Rp 0.',
                                        'max' => 'Jumlah pembayaran maksimal Rp 99.999.999,99.',
                                    ])
                                    ->placeholder('0')
                                    ->helperText('Masukkan jumlah pembayaran dalam Rupiah'),

                                DateTimePicker::make('payment_date')
                                    ->label('Tanggal & Waktu Pembayaran')
                                    ->required()
                                    ->native(false)
                                    ->seconds(false)
                                    ->minDate(now()->subMonths(1))
                                    ->maxDate(now())
                                    ->default(now())
                                    ->validationAttribute('tanggal pembayaran')
                                    ->validationMessages([
                                        'required' => 'Tanggal pembayaran wajib diisi.',
                                        'after_or_equal' => 'Tanggal pembayaran tidak boleh lebih dari 1 bulan yang lalu.',
                                        'before_or_equal' => 'Tanggal pembayaran tidak boleh di masa depan.',
                                    ])
                                    ->helperText('Pilih tanggal dan waktu pembayaran'),

                                FileUpload::make('payment_proof_url')
                                    ->label('Bukti Pembayaran')
                                    ->image()
                                    ->required()
                                    ->directory('payment-proofs')
                                    ->visibility('public')
                                    ->maxSize(5120)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                                    ->validationAttribute('bukti pembayaran')
                                    ->validationMessages([
                                        'required' => 'Bukti pembayaran wajib diupload.',
                                        'image' => 'File harus berupa gambar.',
                                        'max' => 'Ukuran bukti pembayaran tidak boleh lebih dari 5MB.',
                                        'mimes' => 'Bukti pembayaran harus berformat JPEG, PNG, JPG, atau WebP.',
                                    ])
                                    ->helperText('Upload screenshot bukti pembayaran (max 5MB)')
                                    ->columnSpanFull(),

                                Textarea::make('notes')
                                    ->label('Catatan')
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->validationAttribute('catatan')
                                    ->validationMessages([
                                        'max' => 'Catatan tidak boleh lebih dari 1000 karakter.',
                                    ])
                                    ->placeholder('Masukkan catatan pembayaran (opsional)')
                                    ->helperText('Opsional')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data pembayaran')
                    ->collapsible()
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
                    ->columnSpanFull()
                    ->visible(fn(string $operation): bool => $operation === 'edit'),
            ])
            ->columns(1);
    }
}
