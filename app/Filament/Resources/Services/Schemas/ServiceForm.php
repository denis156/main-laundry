<?php

declare(strict_types=1);

namespace App\Filament\Resources\Services\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Layanan')
                    ->description('Data lengkap layanan laundry termasuk nama, harga, durasi pengerjaan, dan status')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Layanan')
                                    ->required()
                                    ->maxLength(255)
                                    ->minLength(3)
                                    ->validationAttribute('nama layanan')
                                    ->validationMessages([
                                        'required' => 'Nama layanan wajib diisi.',
                                        'max' => 'Nama layanan tidak boleh lebih dari 255 karakter.',
                                        'min' => 'Nama layanan minimal 3 karakter.',
                                    ])
                                    ->placeholder('Contoh: Cuci Kering, Cuci Setrika, Express')
                                    ->columnSpanFull(),

                                TextInput::make('price_per_kg')
                                    ->label('Harga per Kg')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->maxValue(1000000)
                                    ->step(500)
                                    ->validationAttribute('harga per kg')
                                    ->validationMessages([
                                        'required' => 'Harga per kg wajib diisi.',
                                        'numeric' => 'Harga harus berupa angka.',
                                        'min' => 'Harga minimal Rp 0.',
                                        'max' => 'Harga maksimal Rp 1.000.000.',
                                    ])
                                    ->placeholder('5000')
                                    ->helperText('Masukkan harga dalam rupiah'),

                                TextInput::make('duration_days')
                                    ->label('Durasi Pengerjaan')
                                    ->required()
                                    ->numeric()
                                    ->suffix('Hari')
                                    ->minValue(1)
                                    ->maxValue(30)
                                    ->step(1)
                                    ->validationAttribute('durasi')
                                    ->validationMessages([
                                        'required' => 'Durasi pengerjaan wajib diisi.',
                                        'numeric' => 'Durasi harus berupa angka.',
                                        'min' => 'Durasi minimal 1 hari.',
                                        'max' => 'Durasi maksimal 30 hari.',
                                    ])
                                    ->placeholder('3')
                                    ->helperText('Estimasi waktu pengerjaan dalam hari'),

                                ToggleButtons::make('is_active')
                                    ->label('Status Layanan')
                                    ->required()
                                    ->boolean()
                                    ->grouped()
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
                                    ->validationAttribute('status layanan')
                                    ->validationMessages([
                                        'required' => 'Status layanan wajib dipilih.',
                                    ])
                                    ->helperText('Layanan aktif akan ditampilkan di sistem kasir')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data layanan')
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
