<?php

declare(strict_types=1);

namespace App\Filament\Resources\MaterialStockHistories\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;

class MaterialStockHistoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Bahan')
                    ->description('Pilih bahan yang akan dicatat pergerakan stoknya')
                    ->collapsible()
                    ->schema([
                        Select::make('material_id')
                            ->label('Nama Bahan')
                            ->relationship('material', 'name')
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->validationAttribute('bahan')
                            ->validationMessages([
                                'required' => 'Bahan wajib dipilih.',
                            ])
                            ->placeholder('Pilih bahan')
                            ->helperText('Pilih bahan dari daftar yang tersedia')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $material = \App\Models\Material::find($state);
                                    if ($material) {
                                        $set('stock_before', $material->current_stock);
                                    }
                                } else {
                                    $set('stock_before', null);
                                }
                            })
                            ->columnSpanFull(),
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Pergerakan Stock')
                    ->description('Detail pergerakan stock masuk atau keluar')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Select::make('type')
                                    ->label('Tipe Pergerakan')
                                    ->required()
                                    ->native(false)
                                    ->options([
                                        'in' => 'Masuk',
                                        'out' => 'Keluar',
                                    ])
                                    ->validationAttribute('tipe pergerakan')
                                    ->validationMessages([
                                        'required' => 'Tipe pergerakan wajib dipilih.',
                                    ])
                                    ->placeholder('Pilih tipe pergerakan')
                                    ->helperText('Pilih apakah stock masuk atau keluar')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $stockBefore = $get('stock_before');
                                        $quantity = $get('quantity');

                                        if ($stockBefore !== null && $quantity !== null && $state) {
                                            if ($state === 'in') {
                                                $set('stock_after', $stockBefore + $quantity);
                                            } else {
                                                $set('stock_after', $stockBefore - $quantity);
                                            }
                                        }
                                    }),

                                TextInput::make('quantity')
                                    ->label('Jumlah')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.01)
                                    ->step(0.01)
                                    ->validationAttribute('jumlah')
                                    ->validationMessages([
                                        'required' => 'Jumlah wajib diisi.',
                                        'numeric' => 'Jumlah harus berupa angka.',
                                        'min' => 'Jumlah minimal 0.01.',
                                    ])
                                    ->placeholder('100')
                                    ->helperText('Jumlah yang masuk atau keluar')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $stockBefore = $get('stock_before');
                                        $type = $get('type');

                                        if ($stockBefore !== null && $state !== null && $type) {
                                            if ($type === 'in') {
                                                $set('stock_after', $stockBefore + $state);
                                            } else {
                                                $set('stock_after', $stockBefore - $state);
                                            }
                                        }
                                    }),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Stock Sebelum & Sesudah')
                    ->description('Jumlah stock sebelum dan sesudah pergerakan')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('stock_before')
                                    ->label('Stock Sebelum')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->validationAttribute('stock sebelum')
                                    ->validationMessages([
                                        'required' => 'Stock sebelum wajib diisi.',
                                        'numeric' => 'Stock harus berupa angka.',
                                        'min' => 'Stock minimal 0.',
                                    ])
                                    ->placeholder('200')
                                    ->helperText('Otomatis terisi dari stock bahan yang dipilih')
                                    ->readOnly()
                                    ->dehydrated(),

                                TextInput::make('stock_after')
                                    ->label('Stock Sesudah')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->validationAttribute('stock sesudah')
                                    ->validationMessages([
                                        'required' => 'Stock sesudah wajib diisi.',
                                        'numeric' => 'Stock harus berupa angka.',
                                        'min' => 'Stock minimal 0.',
                                    ])
                                    ->placeholder('300')
                                    ->helperText('Otomatis terhitung dari stock sebelum dan jumlah')
                                    ->readOnly()
                                    ->dehydrated(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Catatan')
                    ->description('Catatan tambahan untuk pergerakan stock ini (opsional)')
                    ->collapsible()
                    ->schema([
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(4)
                            ->maxLength(1000)
                            ->validationAttribute('catatan')
                            ->validationMessages([
                                'max' => 'Catatan tidak boleh lebih dari 1000 karakter.',
                            ])
                            ->placeholder('Contoh: Pembelian dari supplier ABC, Pemakaian untuk produksi, dll.')
                            ->helperText('Tambahkan catatan jika diperlukan (maksimal 1000 karakter)')
                            ->columnSpanFull(),
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
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
                            ]),
                    ])
                    ->aside()
                    ->columnSpanFull()
                    ->visible(fn(string $operation): bool => $operation === 'edit'),
            ])
            ->columns(1);
    }
}
