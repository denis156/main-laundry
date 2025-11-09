<?php

declare(strict_types=1);

namespace App\Filament\Resources\Locations\Schemas;

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

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Lokasi')
                    ->description('Data lengkap resort atau pos termasuk nama, lokasi, resort induk, dan area layanan')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Select::make('type')
                                    ->label('Tipe Lokasi')
                                    ->options([
                                        'resort' => 'Resort',
                                        'pos' => 'POS',
                                    ])
                                    ->native(false)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        // Reset parent_id dan coverage_area ketika tipe berubah
                                        if ($state === 'resort') {
                                            $set('parent_id', null);
                                        }
                                        $set('data.coverage_area', null);
                                    })
                                    ->validationAttribute('tipe lokasi')
                                    ->validationMessages([
                                        'required' => 'Tipe lokasi wajib dipilih.',
                                    ])
                                    ->helperText('Resort: cabang utama yang memproses laundry. POS: transit yang menerima dan mengantar laundry')
                                    ->columnSpanFull(),

                                Select::make('parent_id')
                                    ->label('Resort Induk')
                                    ->relationship('parent', 'name', function ($query) {
                                        return $query->where('type', 'resort');
                                    })
                                    ->searchable()
                                    ->searchingMessage('Mencari resort...')
                                    ->noSearchResultsMessage('Tidak ada resort yang cocok.')
                                    ->preload()
                                    ->native(false)
                                    ->placeholder('Pilih resort (opsional)')
                                    ->hint('Opsional')
                                    ->helperText('POS dapat berdiri sendiri atau terkait dengan resort induk')
                                    ->visible(fn(Get $get): bool => $get('type') === 'pos')
                                    ->columnSpanFull(),

                                TextInput::make('name')
                                    ->label(fn(Get $get): string => $get('type') === 'resort' ? 'Nama Resort' : 'Nama POS')
                                    ->required()
                                    ->maxLength(255)
                                    ->minLength(3)
                                    ->validationAttribute('nama lokasi')
                                    ->validationMessages([
                                        'required' => 'Nama lokasi wajib diisi.',
                                        'max' => 'Nama lokasi tidak boleh lebih dari 255 karakter.',
                                        'min' => 'Nama lokasi minimal 3 karakter.',
                                    ])
                                    ->placeholder(fn(Get $get): string => $get('type') === 'resort' ? 'Contoh: Resort Kendari Barat' : 'Contoh: POS Mandonga')
                                    ->columnSpanFull(),

                                TextInput::make('data.contact.pic_name')
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

                                TextInput::make('data.contact.phone')
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

                                Select::make('data.location.district_code')
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
                                        $set('data.location.village_code', null);
                                        $set('data.location.village_name', null);
                                        $set('data.coverage_area', null); // Reset coverage area ketika kecamatan berubah
                                        if ($state) {
                                            $districts = WilayahHelper::getKendariDistricts();
                                            $district = collect($districts)->firstWhere('code', $state);
                                            $districtName = $district['name'] ?? null;
                                            $set('data.location.district_name', $districtName);

                                            // Auto-generate alamat lengkap jika detail_address sudah ada
                                            $detailAddress = $get('data.location.detail_address');
                                            $villageName = $get('data.location.village_name');

                                            if ($detailAddress && $villageName && $districtName) {
                                                $fullAddress = WilayahHelper::formatFullAddress($detailAddress, $villageName, $districtName);
                                                $set('data.location.address', $fullAddress);
                                            }
                                        }
                                    })
                                    ->placeholder('Pilih kecamatan')
                                    ->helperText(fn(Get $get): string => $get('type') === 'resort' ? 'Lokasi kantor resort' : 'Lokasi kantor POS'),

                                Select::make('data.location.village_code')
                                    ->label('Kel/Desa')
                                    ->options(function (Get $get) {
                                        $districtCode = $get('data.location.district_code');
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
                                            $districtCode = $get('data.location.district_code');
                                            if ($districtCode) {
                                                $villages = WilayahHelper::getVillagesByDistrict($districtCode);
                                                $village = collect($villages)->firstWhere('code', $state);
                                                $set('data.location.village_name', $village['name'] ?? null);

                                                // Auto-generate alamat lengkap jika detail_address sudah ada
                                                $detailAddress = $get('data.location.detail_address');
                                                $districtName = $get('data.location.district_name');
                                                $villageName = $village['name'] ?? null;

                                                if ($detailAddress && $villageName && $districtName) {
                                                    $fullAddress = WilayahHelper::formatFullAddress($detailAddress, $villageName, $districtName);
                                                    $set('data.location.address', $fullAddress);
                                                }
                                            }
                                        }
                                    })
                                    ->disabled(fn(Get $get) => !$get('data.location.district_code'))
                                    ->placeholder('Pilih kel/desa')
                                    ->helperText('Pilih kecamatan terlebih dahulu'),

                                Textarea::make('data.location.detail_address')
                                    ->label('Detail Alamat')
                                    ->required()
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                        // Auto-generate alamat lengkap
                                        $villageName = $get('data.location.village_name');
                                        $districtName = $get('data.location.district_name');

                                        if ($state && $villageName && $districtName) {
                                            $fullAddress = WilayahHelper::formatFullAddress($state, $villageName, $districtName);
                                            $set('data.location.address', $fullAddress);
                                        }
                                    })
                                    ->validationAttribute('detail alamat')
                                    ->validationMessages([
                                        'required' => 'Detail alamat wajib diisi.',
                                        'max' => 'Detail alamat tidak boleh lebih dari 500 karakter.',
                                    ])
                                    ->placeholder(fn(Get $get): string => $get('type') === 'resort' ? 'Contoh: Jl. Ahmad Yani No. 45, RT 003/RW 001' : 'Contoh: Jl. Mawar No. 123, RT 001/RW 002')
                                    ->helperText(fn(Get $get): string => $get('type') === 'resort' ? 'Nama jalan, nomor rumah, RT/RW kantor resort' : 'Nama jalan, nomor rumah, RT/RW kantor POS')
                                    ->columnSpanFull(),

                                Select::make('data.coverage_area')
                                    ->label(fn(Get $get): string => $get('type') === 'resort' ? 'Area Layanan (Kecamatan)' : 'Area Layanan (Kel/Desa)')
                                    ->required()
                                    ->options(function (Get $get) {
                                        $type = $get('type');

                                        if ($type === 'resort') {
                                            // Untuk resort: tampilkan semua kecamatan di Kendari
                                            $districts = WilayahHelper::getKendariDistricts();
                                            return collect($districts)->pluck('name', 'name')->toArray();
                                        } else {
                                            // Untuk POS: tampilkan kelurahan dari kecamatan yang dipilih
                                            $districtCode = $get('data.location.district_code');
                                            if (!$districtCode) {
                                                return [];
                                            }
                                            $villages = WilayahHelper::getVillagesByDistrict($districtCode);
                                            return collect($villages)->pluck('name', 'name')->toArray();
                                        }
                                    })
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->disabled(fn(Get $get) => $get('type') === 'pos' && !$get('data.location.district_code'))
                                    ->placeholder(fn(Get $get): string => $get('type') === 'resort' ? 'Pilih kecamatan yang dilayani' : 'Pilih kel/desa yang dilayani')
                                    ->validationAttribute('area layanan')
                                    ->validationMessages([
                                        'required' => 'Area layanan wajib dipilih minimal satu.',
                                    ])
                                    ->helperText(fn(Get $get): string => $get('type') === 'resort'
                                        ? 'Pilih satu atau lebih kecamatan yang dilayani resort ini'
                                        : 'Pilih kecamatan terlebih dahulu, lalu pilih kel/desa yang dilayani POS ini')
                                    ->columnSpanFull(),

                                TextInput::make('data.location.district_name')
                                    ->hidden()
                                    ->dehydrated(),

                                TextInput::make('data.location.village_name')
                                    ->hidden()
                                    ->dehydrated(),

                                TextInput::make('data.location.address')
                                    ->hidden()
                                    ->dehydrated(),

                                Textarea::make('full_address_display')
                                    ->label('Alamat Lengkap (Auto-generated)')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->hint('Dibuat otomatis dari data di atas')
                                    ->rows(2)
                                    ->formatStateUsing(fn(Get $get) => $get('data.location.address'))
                                    ->columnSpanFull(),

                                ToggleButtons::make('is_active')
                                    ->label(fn(Get $get): string => $get('type') === 'resort' ? 'Status Resort' : 'Status POS')
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
                                    ->validationAttribute('status')
                                    ->validationMessages([
                                        'required' => 'Status wajib dipilih.',
                                    ])
                                    ->helperText('Lokasi aktif akan ditampilkan dalam sistem'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data lokasi')
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
