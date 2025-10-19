<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Schemas;

use App\Services\WilayahService;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                                    ->hint('Opsional')
                                    ->placeholder('Contoh: customer@email.com'),

                                Select::make('district_code')
                                    ->label('Kecamatan')
                                    ->options(function () {
                                        $wilayahService = app(WilayahService::class);
                                        $districts = $wilayahService->getKendariDistricts();
                                        return collect($districts)->pluck('name', 'code')->toArray();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                        $set('village_code', null);
                                        // Update district_name
                                        if ($state) {
                                            $wilayahService = app(WilayahService::class);
                                            $districts = $wilayahService->getKendariDistricts();
                                            $district = collect($districts)->firstWhere('code', $state);
                                            $set('district_name', $district['name'] ?? null);
                                        }
                                    })
                                    ->placeholder('Pilih kecamatan')
                                    ->hint('Opsional')
                                    ->helperText('Pilih kecamatan di Kota Kendari'),

                                Select::make('village_code')
                                    ->label('Kelurahan')
                                    ->options(function (Get $get) {
                                        $districtCode = $get('district_code');
                                        if (!$districtCode) {
                                            return [];
                                        }
                                        $wilayahService = app(WilayahService::class);
                                        $villages = $wilayahService->getVillagesByDistrict($districtCode);
                                        return collect($villages)->pluck('name', 'code')->toArray();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        // Update village_name
                                        if ($state) {
                                            $wilayahService = app(WilayahService::class);
                                            $districtCode = request()->input('district_code');
                                            if ($districtCode) {
                                                $villages = $wilayahService->getVillagesByDistrict($districtCode);
                                                $village = collect($villages)->firstWhere('code', $state);
                                                $set('village_name', $village['name'] ?? null);
                                            }
                                        }
                                    })
                                    ->disabled(fn(Get $get) => !$get('district_code'))
                                    ->placeholder('Pilih kelurahan')
                                    ->hint('Opsional')
                                    ->helperText('Pilih kecamatan terlebih dahulu'),

                                Textarea::make('detail_address')
                                    ->label('Detail Alamat')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->validationAttribute('detail alamat')
                                    ->validationMessages([
                                        'max' => 'Detail alamat tidak boleh lebih dari 500 karakter.',
                                    ])
                                    ->hint('Opsional')
                                    ->placeholder('Contoh: Jl. Mawar No. 123, RT 001/RW 002')
                                    ->helperText('Nama jalan, nomor rumah, RT/RW, patokan, dll')
                                    ->columnSpanFull(),

                                TextInput::make('district_name')
                                    ->hidden()
                                    ->dehydrated(),

                                TextInput::make('village_name')
                                    ->hidden()
                                    ->dehydrated(),

                                Textarea::make('address')
                                    ->label('Alamat Lengkap (Auto-generated)')
                                    ->disabled()
                                    ->dehydrated()
                                    ->hint('Dibuat otomatis dari data di atas')
                                    ->columnSpanFull()
                                    ->visible(fn(string $operation): bool => $operation === 'edit'),

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
