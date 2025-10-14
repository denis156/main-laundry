<?php

declare(strict_types=1);

namespace App\Filament\Resources\Resorts\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class ResortForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Resort')
                    ->description('Data lengkap resort/pos pusat termasuk nama, lokasi, penanggung jawab, dan area layanan')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Resort')
                                    ->required()
                                    ->maxLength(255)
                                    ->minLength(3)
                                    ->validationAttribute('nama resort')
                                    ->validationMessages([
                                        'required' => 'Nama resort wajib diisi.',
                                        'max' => 'Nama resort tidak boleh lebih dari 255 karakter.',
                                        'min' => 'Nama resort minimal 3 karakter.',
                                    ])
                                    ->placeholder('Contoh: Resort Kendari Barat, Pos Pusat Laundry')
                                    ->columnSpanFull(),

                                TextInput::make('pic_name')
                                    ->label('Penanggung Jawab')
                                    ->required()
                                    ->maxLength(255)
                                    ->minLength(2)
                                    ->validationAttribute('penanggung jawab')
                                    ->validationMessages([
                                        'required' => 'Nama penanggung jawab wajib diisi.',
                                        'max' => 'Nama penanggung jawab tidak boleh lebih dari 255 karakter.',
                                        'min' => 'Nama penanggung jawab minimal 2 karakter.',
                                    ])
                                    ->placeholder('Contoh: Budi Santoso'),

                                TextInput::make('phone')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->required()
                                    ->maxLength(20)
                                    ->minLength(10)
                                    ->regex('/^(\+62|62|0)8[1-9][0-9]{6,11}$/')
                                    ->validationAttribute('telepon')
                                    ->validationMessages([
                                        'required' => 'Nomor telepon wajib diisi.',
                                        'max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
                                        'min' => 'Nomor telepon minimal 10 karakter.',
                                        'regex' => 'Format telepon tidak valid. Gunakan format Indonesia yang benar.',
                                    ])
                                    ->placeholder('Contoh: 08123456789'),

                                Textarea::make('address')
                                    ->label('Alamat Lengkap')
                                    ->required()
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->validationAttribute('alamat')
                                    ->validationMessages([
                                        'required' => 'Alamat wajib diisi.',
                                        'max' => 'Alamat tidak boleh lebih dari 500 karakter.',
                                    ])
                                    ->placeholder('Masukkan alamat lengkap resort')
                                    ->columnSpanFull(),

                                TagsInput::make('area_coverage')
                                    ->label('Area Layanan')
                                    ->placeholder('Ketik nama area lalu tekan Enter')
                                    ->helperText('Area yang dilayani oleh resort ini. Tekan Enter setelah mengetik setiap area.')
                                    ->columnSpanFull()
                                    ->visible(fn($get) => $get('is_main_post') === true),

                                ToggleButtons::make('is_active')
                                    ->label('Status Resort')
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
                                    ->validationAttribute('status resort')
                                    ->validationMessages([
                                        'required' => 'Status resort wajib dipilih.',
                                    ])
                                    ->helperText('Resort aktif akan ditampilkan dalam sistem'),

                                ToggleButtons::make('is_main_post')
                                    ->label('Tipe Lokasi')
                                    ->required()
                                    ->boolean()
                                    ->grouped()
                                    ->default(false)
                                    ->options([
                                        false => 'Resort Biasa',
                                        true => 'Pos Pusat',
                                    ])
                                    ->colors([
                                        false => 'info',
                                        true => 'warning',
                                    ])
                                    ->icons([
                                        false => 'solar-buildings-2-bold',
                                        true => 'solar-home-2-bold',
                                    ])
                                    ->validationAttribute('tipe lokasi')
                                    ->validationMessages([
                                        'required' => 'Tipe lokasi wajib dipilih.',
                                    ])
                                    ->helperText('Pos Pusat adalah tempat pencucian utama, Resort adalah cabang yang melayani area tertentu')
                                    ->live(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data resort')
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
