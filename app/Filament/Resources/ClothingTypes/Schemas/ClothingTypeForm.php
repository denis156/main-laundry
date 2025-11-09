<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClothingTypes\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class ClothingTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->description('Data utama jenis pakaian')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Jenis Pakaian')
                                    ->placeholder('Contoh: Kemeja, Celana, dan lain-lain')
                                    ->required()
                                    ->maxLength(255)
                                    ->minLength(2)
                                    ->columnSpanFull()
                                    ->validationAttribute('nama jenis pakaian')
                                    ->validationMessages([
                                        'required' => 'Nama jenis pakaian wajib diisi.',
                                        'max' => 'Nama jenis pakaian tidak boleh lebih dari :max karakter.',
                                        'min' => 'Nama jenis pakaian minimal :min karakter.',
                                    ]),
                                Textarea::make('data.description')
                                    ->label('Deskripsi')
                                    ->placeholder('Deskripsi singkat tentang jenis pakaian ini')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpanFull()
                                    ->helperText('Opsional - Jelaskan karakteristik jenis pakaian ini'),
                                TagsInput::make('data.care_instructions')
                                    ->label('Instruksi Perawatan')
                                    ->placeholder('Tambahkan instruksi (Enter untuk menambah)')
                                    ->helperText('Opsional - Contoh: Cuci dengan air dingin, Jangan gunakan pemutih')
                                    ->nestedRecursiveRules([
                                        'string',
                                        'max:100',
                                    ])
                                    ->columnSpanFull(),
                                ToggleButtons::make('is_active')
                                    ->label('Status Aktif')
                                    ->boolean()
                                    ->grouped()
                                    ->required()
                                    ->default(true)
                                    ->options([
                                        true => 'Aktif',
                                        false => 'Tidak Aktif',
                                    ])
                                    ->colors([
                                        true => 'success',
                                        false => 'danger',
                                    ])
                                    ->icons([
                                        true => 'solar-check-circle-bold',
                                        false => 'solar-close-circle-bold',
                                    ])
                                    ->helperText('Hanya jenis pakaian aktif yang dapat dipilih dalam transaksi')
                                    ->validationAttribute('status')
                                    ->validationMessages([
                                        'required' => 'Status wajib dipilih.',
                                    ])
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data jenis pakaian')
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
