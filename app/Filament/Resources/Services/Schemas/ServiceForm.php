<?php

declare(strict_types=1);

namespace App\Filament\Resources\Services\Schemas;

use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->description('Data dasar layanan laundry')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Layanan')
                                    ->required()
                                    ->maxLength(255)
                                    ->minLength(3)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('data.service_type', Str::slug($state)))
                                    ->validationAttribute('nama layanan')
                                    ->validationMessages([
                                        'required' => 'Nama layanan wajib diisi.',
                                        'max' => 'Nama layanan tidak boleh lebih dari 255 karakter.',
                                        'min' => 'Nama layanan minimal 3 karakter.',
                                    ])
                                    ->placeholder('Contoh: Cuci Kering, Cuci Setrika, Cuci Karpet')
                                    ->columnSpanFull(),

                                TextInput::make('data.service_type')
                                    ->label('Slug/Tipe Layanan')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(100)
                                    ->dehydrated()
                                    ->validationAttribute('slug')
                                    ->validationMessages([
                                        'required' => 'Slug wajib diisi.',
                                    ])
                                    ->helperText('Terisi otomatis dari nama layanan')
                                    ->columnSpanFull(),

                                Select::make('data.pricing.unit')
                                    ->label('Satuan Harga')
                                    ->required()
                                    ->live()
                                    ->native(false)
                                    ->default('per_kg')
                                    ->options([
                                        'per_kg' => 'Per Kilogram (Kg) - Perlu jenis pakaian & berat',
                                        'per_item' => 'Per Item/Lembar - Tidak perlu jenis pakaian',
                                    ])
                                    ->validationAttribute('satuan harga')
                                    ->validationMessages([
                                        'required' => 'Satuan harga wajib dipilih.',
                                    ])
                                    ->helperText('Pilih satuan untuk pricing layanan ini')
                                    ->columnSpanFull(),

                                TextInput::make('data.pricing.price_per_kg')
                                    ->label('Harga per Kg')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->maxValue(1000000)
                                    ->step(500)
                                    ->validationAttribute('harga per kg')
                                    ->validationMessages([
                                        'required' => 'Harga per kg wajib diisi.',
                                        'numeric' => 'Harga harus berupa angka.',
                                        'min' => 'Harga minimal Rp 0.',
                                        'max' => 'Harga maksimal Rp 1.000.000.',
                                    ])
                                    ->placeholder('5000')
                                    ->helperText('Harga untuk setiap kilogram')
                                    ->visible(fn(Get $get) => $get('data.pricing.unit') === 'per_kg'),

                                TextInput::make('data.pricing.price_per_item')
                                    ->label('Harga per Item')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->maxValue(10000000)
                                    ->step(1000)
                                    ->validationAttribute('harga per item')
                                    ->validationMessages([
                                        'required' => 'Harga per item wajib diisi.',
                                        'numeric' => 'Harga harus berupa angka.',
                                        'min' => 'Harga minimal Rp 0.',
                                        'max' => 'Harga maksimal Rp 10.000.000.',
                                    ])
                                    ->placeholder('50000')
                                    ->helperText('Harga untuk setiap item/lembar')
                                    ->visible(fn(Get $get) => $get('data.pricing.unit') === 'per_item'),

                                TextInput::make('data.duration_hours')
                                    ->label('Durasi Pengerjaan')
                                    ->required()
                                    ->numeric()
                                    ->suffix('Jam')
                                    ->minValue(1)
                                    ->maxValue(720)
                                    ->step(1)
                                    ->default(72)
                                    ->validationAttribute('durasi')
                                    ->validationMessages([
                                        'required' => 'Durasi pengerjaan wajib diisi.',
                                        'numeric' => 'Durasi harus berupa angka.',
                                        'min' => 'Durasi minimal 1 jam.',
                                        'max' => 'Durasi maksimal 720 jam.',
                                    ])
                                    ->placeholder('72')
                                    ->helperText('Estimasi waktu pengerjaan dalam jam'),

                                ToggleButtons::make('is_featured')
                                    ->label('Layanan Unggulan')
                                    ->boolean()
                                    ->grouped()
                                    ->default(false)
                                    ->options([
                                        true => 'Ya, Unggulan',
                                        false => 'Tidak',
                                    ])
                                    ->colors([
                                        true => 'warning',
                                        false => 'gray',
                                    ])
                                    ->icons([
                                        true => 'solar-star-bold',
                                        false => 'solar-star-linear',
                                    ])
                                    ->helperText('Layanan unggulan akan ditampilkan lebih menonjol'),

                                TextInput::make('sort_order')
                                    ->label('Urutan Tampilan')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(1000)
                                    ->step(1)
                                    ->default(0)
                                    ->validationAttribute('urutan')
                                    ->validationMessages([
                                        'required' => 'Urutan wajib diisi.',
                                        'numeric' => 'Urutan harus berupa angka.',
                                    ])
                                    ->placeholder('0')
                                    ->helperText('Semakin kecil angka, semakin depan urutannya'),

                                ToggleButtons::make('is_active')
                                    ->label('Status Layanan')
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
                                    ->validationAttribute('status layanan')
                                    ->validationMessages([
                                        'required' => 'Status layanan wajib dipilih.',
                                    ])
                                    ->helperText('Layanan aktif akan ditampilkan di sistem')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Detail Layanan')
                    ->description('Fitur, termasuk, dan batasan layanan')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                        ])
                            ->schema([
                                TagsInput::make('data.features')
                                    ->label('Fitur Layanan')
                                    ->placeholder('Tekan Enter untuk menambah fitur')
                                    ->helperText('Contoh: Pewangi Premium, Pengeringan Maksimal, dll')
                                    ->columnSpanFull(),

                                TagsInput::make('data.includes')
                                    ->label('Yang Termasuk')
                                    ->placeholder('Tekan Enter untuk menambah item')
                                    ->helperText('Apa saja yang termasuk dalam layanan ini')
                                    ->columnSpanFull(),

                                TagsInput::make('data.restrictions')
                                    ->label('Batasan')
                                    ->placeholder('Tekan Enter untuk menambah batasan')
                                    ->helperText('Contoh: Tidak untuk pakaian berbahan sutra, dll')
                                    ->columnSpanFull(),

                                TagsInput::make('data.materials_used')
                                    ->label('Material yang Digunakan')
                                    ->placeholder('Tekan Enter untuk menambah material')
                                    ->helperText('Contoh: Deterjen Premium, Pewangi Khusus, dll')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data layanan')
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
