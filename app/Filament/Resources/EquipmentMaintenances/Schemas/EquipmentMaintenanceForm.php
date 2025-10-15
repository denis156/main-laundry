<?php

declare(strict_types=1);

namespace App\Filament\Resources\EquipmentMaintenances\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;

class EquipmentMaintenanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Peralatan')
                    ->description('Pilih peralatan yang akan dilakukan perawatan')
                    ->collapsible()
                    ->schema([
                        Select::make('equipment_id')
                            ->label('Nama Peralatan')
                            ->relationship('equipment', 'name')
                            ->required()
                            ->native(false)
                            ->searchable()
                            ->preload()
                            ->validationAttribute('peralatan')
                            ->validationMessages([
                                'required' => 'Peralatan wajib dipilih.',
                            ])
                            ->placeholder('Pilih peralatan')
                            ->helperText('Pilih peralatan dari daftar yang tersedia')
                            ->columnSpanFull(),
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Detail Perawatan')
                    ->description('Informasi tanggal dan biaya perawatan')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                DatePicker::make('maintenance_date')
                                    ->label('Tanggal Perawatan')
                                    ->required()
                                    ->native(false)
                                    ->maxDate(now())
                                    ->validationAttribute('tanggal perawatan')
                                    ->validationMessages([
                                        'required' => 'Tanggal perawatan wajib diisi.',
                                        'date' => 'Format tanggal tidak valid.',
                                        'before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini.',
                                    ])
                                    ->placeholder('Pilih tanggal perawatan')
                                    ->helperText('Tanggal dilakukan perawatan'),

                                TextInput::make('cost')
                                    ->label('Biaya Perawatan')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->minValue(0)
                                    ->step(1000)
                                    ->validationAttribute('biaya perawatan')
                                    ->validationMessages([
                                        'required' => 'Biaya perawatan wajib diisi.',
                                        'numeric' => 'Biaya harus berupa angka.',
                                        'min' => 'Biaya minimal Rp 0.',
                                    ])
                                    ->placeholder('500000')
                                    ->helperText('Biaya yang dikeluarkan untuk perawatan'),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Deskripsi Perawatan')
                    ->description('Detail pekerjaan yang dilakukan dan penanggung jawab')
                    ->collapsible()
                    ->schema([
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->maxLength(1000)
                            ->validationAttribute('deskripsi')
                            ->validationMessages([
                                'max' => 'Deskripsi tidak boleh lebih dari 1000 karakter.',
                            ])
                            ->placeholder('Contoh: Ganti spare part, Servis rutin, dll.')
                            ->helperText('Jelaskan pekerjaan perawatan yang dilakukan (maksimal 1000 karakter)')
                            ->columnSpanFull(),

                        TextInput::make('performed_by')
                            ->label('Dikerjakan Oleh')
                            ->maxLength(255)
                            ->validationAttribute('dikerjakan oleh')
                            ->validationMessages([
                                'max' => 'Nama tidak boleh lebih dari 255 karakter.',
                            ])
                            ->placeholder('Contoh: Teknisi Ahmad, PT Service XYZ')
                            ->helperText('Nama teknisi atau perusahaan yang melakukan perawatan')
                            ->columnSpanFull(),
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
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
                            ]),
                    ])
                    ->aside()
                    ->columnSpanFull()
                    ->visible(fn(string $operation): bool => $operation === 'edit'),
            ])
            ->columns(1);
    }
}
