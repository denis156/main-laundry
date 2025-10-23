<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Schemas\Schema;
use App\Helper\InvoiceHelper;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;
use App\Helper\StatusTransactionHelper;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transaksi')
                    ->description('Data dasar transaksi termasuk nomor invoice, pelanggan, layanan, dan kurir')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('invoice_number')
                                    ->label('Nomor Invoice')
                                    ->required()
                                    ->maxLength(50)
                                    ->unique(ignoreRecord: true)
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(fn() => InvoiceHelper::generateInvoiceNumber())
                                    ->validationAttribute('nomor invoice')
                                    ->validationMessages([
                                        'required' => 'Nomor invoice wajib diisi.',
                                        'unique' => 'Nomor invoice sudah terdaftar.',
                                        'max' => 'Nomor invoice tidak boleh lebih dari 50 karakter.',
                                    ])
                                    ->hint('Otomatis')
                                    ->helperText('Nomor invoice akan di-generate otomatis')
                                    ->placeholder('INV/YYYYMMDD/XXXX')
                                    ->columnSpanFull(),

                                Select::make('customer_id')
                                    ->label('Pelanggan')
                                    ->relationship('customer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->validationAttribute('pelanggan')
                                    ->validationMessages([
                                        'required' => 'Pelanggan wajib dipilih.',
                                    ])
                                    ->placeholder('Pilih pelanggan')
                                    ->helperText('Pelanggan yang melakukan transaksi'),

                                Select::make('service_id')
                                    ->label('Layanan')
                                    ->relationship('service', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->validationAttribute('layanan')
                                    ->validationMessages([
                                        'required' => 'Layanan wajib dipilih.',
                                    ])
                                    ->placeholder('Pilih layanan')
                                    ->helperText('Jenis layanan yang dipilih'),

                                Select::make('courier_motorcycle_id')
                                    ->label('Kurir Motor')
                                    ->relationship('courierMotorcycle', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih kurir motor')
                                    ->hint('Opsional')
                                    ->helperText('Kurir motor yang menangani transaksi'),

                                Select::make('pos_id')
                                    ->label('Pos')
                                    ->relationship('pos', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih pos')
                                    ->hint('Opsional')
                                    ->helperText('Pos transit cucian'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Detail Order')
                    ->description('Informasi detail order termasuk berat, harga, dan total bayar')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 3,
                        ])
                            ->schema([
                                TextInput::make('weight')
                                    ->label('Berat')
                                    ->numeric()
                                    ->suffix('Kg')
                                    ->minValue(0.01)
                                    ->maxValue(1000)
                                    ->step(0.01)
                                    ->inputMode('decimal')
                                    ->validationAttribute('berat')
                                    ->validationMessages([
                                        'numeric' => 'Berat harus berupa angka.',
                                        'min' => 'Berat minimal 0.01 kg.',
                                        'max' => 'Berat maksimal 1000 kg.',
                                    ])
                                    ->placeholder('8.92')
                                    ->hint('Opsional'),

                                TextInput::make('price_per_kg')
                                    ->label('Harga per Kg')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->validationAttribute('harga per kg')
                                    ->validationMessages([
                                        'required' => 'Harga per kg wajib diisi.',
                                        'numeric' => 'Harga per kg harus berupa angka.',
                                        'min' => 'Harga per kg minimal Rp 0.',
                                    ])
                                    ->placeholder('7000'),

                                TextInput::make('total_price')
                                    ->label('Total Bayar')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->default(0)
                                    ->validationAttribute('total bayar')
                                    ->validationMessages([
                                        'required' => 'Total bayar wajib diisi.',
                                        'numeric' => 'Total bayar harus berupa angka.',
                                        'min' => 'Total bayar minimal Rp 0.',
                                    ])
                                    ->placeholder('35000')
                                    ->helperText('Berat × Harga/kg'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Status Workflow & Pembayaran')
                    ->description('Status workflow transaksi dan informasi pembayaran')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Select::make('workflow_status')
                                    ->label('Status Workflow')
                                    ->required()
                                    ->native(false)
                                    ->default('pending_confirmation')
                                    ->options(StatusTransactionHelper::getAllStatuses())
                                    ->validationAttribute('status workflow')
                                    ->validationMessages([
                                        'required' => 'Status workflow wajib dipilih.',
                                    ])
                                    ->placeholder('Pilih status workflow')
                                    ->helperText('Status pengerjaan order')
                                    ->columnSpanFull(),

                                ToggleButtons::make('payment_timing')
                                    ->label('Waktu Pembayaran')
                                    ->required()
                                    ->grouped()
                                    ->options([
                                        'on_pickup' => 'Saat Jemput',
                                        'on_delivery' => 'Saat Antar',
                                    ])
                                    ->colors([
                                        'on_pickup' => 'warning',
                                        'on_delivery' => 'info',
                                    ])
                                    ->icons([
                                        'on_pickup' => 'solar-box-bold',
                                        'on_delivery' => 'solar-delivery-bold',
                                    ])
                                    ->validationAttribute('waktu pembayaran')
                                    ->validationMessages([
                                        'required' => 'Waktu pembayaran wajib dipilih.',
                                    ])
                                    ->helperText('Kapan customer melakukan pembayaran'),

                                ToggleButtons::make('payment_status')
                                    ->label('Status Pembayaran')
                                    ->required()
                                    ->grouped()
                                    ->default('unpaid')
                                    ->options([
                                        'unpaid' => 'Belum Bayar',
                                        'paid' => 'Lunas',
                                    ])
                                    ->colors([
                                        'unpaid' => 'danger',
                                        'paid' => 'success',
                                    ])
                                    ->icons([
                                        'unpaid' => 'solar-close-circle-bold',
                                        'paid' => 'solar-check-circle-bold',
                                    ])
                                    ->validationAttribute('status pembayaran')
                                    ->validationMessages([
                                        'required' => 'Status pembayaran wajib dipilih.',
                                    ])
                                    ->helperText('Status pembayaran transaksi'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Jadwal')
                    ->description('Tanggal order dan estimasi penyelesaian')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 3,
                        ])
                            ->schema([
                                DateTimePicker::make('order_date')
                                    ->label('Tanggal Order')
                                    ->required()
                                    ->native(false)
                                    ->default(now())
                                    ->validationAttribute('tanggal order')
                                    ->validationMessages([
                                        'required' => 'Tanggal order wajib diisi.',
                                    ])
                                    ->helperText('Tanggal order dibuat'),

                                DateTimePicker::make('estimated_finish_date')
                                    ->label('Estimasi Selesai')
                                    ->native(false)
                                    ->placeholder('Belum ada estimasi')
                                    ->hint('Opsional')
                                    ->helperText('Perkiraan tanggal selesai'),

                                DateTimePicker::make('actual_finish_date')
                                    ->label('Selesai Aktual')
                                    ->native(false)
                                    ->placeholder('Belum selesai')
                                    ->hint('Opsional')
                                    ->helperText('Tanggal selesai sebenarnya'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Catatan')
                    ->description('Catatan tambahan untuk transaksi ini')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(4)
                            ->maxLength(1000)
                            ->validationAttribute('catatan')
                            ->validationMessages([
                                'max' => 'Catatan tidak boleh lebih dari 1000 karakter.',
                            ])
                            ->hint('Opsional')
                            ->placeholder('Tambahkan catatan khusus untuk order ini...')
                            ->helperText('Catatan khusus untuk order ini')
                            ->columnSpanFull(),
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data transaksi')
                    ->collapsible()
                    ->collapsed()
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

                Section::make('Tracking & Security')
                    ->description('Informasi tracking token dan data security untuk fraud detection')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('tracking_token')
                                    ->label('Tracking Token')
                                    ->maxLength(36)
                                    ->disabled()
                                    ->hint('Auto-generated')
                                    ->helperText('Token unik untuk tracking pesanan')
                                    ->placeholder('Akan di-generate otomatis')
                                    ->columnSpanFull(),

                                TextInput::make('customer_ip')
                                    ->label('IP Address')
                                    ->maxLength(45)
                                    ->disabled()
                                    ->hint('Auto-captured')
                                    ->helperText('IP address customer saat order')
                                    ->placeholder('Akan di-capture otomatis')
                                    ->columnSpanFull(),

                                Textarea::make('customer_user_agent')
                                    ->label('User Agent')
                                    ->rows(2)
                                    ->disabled()
                                    ->hint('Auto-captured')
                                    ->helperText('Browser user agent customer')
                                    ->placeholder('Akan di-capture otomatis')
                                    ->columnSpanFull(),

                                DateTimePicker::make('form_loaded_at')
                                    ->label('Form Dimuat Pada')
                                    ->native(false)
                                    ->disabled()
                                    ->hint('Auto-captured')
                                    ->helperText('Waktu form dimuat untuk detect bot submission')
                                    ->placeholder('Akan di-capture otomatis')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->aside()
                    ->columnSpanFull()
                    ->visible(fn(string $operation): bool => $operation === 'edit' && Auth::user()?->super_admin === true),
            ])
            ->columns(1);
    }
}
