<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierMotorcycles\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class CourierMotorcycleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kurir Motor')
                    ->description('Data lengkap kurir motor termasuk identitas, kontak, kendaraan, dan penugasan pos')
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
                                    ->directory('avatars/couriers')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                    ->validationAttribute('foto profil')
                                    ->validationMessages([
                                        'image' => 'File harus berupa gambar.',
                                        'max' => 'Ukuran foto profil tidak boleh lebih dari 2MB.',
                                        'mimes' => 'Foto profil harus berformat JPEG, PNG, GIF, atau WebP.',
                                    ])
                                    ->helperText('Opsional')
                                    ->columnSpan(['default' => 1, 'sm' => 1]),

                                Fieldset::make('Akun Kurir')
                                    ->columns(['default' => 1, 'sm' => 2])
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
                                            ->placeholder('Contoh: Budi Santoso')
                                            ->columnSpanFull(),

                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->validationAttribute('email')
                                            ->validationMessages([
                                                'required' => 'Email wajib diisi.',
                                                'email' => 'Format email tidak valid.',
                                                'unique' => 'Email sudah digunakan.',
                                                'max' => 'Email tidak boleh lebih dari 255 karakter.',
                                            ])
                                            ->placeholder('Contoh: kurir@email.com'),

                                        TextInput::make('phone')
                                            ->label('Nomor Telepon')
                                            ->tel()
                                            ->required()
                                            ->maxLength(20)
                                            ->minLength(10)
                                            ->regex('/^(0)?8[1-9][0-9]{6,11}$/')
                                            ->prefix('+62')
                                            ->dehydrateStateUsing(function ($state) {
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
                                                'required' => 'Nomor telepon wajib diisi.',
                                                'max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
                                                'min' => 'Nomor telepon minimal 10 karakter.',
                                                'regex' => 'Format telepon tidak valid. Gunakan format Indonesia yang benar.',
                                            ])
                                            ->helperText('Bisa tulis dengan 08 atau langsung 8')
                                            ->placeholder('Contoh: 81234567890'),

                                        TextInput::make('vehicle_number')
                                            ->label('Nomor Kendaraan')
                                            ->required()
                                            ->maxLength(20)
                                            ->minLength(3)
                                            ->validationAttribute('nomor kendaraan')
                                            ->validationMessages([
                                                'required' => 'Nomor kendaraan wajib diisi.',
                                                'max' => 'Nomor kendaraan tidak boleh lebih dari 20 karakter.',
                                                'min' => 'Nomor kendaraan minimal 3 karakter.',
                                            ])
                                            ->placeholder('Contoh: B 1234 XYZ')
                                            ->columnSpanFull(),

                                        Select::make('assigned_pos_id')
                                            ->label('Pos Ditugaskan')
                                            ->relationship('assignedPos', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->validationAttribute('pos')
                                            ->validationMessages([
                                                'required' => 'Pos wajib dipilih.',
                                            ])
                                            ->placeholder('Pilih pos')
                                            ->helperText('Pos tempat kurir ini ditugaskan')
                                            ->columnSpanFull(),

                                        ToggleButtons::make('is_active')
                                            ->label('Status Kurir')
                                            ->inline()
                                            ->boolean()
                                            ->required()
                                            ->default(true)
                                            ->columnSpanFull(),

                                        TextInput::make('password')
                                            ->label('Password')
                                            ->password()
                                            ->revealable()
                                            ->required(fn(string $operation): bool => $operation === 'create')
                                            ->dehydrated(fn(?string $state): bool => filled($state))
                                            ->minLength(8)
                                            ->maxLength(255)
                                            ->validationAttribute('password')
                                            ->validationMessages([
                                                'required' => 'Password wajib diisi.',
                                                'min' => 'Password minimal 8 karakter.',
                                                'max' => 'Password tidak boleh lebih dari 255 karakter.',
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
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data kurir motor')
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
