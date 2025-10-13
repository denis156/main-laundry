<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Schemas\Schema;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Transaksi')
                    ->description('Data dasar transaksi termasuk nomor invoice, pelanggan, dan kasir')
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
                                    ->default(fn() => app(InvoiceService::class)->generateInvoiceNumber())
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

                                Select::make('user_id')
                                    ->label('Kasir')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(fn() => Auth::id())
                                    ->validationAttribute('kasir')
                                    ->validationMessages([
                                        'required' => 'Kasir wajib dipilih.',
                                    ])
                                    ->placeholder('Pilih kasir')
                                    ->helperText('Kasir yang melayani transaksi'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Detail Order')
                    ->description('Informasi detail order termasuk berat, subtotal, dan total bayar')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                            'lg' => 3,
                        ])
                            ->schema([
                                TextInput::make('total_weight')
                                    ->label('Total Berat')
                                    ->required()
                                    ->numeric()
                                    ->suffix('Kg')
                                    ->minValue(0.1)
                                    ->maxValue(1000)
                                    ->step(0.1)
                                    ->validationAttribute('total berat')
                                    ->validationMessages([
                                        'required' => 'Total berat wajib diisi.',
                                        'numeric' => 'Total berat harus berupa angka.',
                                        'min' => 'Total berat minimal 0.1 kg.',
                                        'max' => 'Total berat maksimal 1000 kg.',
                                    ])
                                    ->placeholder('5.0')
                                    ->helperText('Total berat cucian dalam kg'),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->validationAttribute('subtotal')
                                    ->validationMessages([
                                        'required' => 'Subtotal wajib diisi.',
                                        'numeric' => 'Subtotal harus berupa angka.',
                                        'min' => 'Subtotal minimal Rp 0.',
                                    ])
                                    ->placeholder('50000')
                                    ->helperText('Subtotal sebelum diskon'),

                                TextInput::make('total_price')
                                    ->label('Total Bayar')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->validationAttribute('total bayar')
                                    ->validationMessages([
                                        'required' => 'Total bayar wajib diisi.',
                                        'numeric' => 'Total bayar harus berupa angka.',
                                        'min' => 'Total bayar minimal Rp 0.',
                                    ])
                                    ->placeholder('45000')
                                    ->helperText('Total setelah diskon'),

                                TextInput::make('paid_amount')
                                    ->label('Jumlah Terbayar')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->default(0)
                                    ->validationAttribute('jumlah terbayar')
                                    ->validationMessages([
                                        'required' => 'Jumlah terbayar wajib diisi.',
                                        'numeric' => 'Jumlah terbayar harus berupa angka.',
                                        'min' => 'Jumlah terbayar minimal Rp 0.',
                                    ])
                                    ->placeholder('45000')
                                    ->helperText('Jumlah yang sudah dibayar'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Diskon & Promo')
                    ->description('Informasi promo dan diskon yang digunakan')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Select::make('promo_id')
                                    ->label('Promo')
                                    ->relationship('promo', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->validationAttribute('promo')
                                    ->placeholder('Pilih promo (jika ada)')
                                    ->hint('Opsional')
                                    ->helperText('Promo yang sedang berlaku'),

                                TextInput::make('promo_discount_amount')
                                    ->label('Nominal Diskon Promo')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->default(0)
                                    ->validationAttribute('nominal diskon promo')
                                    ->validationMessages([
                                        'numeric' => 'Nominal diskon promo harus berupa angka.',
                                        'min' => 'Nominal diskon promo minimal Rp 0.',
                                    ])
                                    ->placeholder('5000')
                                    ->hint('Opsional')
                                    ->helperText('Nominal diskon dari promo'),

                                TextInput::make('total_discount_amount')
                                    ->label('Total Diskon')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->default(0)
                                    ->validationAttribute('total diskon')
                                    ->validationMessages([
                                        'numeric' => 'Total diskon harus berupa angka.',
                                        'min' => 'Total diskon minimal Rp 0.',
                                    ])
                                    ->placeholder('10000')
                                    ->hint('Opsional')
                                    ->helperText('Total semua diskon'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Status & Jadwal')
                    ->description('Status order, pembayaran, dan jadwal pengerjaan')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                ToggleButtons::make('status')
                                    ->label('Status Order')
                                    ->required()
                                    ->grouped()
                                    ->default('pending')
                                    ->options([
                                        'pending' => 'Menunggu',
                                        'processing' => 'Diproses',
                                        'ready' => 'Siap',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ])
                                    ->colors([
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'ready' => 'success',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                    ])
                                    ->icons([
                                        'pending' => 'solar-clock-circle-bold',
                                        'processing' => 'solar-refresh-circle-bold',
                                        'ready' => 'solar-check-circle-bold',
                                        'completed' => 'solar-verified-check-bold',
                                        'cancelled' => 'solar-close-circle-bold',
                                    ])
                                    ->validationAttribute('status order')
                                    ->validationMessages([
                                        'required' => 'Status order wajib dipilih.',
                                    ])
                                    ->helperText('Status pengerjaan order')
                                    ->columnSpanFull(),

                                ToggleButtons::make('payment_status')
                                    ->label('Status Pembayaran')
                                    ->required()
                                    ->grouped()
                                    ->default('unpaid')
                                    ->options([
                                        'unpaid' => 'Belum Bayar',
                                        'partial' => 'Cicilan',
                                        'paid' => 'Lunas',
                                    ])
                                    ->colors([
                                        'unpaid' => 'danger',
                                        'partial' => 'warning',
                                        'paid' => 'success',
                                    ])
                                    ->icons([
                                        'unpaid' => 'solar-close-circle-bold',
                                        'partial' => 'solar-clock-circle-bold',
                                        'paid' => 'solar-check-circle-bold',
                                    ])
                                    ->validationAttribute('status pembayaran')
                                    ->validationMessages([
                                        'required' => 'Status pembayaran wajib dipilih.',
                                    ])
                                    ->helperText('Status pembayaran transaksi')
                                    ->columnSpanFull(),

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
                                    ->required()
                                    ->native(false)
                                    ->validationAttribute('estimasi selesai')
                                    ->validationMessages([
                                        'required' => 'Estimasi selesai wajib diisi.',
                                    ])
                                    ->helperText('Perkiraan tanggal selesai'),

                                DateTimePicker::make('actual_finish_date')
                                    ->label('Selesai Aktual')
                                    ->native(false)
                                    ->validationAttribute('selesai aktual')
                                    ->placeholder('Belum selesai')
                                    ->hint('Opsional')
                                    ->helperText('Tanggal selesai sebenarnya')
                                    ->columnSpanFull(),
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
