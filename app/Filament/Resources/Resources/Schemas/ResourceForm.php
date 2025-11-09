<?php

declare(strict_types=1);

namespace App\Filament\Resources\Resources\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;

class ResourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->description('Informasi umum tentang sumber daya')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                ToggleButtons::make('type')
                                    ->label('Jenis Sumber Daya')
                                    ->options([
                                        'equipment' => 'Peralatan (Aset)',
                                        'material' => 'Material (Habis Pakai)',
                                    ])
                                    ->colors([
                                        'equipment' => 'info',
                                        'material' => 'warning',
                                    ])
                                    ->icons([
                                        'equipment' => 'solar-box-minimalistic-linear',
                                        'material' => 'solar-jar-of-pills-2-linear',
                                    ])
                                    ->grouped()
                                    ->required()
                                    ->default('equipment')
                                    ->live()
                                    ->validationAttribute('jenis')
                                    ->validationMessages([
                                        'required' => 'Jenis sumber daya wajib dipilih.',
                                    ])
                                    ->columnSpanFull(),

                                TextInput::make('name')
                                    ->label('Nama Sumber Daya')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Mesin Cuci Industrial / Detergen Premium')
                                    ->validationAttribute('nama')
                                    ->validationMessages([
                                        'required' => 'Nama sumber daya wajib diisi.',
                                        'max' => 'Nama tidak boleh lebih dari 255 karakter.',
                                    ])
                                    ->columnSpanFull(),

                                Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->inline(false)
                                    ->helperText('Nonaktifkan jika sumber daya tidak digunakan lagi')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                // EQUIPMENT SPECIFIC SECTIONS
                Section::make('Data Peralatan')
                    ->description('Informasi khusus untuk peralatan/aset')
                    ->collapsible()
                    ->visible(fn(Get $get): bool => $get('type') === 'equipment')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('data.brand')
                                    ->label('Merek/Brand')
                                    ->maxLength(100)
                                    ->placeholder('Contoh: Samsung, LG, Electrolux'),

                                TextInput::make('data.serial_number')
                                    ->label('Nomor Seri')
                                    ->maxLength(100)
                                    ->placeholder('Serial number peralatan'),

                                TextInput::make('data.purchase_price')
                                    ->label('Harga Beli')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('0'),

                                DatePicker::make('data.purchase_date')
                                    ->label('Tanggal Pembelian')
                                    ->native(false)
                                    ->displayFormat('d M Y'),

                                ToggleButtons::make('data.status')
                                    ->label('Kondisi')
                                    ->options([
                                        'baik' => 'Baik',
                                        'rusak' => 'Rusak',
                                        'maintenance' => 'Maintenance',
                                    ])
                                    ->colors([
                                        'baik' => 'success',
                                        'rusak' => 'danger',
                                        'maintenance' => 'warning',
                                    ])
                                    ->icons([
                                        'baik' => 'solar-check-circle-linear',
                                        'rusak' => 'solar-close-circle-linear',
                                        'maintenance' => 'solar-settings-linear',
                                    ])
                                    ->grouped()
                                    ->default('baik')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Maintenance Peralatan')
                    ->description('Riwayat dan jadwal maintenance')
                    ->collapsible()
                    ->visible(fn(Get $get): bool => $get('type') === 'equipment')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                DatePicker::make('data.maintenance.last_date')
                                    ->label('Tanggal Maintenance Terakhir')
                                    ->native(false)
                                    ->displayFormat('d M Y'),

                                TextInput::make('data.maintenance.last_cost')
                                    ->label('Biaya Maintenance Terakhir')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('0'),

                                DatePicker::make('data.maintenance.next_date')
                                    ->label('Tanggal Maintenance Berikutnya')
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->helperText('Jadwal maintenance selanjutnya'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                // MATERIAL SPECIFIC SECTIONS
                Section::make('Data Material')
                    ->description('Informasi khusus untuk material habis pakai')
                    ->collapsible()
                    ->visible(fn(Get $get): bool => $get('type') === 'material')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Select::make('data.unit')
                                    ->label('Satuan')
                                    ->options([
                                        'kg' => 'Kilogram (kg)',
                                        'liter' => 'Liter (L)',
                                        'pcs' => 'Pieces (pcs)',
                                        'box' => 'Box',
                                        'pack' => 'Pack',
                                    ])
                                    ->native(false)
                                    ->placeholder('Pilih satuan')
                                    ->helperText('Satuan pengukuran material')
                                    ->columnSpanFull(),

                                TextInput::make('data.stocks.initial')
                                    ->label('Stok Awal')
                                    ->numeric()
                                    ->default(0)
                                    ->placeholder('0'),

                                TextInput::make('data.stocks.current')
                                    ->label('Stok Saat Ini')
                                    ->numeric()
                                    ->default(0)
                                    ->placeholder('0'),

                                TextInput::make('data.stocks.minimum')
                                    ->label('Stok Minimum')
                                    ->numeric()
                                    ->default(0)
                                    ->placeholder('0')
                                    ->helperText('Batas minimum stok untuk alert'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Harga & Supplier Material')
                    ->description('Informasi harga dan supplier')
                    ->collapsible()
                    ->visible(fn(Get $get): bool => $get('type') === 'material')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('data.pricing.price_per_unit')
                                    ->label('Harga Per Satuan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('0'),

                                TextInput::make('data.pricing.supplier')
                                    ->label('Nama Supplier')
                                    ->maxLength(255)
                                    ->placeholder('Nama pemasok material'),

                                DatePicker::make('data.expired_date')
                                    ->label('Tanggal Kadaluarsa')
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->helperText('Opsional - untuk material yang memiliki masa expired')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Catatan')
                    ->description('Informasi tambahan tentang sumber daya')
                    ->collapsible()
                    ->schema([
                        Textarea::make('data.notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Catatan tambahan tentang sumber daya ini...')
                            ->helperText('Opsional - Informasi tambahan')
                            ->columnSpanFull(),
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data sumber daya')
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
