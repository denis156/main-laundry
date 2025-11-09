<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Schemas;

use App\Helper\WilayahHelper;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pelanggan')
                    ->description('Data identitas lengkap pelanggan termasuk nama, kontak, dan status member')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('data.name')
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

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255)
                                    ->validationAttribute('email')
                                    ->validationMessages([
                                        'email' => 'Format email tidak valid.',
                                        'max' => 'Email tidak boleh lebih dari 255 karakter.',
                                    ])
                                    ->placeholder('Contoh: customer@email.com')
                                    ->helperText('Opsional, untuk login via email'),

                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->dehydrated(fn($state) => filled($state))
                                    ->required(fn(string $operation): bool => $operation === 'create')
                                    ->minLength(6)
                                    ->maxLength(255)
                                    ->validationAttribute('password')
                                    ->validationMessages([
                                        'required' => 'Password wajib diisi.',
                                        'min' => 'Password minimal 6 karakter.',
                                        'max' => 'Password tidak boleh lebih dari 255 karakter.',
                                    ])
                                    ->placeholder('Minimal 6 karakter')
                                    ->helperText(fn(string $operation): string => $operation === 'edit' ? 'Kosongkan jika tidak ingin mengubah password' : 'Password untuk login pelanggan'),

                                ToggleButtons::make('data.member')
                                    ->label('Status Member')
                                    ->boolean()
                                    ->grouped()
                                    ->default(false)
                                    ->options([
                                        true => 'Member',
                                        false => 'Non-Member',
                                    ])
                                    ->colors([
                                        true => 'success',
                                        false => 'gray',
                                    ])
                                    ->icons([
                                        true => 'solar-star-bold',
                                        false => 'solar-star-linear',
                                    ])
                                    ->helperText('Member mendapatkan harga khusus'),

                                FileUpload::make('data.avatar_url')
                                    ->label('Foto Profil')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                    ])
                                    ->directory('avatars/customers')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->helperText('Maksimal 2MB. Rasio 1:1 direkomendasikan')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Alamat Pelanggan')
                    ->description('Pelanggan dapat memiliki beberapa alamat. Tandai salah satu sebagai alamat default')
                    ->collapsible()
                    ->schema([
                        Repeater::make('data.addresses')
                            ->label('Daftar Alamat')
                            ->schema([
                                Grid::make([
                                    'default' => 1,
                                    'sm' => 2,
                                ])
                                    ->schema([
                                        TextInput::make('type')
                                            ->label('Tipe Alamat')
                                            ->required()
                                            ->maxLength(50)
                                            ->default('Rumah')
                                            ->placeholder('Contoh: Rumah, Kantor, Kos, dll')
                                            ->helperText('Bisa diisi bebas sesuai kebutuhan'),

                                        ToggleButtons::make('is_default')
                                            ->label('Alamat Default')
                                            ->boolean()
                                            ->grouped()
                                            ->default(false)
                                            ->options([
                                                true => 'Ya, Alamat Default',
                                                false => 'Bukan Default',
                                            ])
                                            ->colors([
                                                true => 'success',
                                                false => 'gray',
                                            ])
                                            ->icons([
                                                true => 'solar-star-bold',
                                                false => 'solar-star-linear',
                                            ]),

                                        Select::make('district_code')
                                            ->label('Kecamatan')
                                            ->options(function () {
                                                $districts = WilayahHelper::getKendariDistricts();
                                                return collect($districts)->pluck('name', 'code')->toArray();
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                                $set('village_code', null);
                                                $set('village_name', null);
                                                if ($state) {
                                                    $districts = WilayahHelper::getKendariDistricts();
                                                    $district = collect($districts)->firstWhere('code', $state);
                                                    $districtName = $district['name'] ?? null;
                                                    $set('district_name', $districtName);

                                                    // Auto-generate full address jika detail_address sudah ada
                                                    $detailAddress = $get('detail_address');
                                                    $villageName = $get('village_name');

                                                    if ($detailAddress && $villageName && $districtName) {
                                                        $fullAddress = WilayahHelper::formatFullAddress($detailAddress, $villageName, $districtName);
                                                        $set('full_address', $fullAddress);
                                                    }
                                                }
                                            })
                                            ->placeholder('Pilih kecamatan'),

                                        Select::make('village_code')
                                            ->label('Kel/Desa')
                                            ->options(function (Get $get) {
                                                $districtCode = $get('district_code');
                                                if (!$districtCode) {
                                                    return [];
                                                }
                                                $villages = WilayahHelper::getVillagesByDistrict($districtCode);
                                                return collect($villages)->pluck('name', 'code')->toArray();
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                                if ($state) {
                                                    $districtCode = $get('district_code');
                                                    if ($districtCode) {
                                                        $villages = WilayahHelper::getVillagesByDistrict($districtCode);
                                                        $village = collect($villages)->firstWhere('code', $state);
                                                        $set('village_name', $village['name'] ?? null);

                                                        // Auto-generate full address jika detail_address sudah ada
                                                        $detailAddress = $get('detail_address');
                                                        $districtName = $get('district_name');
                                                        $villageName = $village['name'] ?? null;

                                                        if ($detailAddress && $villageName && $districtName) {
                                                            $fullAddress = WilayahHelper::formatFullAddress($detailAddress, $villageName, $districtName);
                                                            $set('full_address', $fullAddress);
                                                        }
                                                    }
                                                }
                                            })
                                            ->disabled(fn(Get $get) => !$get('district_code'))
                                            ->placeholder('Pilih kel/desa')
                                            ->helperText('Pilih kecamatan terlebih dahulu'),

                                        TextInput::make('district_name')
                                            ->hidden()
                                            ->dehydrated(),

                                        TextInput::make('village_name')
                                            ->hidden()
                                            ->dehydrated(),

                                        Textarea::make('detail_address')
                                            ->label('Detail Alamat')
                                            ->required()
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                                // Auto-generate full address
                                                $villageName = $get('village_name');
                                                $districtName = $get('district_name');

                                                if ($state && $villageName && $districtName) {
                                                    $fullAddress = WilayahHelper::formatFullAddress($state, $villageName, $districtName);
                                                    $set('full_address', $fullAddress);
                                                }
                                            })
                                            ->validationAttribute('detail alamat')
                                            ->validationMessages([
                                                'required' => 'Detail alamat wajib diisi.',
                                                'max' => 'Detail alamat tidak boleh lebih dari 500 karakter.',
                                            ])
                                            ->placeholder('Contoh: Jl. Ahmad Yani No. 45, RT 003/RW 001')
                                            ->helperText('Nama jalan, nomor rumah, RT/RW')
                                            ->columnSpanFull(),

                                        TextArea::make('full_address')
                                            ->label('Alamat Lengkap (Auto-generated)')
                                            ->disabled()
                                            ->dehydrated()
                                            ->hint('Dibuat otomatis dari data di atas')
                                            ->columnSpanFull(),
                                    ])
                            ])
                            ->addActionLabel('Tambah Alamat')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string =>
                                ($state['type'] ?? 'Alamat') .
                                ($state['is_default'] ?? false ? '(Default)' : '')
                            )
                            ->defaultItems(1)
                            ->minItems(1)
                            ->columnSpanFull(),
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
