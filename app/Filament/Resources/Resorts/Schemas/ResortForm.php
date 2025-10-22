<?php

declare(strict_types=1);

namespace App\Filament\Resources\Resorts\Schemas;

use App\Helper\WilayahHelper;
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

class ResortForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Resort')
                    ->description('Data lengkap resort (induk) termasuk nama, lokasi, dan penanggung jawab')
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

                                Select::make('district_code')
                                    ->label('Kecamatan')
                                    ->options(function () {
                                        $districts = WilayahHelper::getKendariDistricts();
                                        return collect($districts)->pluck('name', 'code')->toArray();
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('village_code', null);
                                        if ($state) {
                                            $districts = WilayahHelper::getKendariDistricts();
                                            $district = collect($districts)->firstWhere('code', $state);
                                            $set('district_name', $district['name'] ?? null);
                                        }
                                    })
                                    ->placeholder('Pilih kecamatan')
                                    ->helperText('Lokasi kantor resort'),

                                Select::make('village_code')
                                    ->label('Kelurahan')
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
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                        if ($state) {
                                            $districtCode = $get('district_code');
                                            if ($districtCode) {
                                                $villages = WilayahHelper::getVillagesByDistrict($districtCode);
                                                $village = collect($villages)->firstWhere('code', $state);
                                                $set('village_name', $village['name'] ?? null);
                                            }
                                        }
                                    })
                                    ->disabled(fn(Get $get) => !$get('district_code'))
                                    ->placeholder('Pilih kelurahan')
                                    ->helperText('Pilih kecamatan terlebih dahulu'),

                                Textarea::make('detail_address')
                                    ->label('Detail Alamat')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->validationAttribute('detail alamat')
                                    ->validationMessages([
                                        'max' => 'Detail alamat tidak boleh lebih dari 500 karakter.',
                                    ])
                                    ->placeholder('Contoh: Jl. Ahmad Yani No. 45, RT 003/RW 001')
                                    ->helperText('Nama jalan, nomor rumah, RT/RW kantor resort')
                                    ->columnSpanFull(),

                                Select::make('area')
                                    ->label('Area Layanan (Kecamatan)')
                                    ->options(function () {
                                        $districts = WilayahHelper::getKendariDistricts();
                                        return collect($districts)->pluck('name', 'name')->toArray();
                                    })
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->placeholder('Pilih kecamatan yang dilayani')
                                    ->helperText('Pilih satu atau lebih kecamatan yang dilayani resort ini')
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
                                    ->dehydrated(false)
                                    ->hint('Dibuat otomatis dari data di atas')
                                    ->rows(2)
                                    ->columnSpanFull()
                                    ->visible(fn(string $operation): bool => $operation === 'edit'),

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
