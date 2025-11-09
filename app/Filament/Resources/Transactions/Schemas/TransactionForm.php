<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;
use App\Helper\StatusTransactionHelper;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pelanggan')
                    ->description('Data pelanggan dan lokasi pengambilan')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('invoice_number')
                                    ->label('Nomor Invoice')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->default(fn() => \App\Helper\InvoiceHelper::generateInvoiceNumber())
                                    ->disabled(fn(string $operation): bool => $operation === 'create')
                                    ->dehydrated()
                                    ->placeholder('Auto-generate')
                                    ->helperText(
                                        fn(string $operation): string =>
                                        $operation === 'create'
                                            ? 'Nomor invoice akan digenerate otomatis (Format: INV/YYYYMMDD/XXXX)'
                                            : 'Nomor invoice unik untuk transaksi ini'
                                    )
                                    ->validationAttribute('nomor invoice')
                                    ->validationMessages([
                                        'required' => 'Nomor invoice wajib diisi.',
                                        'unique' => 'Nomor invoice sudah digunakan.',
                                        'max' => 'Nomor invoice maksimal :max karakter.',
                                    ])
                                    ->columnSpanFull(),

                                Select::make('customer_id')
                                    ->label('Pelanggan')
                                    ->relationship('customer', 'phone')
                                    ->getOptionLabelFromRecordUsing(
                                        fn($record) => ($record->data['name'] ?? 'Tanpa Nama') . ' - ' . $record->phone
                                    )
                                    ->searchable(['phone'])
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->placeholder('Pilih pelanggan')
                                    ->validationAttribute('pelanggan')
                                    ->validationMessages([
                                        'required' => 'Pelanggan wajib dipilih.',
                                    ])
                                    ->columnSpanFull(),

                                Select::make('courier_id')
                                    ->label('Kurir')
                                    ->relationship('courier', 'email')
                                    ->getOptionLabelFromRecordUsing(
                                        fn($record) => ($record->data['name'] ?? 'Tanpa Nama') . ' - ' . $record->email
                                    )
                                    ->searchable(['email'])
                                    ->preload()
                                    ->native(false)
                                    ->placeholder('Pilih kurir')
                                    ->helperText('Kurir yang bertugas mengambil dan mengantar')
                                    ->live()
                                    ->afterStateUpdated(function ($set, $state) {
                                        // Auto-fill location from courier's assigned location
                                        if ($state) {
                                            $courier = \App\Models\Courier::find($state);
                                            $set('location_id', $courier?->assigned_location_id);
                                        }
                                    }),

                                Select::make('location_id')
                                    ->label('Lokasi (POS/Resort)')
                                    ->relationship('location', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->placeholder('Auto dari kurir')
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Lokasi otomatis terisi dari lokasi penugasan kurir'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Item Transaksi')
                    ->description('Daftar layanan dan pakaian yang dicuci')
                    ->collapsible()
                    ->schema([
                        Repeater::make('data.items')
                            ->label('Daftar Item Layanan')
                            ->schema([
                                Select::make('service_id')
                                    ->label('Layanan')
                                    ->options(
                                        fn() => \App\Models\Service::query()
                                            ->where('is_active', true)
                                            ->pluck('name', 'id')
                                            ->toArray()
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->placeholder('Pilih layanan')
                                    ->live()
                                    ->afterStateUpdated(function ($set, $state, Get $get) {
                                        // Auto-fill service_name and pricing_unit when service selected
                                        if ($state) {
                                            $service = \App\Models\Service::find($state);
                                            $set('service_name', $service?->name);
                                            $set('pricing_unit', $service?->data['pricing']['unit'] ?? 'per_kg');
                                            $set('price_per_kg', $service?->data['pricing']['price_per_kg'] ?? null);
                                            $set('price_per_item', $service?->data['pricing']['price_per_item'] ?? null);

                                            // Reset fields hanya jika bukan saat load (tidak ada existing data)
                                            if (!$get('../../id')) {
                                                $set('clothing_items', []);
                                                $set('quantity', null);
                                                $set('subtotal', 0);
                                            }
                                        }
                                    })
                                    ->afterStateHydrated(function ($set, $state, $get) {
                                        // Auto-fill pricing_unit saat load existing data
                                        if ($state && !$get('pricing_unit')) {
                                            $service = \App\Models\Service::find($state);
                                            if ($service) {
                                                $set('service_name', $service?->name);
                                                $set('pricing_unit', $service->data['pricing']['unit'] ?? 'per_kg');
                                                $set('price_per_kg', $service->data['pricing']['price_per_kg'] ?? null);
                                                $set('price_per_item', $service->data['pricing']['price_per_item'] ?? null);
                                            }
                                        }
                                    })
                                    ->columnSpanFull(),

                                TextInput::make('service_name')
                                    ->hidden()
                                    ->dehydrated()
                                    ->live(),

                                TextInput::make('pricing_unit')
                                    ->hidden()
                                    ->dehydrated()
                                    ->live(),

                                TextInput::make('price_per_kg')
                                    ->hidden()
                                    ->dehydrated()
                                    ->live(),

                                TextInput::make('price_per_item')
                                    ->hidden()
                                    ->dehydrated()
                                    ->live(),

                                // Nested Repeater untuk Per KG (detail jenis pakaian)
                                Repeater::make('clothing_items')
                                    ->label('Detail Jenis Pakaian')
                                    ->visible(fn(Get $get) => $get('pricing_unit') === 'per_kg')
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'sm' => 2,
                                        ])
                                            ->schema([
                                                Select::make('clothing_type_id')
                                                    ->label('Jenis Pakaian')
                                                    ->options(
                                                        fn() => \App\Models\ClothingType::query()
                                                            ->where('is_active', true)
                                                            ->orderBy('name')
                                                            ->pluck('name', 'id')
                                                            ->toArray()
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->native(false)
                                                    ->placeholder('Pilih jenis')
                                                    ->live()
                                                    ->afterStateUpdated(function ($set, $state) {
                                                        if ($state) {
                                                            $clothingType = \App\Models\ClothingType::find($state);
                                                            $set('clothing_type_name', $clothingType?->name);
                                                        }
                                                    })
                                                    ->afterStateHydrated(function ($set, $state, $get) {
                                                        // Auto-fill clothing_type_name saat load existing data
                                                        if ($state && !$get('clothing_type_name')) {
                                                            $clothingType = \App\Models\ClothingType::find($state);
                                                            if ($clothingType) {
                                                                $set('clothing_type_name', $clothingType->name);
                                                            }
                                                        }
                                                    }),

                                                TextInput::make('clothing_type_name')
                                                    ->hidden()
                                                    ->dehydrated()
                                                    ->live(),

                                                TextInput::make('quantity')
                                                    ->label('Jumlah (Lembar)')
                                                    ->numeric()
                                                    ->suffix('pcs')
                                                    ->minValue(1)
                                                    ->default(1)
                                                    ->required()
                                                    ->placeholder('1')
                                                    ->live(onBlur: true),
                                            ])
                                    ])
                                    ->addActionLabel('Tambah Jenis Pakaian')
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->itemLabel(function (array $state): ?string {
                                        $clothingTypeName = $state['clothing_type_name'] ?? null;

                                        // Jika clothing_type_name masih kosong tapi clothing_type_id ada, ambil dari database
                                        if (!$clothingTypeName && isset($state['clothing_type_id'])) {
                                            $clothingType = \App\Models\ClothingType::find($state['clothing_type_id']);
                                            $clothingTypeName = $clothingType?->name;
                                        }

                                        return ($clothingTypeName ?: 'Pakaian') . ' - ' . ($state['quantity'] ?? 0) . ' pcs';
                                    })
                                    ->defaultItems(1)
                                    ->minItems(1)
                                    ->columnSpanFull(),

                                TextInput::make('total_weight')
                                    ->label('Total Berat')
                                    ->numeric()
                                    ->suffix('Kg')
                                    ->step(0.5)
                                    ->minValue(0)
                                    ->required(fn(Get $get) => $get('pricing_unit') === 'per_kg')
                                    ->visible(fn(Get $get) => $get('pricing_unit') === 'per_kg')
                                    ->placeholder('0.0')
                                    ->helperText('Total berat dari semua jenis pakaian di atas')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($set, $state, Get $get) {
                                        // Auto calculate subtotal for per_kg
                                        if ($get('pricing_unit') === 'per_kg' && $state) {
                                            $pricePerKg = (float) ($get('price_per_kg') ?? 0);
                                            $subtotal = $pricePerKg * (float) $state;
                                            $set('subtotal', $subtotal);
                                        }
                                    })
                                    ->dehydrated()
                                    ->columnSpan(1),

                                // Field untuk Per Item
                                TextInput::make('quantity')
                                    ->label('Jumlah Item')
                                    ->numeric()
                                    ->suffix('Item')
                                    ->minValue(1)
                                    ->default(1)
                                    ->required(fn(Get $get) => $get('pricing_unit') === 'per_item')
                                    ->visible(fn(Get $get) => $get('pricing_unit') === 'per_item')
                                    ->placeholder('1')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($set, $state, Get $get) {
                                        // Auto calculate subtotal for per_item
                                        if ($get('pricing_unit') === 'per_item' && $state) {
                                            $pricePerItem = (float) ($get('price_per_item') ?? 0);
                                            $subtotal = $pricePerItem * (int) $state;
                                            $set('subtotal', $subtotal);
                                        }
                                    })
                                    ->columnSpan(1),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->placeholder('0')
                                    ->readOnly()
                                    ->dehydrated()
                                    ->live()
                                    ->columnSpan(1),
                            ])
                            ->addActionLabel('Tambah Layanan')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                // Debug: tampilkan service_id jika service_name kosong
                                $serviceName = $state['service_name'] ?? null;

                                // Jika service_name masih kosong tapi service_id ada, ambil dari database
                                if (!$serviceName && isset($state['service_id'])) {
                                    $service = \App\Models\Service::find($state['service_id']);
                                    $serviceName = $service?->name;
                                }

                                return ($serviceName ?: 'Item') . ' - Rp ' . number_format($state['subtotal'] ?? 0, 0, ',', '.');
                            })
                            ->defaultItems(1)
                            ->minItems(1)
                            ->columns(['default' => 1, 'sm' => 2])
                            ->columnSpanFull(),
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Pembayaran & Status')
                    ->description('Detail pembayaran dan status transaksi')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('data.pricing.total_price')
                                    ->label('Total Harga')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('0')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visible(fn(string $operation): bool => $operation === 'edit')
                                    ->helperText('Total otomatis dihitung saat save dari semua item')
                                    ->columnSpanFull(),

                                Select::make('workflow_status')
                                    ->label('Status Workflow')
                                    ->options(StatusTransactionHelper::getStatusOptions())
                                    ->native(false)
                                    ->required()
                                    ->default('pending_confirmation')
                                    ->validationAttribute('status workflow')
                                    ->validationMessages([
                                        'required' => 'Status workflow wajib dipilih.',
                                    ])
                                    ->columnSpanFull(),

                                ToggleButtons::make('data.pricing.payment_timing')
                                    ->label('Waktu Pembayaran')
                                    ->options([
                                        'on_pickup' => 'Bayar Saat Jemput',
                                        'on_delivery' => 'Bayar Saat Antar',
                                    ])
                                    ->colors([
                                        'on_pickup' => 'info',
                                        'on_delivery' => 'success',
                                    ])
                                    ->icons([
                                        'on_pickup' => 'solar-box-linear',
                                        'on_delivery' => 'solar-delivery-linear',
                                    ])
                                    ->grouped()
                                    ->default('on_delivery')
                                    ->required(),

                                ToggleButtons::make('payment_status')
                                    ->label('Status Pembayaran')
                                    ->options([
                                        'unpaid' => 'Belum Bayar',
                                        'paid' => 'Sudah Bayar',
                                    ])
                                    ->colors([
                                        'unpaid' => 'warning',
                                        'paid' => 'success',
                                    ])
                                    ->icons([
                                        'unpaid' => 'solar-clock-circle-linear',
                                        'paid' => 'solar-check-circle-bold',
                                    ])
                                    ->grouped()
                                    ->default('unpaid')
                                    ->required()
                                    ->validationAttribute('status pembayaran')
                                    ->validationMessages([
                                        'required' => 'Status pembayaran wajib dipilih.',
                                    ]),

                                Textarea::make('data.notes')
                                    ->label('Catatan')
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->placeholder('Catatan tambahan untuk transaksi ini...')
                                    ->helperText('Opsional - Informasi tambahan tentang transaksi')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data transaksi')
                    ->collapsible()
                    ->visible(fn(string $operation): bool => $operation === 'edit')
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
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}
