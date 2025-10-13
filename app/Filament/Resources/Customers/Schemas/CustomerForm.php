<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pelanggan')
                    ->description('Data identitas lengkap pelanggan termasuk nama, kontak, dan alamat')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255)
                                    ->minLength(2)
                                    ->validationAttribute('nama')
                                    ->validationMessages([
                                        'required' => 'Nama wajib diisi.',
                                        'max' => 'Nama tidak boleh lebih dari 255 karakter.',
                                        'min' => 'Nama minimal 2 karakter.',
                                    ])
                                    ->columnSpanFull(),

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

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255)
                                    ->validationAttribute('email')
                                    ->validationMessages([
                                        'email' => 'Format email tidak valid.',
                                        'max' => 'Email tidak boleh lebih dari 255 karakter.',
                                    ])
                                    ->hint('Opsional')
                                    ->placeholder('Contoh: customer@email.com'),

                                Textarea::make('address')
                                    ->label('Alamat')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->validationAttribute('alamat')
                                    ->validationMessages([
                                        'max' => 'Alamat tidak boleh lebih dari 500 karakter.',
                                    ])
                                    ->hint('Opsional')
                                    ->placeholder('Masukkan alamat lengkap pelanggan')
                                    ->columnSpanFull(),

                                ToggleButtons::make('member')
                                    ->label('Status Member')
                                    ->boolean()
                                    ->default(false)
                                    ->grouped()
                                    ->icons([
                                        true => 'solar-medal-star-bold',
                                        false => 'solar-user-bold',
                                    ])
                                    ->colors([
                                        true => 'success',
                                        false => 'gray',
                                    ])
                                    ->helperText('Apakah pelanggan ini member?')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data pelanggan')
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
