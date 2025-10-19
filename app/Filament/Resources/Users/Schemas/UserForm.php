<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengguna')
                    ->description('Data identitas lengkap pengguna termasuk foto profil, email, dan kontak')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                            'lg' => 3,
                        ])
                            ->schema([
                                FileUpload::make('avatar_url')
                                    ->label('Foto Profil')
                                    ->image()
                                    ->avatar()
                                    ->directory('avatars')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->validationAttribute('foto profil')
                                    ->validationMessages([
                                        'image' => 'File harus berupa gambar.',
                                        'max' => 'Ukuran foto profil tidak boleh lebih dari 2MB.',
                                        'mimes' => 'Foto profil harus berformat JPEG, PNG, GIF, atau WebP.',
                                    ])
                                    ->hint('Opsional')
                                    ->columnSpan(['default' => 1, 'sm' => 1]),

                                Fieldset::make('Akun Pengguna')
                                    ->columns(['default' => 1, 'sm' => 2])
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama')
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

                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->validationAttribute('email')
                                            ->validationMessages([
                                                'required' => 'Email wajib diisi.',
                                                'email' => 'Format email tidak valid.',
                                                'unique' => 'Email sudah terdaftar.',
                                                'max' => 'Email tidak boleh lebih dari 255 karakter.',
                                            ]),

                                        TextInput::make('phone')
                                            ->label('Telepon')
                                            ->tel()
                                            ->maxLength(20)
                                            ->minLength(10)
                                            ->regex('/^(0)?8[1-9][0-9]{6,11}$/')
                                            ->prefix('+62')
                                            ->dehydrateStateUsing(function ($state) {
                                                if (empty($state)) {
                                                    return null;
                                                }
                                                // Hapus karakter non-numeric
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $state);
                                                // Hapus leading 0 jika ada
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $cleanPhone = substr($cleanPhone, 1);
                                                }
                                                return $cleanPhone;
                                            })
                                            ->validationAttribute('telepon')
                                            ->validationMessages([
                                                'max' => 'telepon tidak boleh lebih dari 20 karakter.',
                                                'min' => 'telepon minimal 10 karakter.',
                                                'regex' => 'Format telepon tidak valid. Gunakan format Indonesia yang benar.',
                                            ])
                                            ->helperText('Bisa tulis dengan 08 atau langsung 8')
                                            ->hint('Opsional')
                                            ->placeholder('Contoh: 81234567890'),

                                        ToggleButtons::make('super_admin')
                                            ->label('Super Admin')
                                            ->inline()
                                            ->boolean()
                                            ->required(),

                                        TextInput::make('password')
                                            ->label('Password')
                                            ->password()
                                            ->revealable()
                                            ->required(fn(string $operation): bool => $operation === 'create')
                                            ->minLength(8)
                                            ->maxLength(255)
                                            ->confirmed()
                                            ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/')
                                            ->dehydrated(fn(?string $state): bool => filled($state))
                                            ->validationAttribute('password')
                                            ->validationMessages([
                                                'required' => 'Password wajib diisi.',
                                                'min' => 'Password minimal 8 karakter.',
                                                'max' => 'Password tidak boleh lebih dari 255 karakter.',
                                                'confirmed' => 'Konfirmasi password tidak cocok.',
                                                'regex' => 'Password harus mengandung minimal 1 huruf kecil, 1 huruf besar, 1 angka, dan 1 karakter khusus.',
                                            ])
                                            ->hint(fn(string $operation): string => $operation === 'edit' ? 'Opsional' : '')
                                            ->columnSpanFull(),

                                        TextInput::make('password_confirmation')
                                            ->label('Konfirmasi Password')
                                            ->password()
                                            ->revealable()
                                            ->required(fn(string $operation): bool => $operation === 'create')
                                            ->minLength(8)
                                            ->maxLength(255)
                                            ->dehydrated(fn(?string $state): bool => filled($state))
                                            ->validationAttribute('konfirmasi password')
                                            ->validationMessages([
                                                'required' => 'Konfirmasi password wajib diisi.',
                                                'min' => 'Konfirmasi password minimal 8 karakter.',
                                                'max' => 'Konfirmasi password tidak boleh lebih dari 255 karakter.',
                                            ])
                                            ->hint(fn(string $operation): string => $operation === 'edit' ? 'Opsional' : '')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpan(['default' => 1, 'sm' => 2]),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data pengguna')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                            'lg' => 4,
                        ])
                            ->schema([
                                DateTimePicker::make('email_verified_at')
                                    ->label('Verifikasi Email')
                                    ->placeholder('Belum Diverifikasi')
                                    ->disabled()
                                    ->native(false),
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
