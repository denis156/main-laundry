<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pos\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class PosForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pos')
                    ->description('Data lengkap pos termasuk nama, lokasi, resort induk, dan area layanan')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Select::make('resort_id')
                                    ->label('Resort Induk')
                                    ->relationship('resort', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->placeholder('Pilih resort (opsional)')
                                    ->hint('Opsional')
                                    ->helperText('Pos dapat berdiri sendiri atau terkait dengan resort induk'),

                                TextInput::make('name')
                                    ->label('Nama Pos')
                                    ->required()
                                    ->maxLength(255)
                                    ->minLength(3)
                                    ->validationAttribute('nama pos')
                                    ->validationMessages([
                                        'required' => 'Nama pos wajib diisi.',
                                        'max' => 'Nama pos tidak boleh lebih dari 255 karakter.',
                                        'min' => 'Nama pos minimal 3 karakter.',
                                    ])
                                    ->placeholder('Contoh: Pos Kendari Barat, Pos Abeli'),

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

                                TextInput::make('area')
                                    ->label('Area Layanan')
                                    ->maxLength(255)
                                    ->validationAttribute('area layanan')
                                    ->validationMessages([
                                        'max' => 'Area layanan tidak boleh lebih dari 255 karakter.',
                                    ])
                                    ->placeholder('Contoh: Kadia, Abeli, Kambu')
                                    ->hint('Opsional')
                                    ->helperText('Area yang dilayani oleh pos ini')
                                    ->columnSpanFull(),

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
                                    ->placeholder('Masukkan alamat lengkap pos')
                                    ->columnSpanFull(),

                                ToggleButtons::make('is_active')
                                    ->label('Status Pos')
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
                                    ->validationAttribute('status pos')
                                    ->validationMessages([
                                        'required' => 'Status pos wajib dipilih.',
                                    ])
                                    ->helperText('Pos aktif akan ditampilkan dalam sistem'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data pos')
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
