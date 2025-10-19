<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pos\Schemas;

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
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('village_code', null);
                                        $set('area', null); // Reset area layanan ketika kecamatan berubah
                                        if ($state) {
                                            $wilayahService = app(WilayahService::class);
                                            $districts = $wilayahService->getKendariDistricts();
                                            $district = collect($districts)->firstWhere('code', $state);
                                            $set('district_name', $district['name'] ?? null);
                                        }
                                    })
                                    ->placeholder('Pilih kecamatan')
                                    ->helperText('Lokasi kantor pos'),

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
                                    ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                        if ($state) {
                                            $districtCode = $get('district_code');
                                            if ($districtCode) {
                                                $wilayahService = app(WilayahService::class);
                                                $villages = $wilayahService->getVillagesByDistrict($districtCode);
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
                                    ->placeholder('Contoh: Jl. Mawar No. 123, RT 001/RW 002')
                                    ->helperText('Nama jalan, nomor rumah, RT/RW kantor pos')
                                    ->columnSpanFull(),

                                Select::make('area')
                                    ->label('Area Layanan (Kelurahan)')
                                    ->options(function (Get $get) {
                                        $districtCode = $get('district_code');
                                        if (!$districtCode) {
                                            return [];
                                        }
                                        $wilayahService = app(WilayahService::class);
                                        $villages = $wilayahService->getVillagesByDistrict($districtCode);
                                        return collect($villages)->pluck('name', 'name')->toArray();
                                    })
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->disabled(fn(Get $get) => !$get('district_code'))
                                    ->placeholder('Pilih kelurahan yang dilayani')
                                    ->helperText('Pilih kecamatan terlebih dahulu, lalu pilih kelurahan yang dilayani pos ini')
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
