<?php

declare(strict_types=1);

namespace App\Filament\Resources\Materials\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;

class MaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Bahan')
                    ->description('Data lengkap bahan termasuk nama, jenis, satuan, dan stok')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Bahan')
                                    ->required()
                                    ->maxLength(255)
                                    ->minLength(3)
                                    ->validationAttribute('nama bahan')
                                    ->validationMessages([
                                        'required' => 'Nama bahan wajib diisi.',
                                        'max' => 'Nama bahan tidak boleh lebih dari 255 karakter.',
                                        'min' => 'Nama bahan minimal 3 karakter.',
                                    ])
                                    ->placeholder('Contoh: Detergen Bubuk Premium, Pewangi Lavender')
                                    ->columnSpanFull(),

                                Select::make('type')
                                    ->label('Jenis Bahan')
                                    ->required()
                                    ->native(false)
                                    ->options([
                                        'detergen' => 'Detergen',
                                        'pewangi' => 'Pewangi',
                                        'softener' => 'Softener',
                                        'pemutih' => 'Pemutih',
                                        'plastik' => 'Plastik',
                                        'aksesoris' => 'Aksesoris',
                                    ])
                                    ->searchable()
                                    ->validationAttribute('jenis bahan')
                                    ->validationMessages([
                                        'required' => 'Jenis bahan wajib dipilih.',
                                    ])
                                    ->placeholder('Pilih jenis bahan')
                                    ->helperText('Pilih kategori bahan yang sesuai'),

                                TextInput::make('unit')
                                    ->label('Satuan')
                                    ->required()
                                    ->maxLength(50)
                                    ->validationAttribute('satuan')
                                    ->validationMessages([
                                        'required' => 'Satuan wajib diisi.',
                                        'max' => 'Satuan tidak boleh lebih dari 50 karakter.',
                                    ])
                                    ->placeholder('Contoh: kg, liter, pcs')
                                    ->helperText('Satuan pengukuran bahan (kg, liter, pcs, dll)'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Manajemen Stok')
                    ->description('Kelola stok bahan termasuk stok awal, stok saat ini, dan stok minimum')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 3,
                        ])
                            ->schema([
                                TextInput::make('initial_stock')
                                    ->label('Stok Awal')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->validationAttribute('stok awal')
                                    ->validationMessages([
                                        'required' => 'Stok awal wajib diisi.',
                                        'numeric' => 'Stok harus berupa angka.',
                                        'min' => 'Stok minimal 0.',
                                    ])
                                    ->placeholder('100')
                                    ->helperText('Jumlah stok pertama kali'),

                                TextInput::make('current_stock')
                                    ->label('Stok Sekarang')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->validationAttribute('stok sekarang')
                                    ->validationMessages([
                                        'required' => 'Stok sekarang wajib diisi.',
                                        'numeric' => 'Stok harus berupa angka.',
                                        'min' => 'Stok minimal 0.',
                                    ])
                                    ->placeholder('75')
                                    ->helperText('Jumlah stok saat ini'),

                                TextInput::make('minimum_stock')
                                    ->label('Stok Minimum')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->validationAttribute('stok minimum')
                                    ->validationMessages([
                                        'numeric' => 'Stok harus berupa angka.',
                                        'min' => 'Stok minimal 0.',
                                    ])
                                    ->placeholder('20')
                                    ->helperText('Batas minimum untuk alert restok'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Harga & Kadaluarsa')
                    ->description('Informasi harga per satuan dan tanggal kadaluarsa bahan')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('price_per_unit')
                                    ->label('Harga per Satuan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->step(100)
                                    ->validationAttribute('harga per satuan')
                                    ->validationMessages([
                                        'numeric' => 'Harga harus berupa angka.',
                                        'min' => 'Harga minimal Rp 0.',
                                    ])
                                    ->placeholder('50000')
                                    ->helperText('Harga per satuan dalam rupiah'),

                                DatePicker::make('expired_date')
                                    ->label('Tanggal Kadaluarsa')
                                    ->native(false)
                                    ->minDate(now())
                                    ->validationAttribute('tanggal kadaluarsa')
                                    ->validationMessages([
                                        'date' => 'Format tanggal tidak valid.',
                                        'after_or_equal' => 'Tanggal kadaluarsa harus hari ini atau setelahnya.',
                                    ])
                                    ->placeholder('Pilih tanggal kadaluarsa')
                                    ->helperText('Kosongkan jika tidak ada tanggal kadaluarsa'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data bahan')
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
