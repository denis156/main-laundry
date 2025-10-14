<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierCarSchedules\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class CourierCarScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jadwal')
                    ->description('Data lengkap jadwal kurir mobil termasuk tanggal, waktu, jenis trip, dan resort yang dikunjungi')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                DatePicker::make('trip_date')
                                    ->label('Tanggal Perjalanan')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d F Y')
                                    ->minDate(now()->subDays(7))
                                    ->maxDate(now()->addMonths(3))
                                    ->validationAttribute('tanggal perjalanan')
                                    ->validationMessages([
                                        'required' => 'Tanggal perjalanan wajib diisi.',
                                        'after_or_equal' => 'Tanggal perjalanan tidak boleh lebih dari 7 hari yang lalu.',
                                        'before_or_equal' => 'Tanggal perjalanan tidak boleh lebih dari 3 bulan ke depan.',
                                    ])
                                    ->helperText('Pilih tanggal perjalanan kurir mobil'),

                                TimePicker::make('departure_time')
                                    ->label('Waktu Keberangkatan')
                                    ->required()
                                    ->native(false)
                                    ->seconds(false)
                                    ->minutesStep(15)
                                    ->validationAttribute('waktu keberangkatan')
                                    ->validationMessages([
                                        'required' => 'Waktu keberangkatan wajib diisi.',
                                    ])
                                    ->helperText('Pilih waktu keberangkatan (interval 15 menit)'),

                                ToggleButtons::make('trip_type')
                                    ->label('Jenis Trip')
                                    ->required()
                                    ->grouped()
                                    ->options([
                                        'pickup' => 'Ambil dari Resort',
                                        'delivery' => 'Antar ke Resort',
                                    ])
                                    ->colors([
                                        'pickup' => 'info',
                                        'delivery' => 'success',
                                    ])
                                    ->icons([
                                        'pickup' => 'solar-box-bold',
                                        'delivery' => 'solar-delivery-bold',
                                    ])
                                    ->validationAttribute('jenis trip')
                                    ->validationMessages([
                                        'required' => 'Jenis trip wajib dipilih.',
                                    ])
                                    ->helperText('Pickup: ambil laundry dari resort ke pos pusat | Delivery: antar laundry bersih dari pos pusat ke resort')
                                    ->columnSpanFull(),

                                Select::make('resort_ids')
                                    ->label('Resort Dikunjungi')
                                    ->options(fn() => \App\Models\Resort::query()
                                        ->where('is_active', true)
                                        ->pluck('name', 'id'))
                                    ->multiple()
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->validationAttribute('resort')
                                    ->validationMessages([
                                        'required' => 'Minimal satu resort harus dipilih.',
                                    ])
                                    ->placeholder('Pilih resort yang akan dikunjungi')
                                    ->helperText('Pilih satu atau lebih resort yang akan dikunjungi dalam trip ini')
                                    ->columnSpanFull(),

                                ToggleButtons::make('status')
                                    ->label('Status')
                                    ->required()
                                    ->grouped()
                                    ->default('scheduled')
                                    ->options([
                                        'scheduled' => 'Dijadwalkan',
                                        'in_progress' => 'Sedang Berlangsung',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ])
                                    ->colors([
                                        'scheduled' => 'info',
                                        'in_progress' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                    ])
                                    ->icons([
                                        'scheduled' => 'solar-calendar-mark-bold',
                                        'in_progress' => 'solar-clock-circle-bold',
                                        'completed' => 'solar-check-circle-bold',
                                        'cancelled' => 'solar-close-circle-bold',
                                    ])
                                    ->validationAttribute('status')
                                    ->validationMessages([
                                        'required' => 'Status wajib dipilih.',
                                    ])
                                    ->columnSpanFull(),

                                Textarea::make('notes')
                                    ->label('Catatan')
                                    ->rows(3)
                                    ->maxLength(1000)
                                    ->validationAttribute('catatan')
                                    ->validationMessages([
                                        'max' => 'Catatan tidak boleh lebih dari 1000 karakter.',
                                    ])
                                    ->placeholder('Masukkan catatan perjalanan (opsional)')
                                    ->helperText('Opsional')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data jadwal')
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
