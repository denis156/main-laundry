<?php

declare(strict_types=1);

namespace App\Filament\Resources\Equipment\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;

class EquipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Peralatan')
                    ->description('Data lengkap peralatan termasuk nama, jenis, merk, dan nomor seri')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Peralatan')
                                    ->required()
                                    ->maxLength(255)
                                    ->minLength(3)
                                    ->validationAttribute('nama peralatan')
                                    ->validationMessages([
                                        'required' => 'Nama peralatan wajib diisi.',
                                        'max' => 'Nama peralatan tidak boleh lebih dari 255 karakter.',
                                        'min' => 'Nama peralatan minimal 3 karakter.',
                                    ])
                                    ->placeholder('Contoh: Mesin Cuci LG 10kg'),

                                Select::make('type')
                                    ->label('Jenis Peralatan')
                                    ->required()
                                    ->native(false)
                                    ->options([
                                        'mesin cuci' => 'Mesin Cuci',
                                        'setrika' => 'Setrika',
                                        'pengering' => 'Pengering',
                                    ])
                                    ->searchable()
                                    ->validationAttribute('jenis peralatan')
                                    ->validationMessages([
                                        'required' => 'Jenis peralatan wajib dipilih.',
                                    ])
                                    ->placeholder('Pilih jenis peralatan')
                                    ->helperText('Pilih kategori peralatan yang sesuai'),

                                TextInput::make('brand')
                                    ->label('Merk')
                                    ->maxLength(100)
                                    ->validationAttribute('merk')
                                    ->validationMessages([
                                        'max' => 'Merk tidak boleh lebih dari 100 karakter.',
                                    ])
                                    ->placeholder('Contoh: LG, Samsung, Panasonic')
                                    ->helperText('Merk peralatan (opsional)'),

                                TextInput::make('serial_number')
                                    ->label('Nomor Seri')
                                    ->maxLength(100)
                                    ->validationAttribute('nomor seri')
                                    ->validationMessages([
                                        'max' => 'Nomor seri tidak boleh lebih dari 100 karakter.',
                                    ])
                                    ->placeholder('Contoh: ABC123XYZ')
                                    ->helperText('Nomor seri peralatan (opsional)'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Informasi Pembelian')
                    ->description('Detail pembelian peralatan termasuk harga dan tanggal')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('purchase_price')
                                    ->label('Harga Beli')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->step(1000)
                                    ->validationAttribute('harga beli')
                                    ->validationMessages([
                                        'required' => 'Harga beli wajib diisi.',
                                        'numeric' => 'Harga harus berupa angka.',
                                        'min' => 'Harga minimal Rp 0.',
                                    ])
                                    ->placeholder('5000000')
                                    ->helperText('Harga pembelian dalam rupiah'),

                                DatePicker::make('purchase_date')
                                    ->label('Tanggal Beli')
                                    ->required()
                                    ->native(false)
                                    ->maxDate(now())
                                    ->validationAttribute('tanggal beli')
                                    ->validationMessages([
                                        'required' => 'Tanggal beli wajib diisi.',
                                        'date' => 'Format tanggal tidak valid.',
                                        'before_or_equal' => 'Tanggal beli tidak boleh lebih dari hari ini.',
                                    ])
                                    ->placeholder('Pilih tanggal pembelian')
                                    ->helperText('Tanggal pembelian peralatan'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Status & Kondisi')
                    ->description('Status kondisi peralatan saat ini')
                    ->collapsible()
                    ->schema([
                        Select::make('status')
                            ->label('Status Kondisi')
                            ->required()
                            ->native(false)
                            ->options([
                                'baik' => 'Baik',
                                'maintenance' => 'Dalam Perawatan',
                                'rusak' => 'Rusak',
                            ])
                            ->default('baik')
                            ->validationAttribute('status kondisi')
                            ->validationMessages([
                                'required' => 'Status kondisi wajib dipilih.',
                            ])
                            ->placeholder('Pilih status kondisi')
                            ->helperText('Pilih kondisi peralatan saat ini')
                            ->columnSpanFull(),
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Perawatan Terakhir')
                    ->description('Informasi perawatan terakhir peralatan (opsional)')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                DatePicker::make('last_maintenance_date')
                                    ->label('Tanggal Perawatan Terakhir')
                                    ->native(false)
                                    ->maxDate(now())
                                    ->validationAttribute('tanggal perawatan terakhir')
                                    ->validationMessages([
                                        'date' => 'Format tanggal tidak valid.',
                                        'before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini.',
                                    ])
                                    ->placeholder('Pilih tanggal perawatan')
                                    ->helperText('Tanggal terakhir dilakukan perawatan'),

                                TextInput::make('last_maintenance_cost')
                                    ->label('Biaya Perawatan Terakhir')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->step(1000)
                                    ->validationAttribute('biaya perawatan')
                                    ->validationMessages([
                                        'numeric' => 'Biaya harus berupa angka.',
                                        'min' => 'Biaya minimal Rp 0.',
                                    ])
                                    ->placeholder('500000')
                                    ->helperText('Biaya yang dikeluarkan untuk perawatan terakhir'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data peralatan')
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
