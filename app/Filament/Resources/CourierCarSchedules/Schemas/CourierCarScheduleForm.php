<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierCarSchedules\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class CourierCarScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jadwal')
                    ->description('Detail tanggal, jenis trip, dan status jadwal')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                DatePicker::make('trip_date')
                                    ->label('Tanggal Trip')
                                    ->native(false)
                                    ->required()
                                    ->default(now())
                                    ->displayFormat('d M Y')
                                    ->timezone('Asia/Makassar')
                                    ->validationAttribute('tanggal trip')
                                    ->validationMessages([
                                        'required' => 'Tanggal trip wajib diisi.',
                                    ])
                                    ->columnSpanFull(),

                                ToggleButtons::make('trip_type')
                                    ->label('Jenis Trip')
                                    ->options([
                                        'pickup' => 'Penjemputan (POS → Resort)',
                                        'delivery' => 'Pengantaran (Resort → POS)',
                                    ])
                                    ->colors([
                                        'pickup' => 'info',
                                        'delivery' => 'success',
                                    ])
                                    ->icons([
                                        'pickup' => 'solar-box-linear',
                                        'delivery' => 'solar-delivery-linear',
                                    ])
                                    ->grouped()
                                    ->required()
                                    ->default('pickup')
                                    ->validationAttribute('jenis trip')
                                    ->validationMessages([
                                        'required' => 'Jenis trip wajib dipilih.',
                                    ])
                                    ->columnSpanFull(),

                                ToggleButtons::make('status')
                                    ->label('Status')
                                    ->options([
                                        'scheduled' => 'Terjadwal',
                                        'in_progress' => 'Sedang Berlangsung',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ])
                                    ->colors([
                                        'scheduled' => 'gray',
                                        'in_progress' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                    ])
                                    ->icons([
                                        'scheduled' => 'solar-calendar-linear',
                                        'in_progress' => 'solar-course-up-linear',
                                        'completed' => 'solar-check-circle-linear',
                                        'cancelled' => 'solar-close-circle-linear',
                                    ])
                                    ->grouped()
                                    ->required()
                                    ->default('scheduled')
                                    ->validationAttribute('status')
                                    ->validationMessages([
                                        'required' => 'Status wajib dipilih.',
                                    ])
                                    ->columnSpanFull(),

                                TimePicker::make('data.departure_time')
                                    ->label('Waktu Keberangkatan')
                                    ->native(false)
                                    ->seconds(false)
                                    ->timezone('Asia/Makassar')
                                    ->helperText('Waktu keberangkatan mobil dari titik awal')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Lokasi & Rute')
                    ->description('Lokasi yang dikunjungi dalam trip ini')
                    ->collapsible()
                    ->schema([
                        Select::make('data.location_ids')
                            ->label('Lokasi yang Dikunjungi')
                            ->options(
                                fn() => \App\Models\Location::query()
                                    ->where('is_active', true)
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('Pilih lokasi (resort/POS)')
                            ->helperText('Pilih lokasi (resort/POS) yang akan dikunjungi dalam trip ini')
                            ->columnSpanFull(),
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Informasi Sopir & Kendaraan')
                    ->description('Detail sopir dan kendaraan yang digunakan')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                TextInput::make('data.driver_info.name')
                                    ->label('Nama Sopir')
                                    ->maxLength(255)
                                    ->placeholder('Nama lengkap sopir')
                                    ->columnSpan(1),

                                TextInput::make('data.driver_info.phone')
                                    ->label('Nomor Telepon Sopir')
                                    ->tel()
                                    ->maxLength(20)
                                    ->minLength(10)
                                    ->regex('/^(0)?8[1-9][0-9]{6,11}$/')
                                    ->prefix('+62')
                                    ->dehydrateStateUsing(function ($state) {
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $state);
                                        if (str_starts_with($cleanPhone, '62')) {
                                            $cleanPhone = substr($cleanPhone, 2);
                                        }
                                        if (str_starts_with($cleanPhone, '0')) {
                                            $cleanPhone = substr($cleanPhone, 1);
                                        }
                                        return $cleanPhone;
                                    })
                                    ->validationAttribute('telepon sopir')
                                    ->validationMessages([
                                        'max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
                                        'min' => 'Nomor telepon minimal 10 karakter.',
                                        'regex' => 'Format telepon tidak valid. Gunakan format Indonesia yang benar.',
                                    ])
                                    ->helperText('Bisa tulis dengan 08 atau langsung 8')
                                    ->placeholder('Contoh: 81234567890')
                                    ->columnSpan(1),

                                TextInput::make('data.driver_info.vehicle_number')
                                    ->label('Nomor Kendaraan')
                                    ->maxLength(20)
                                    ->placeholder('B 1234 XYZ')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Catatan')
                    ->description('Informasi tambahan tentang jadwal trip')
                    ->collapsible()
                    ->schema([
                        Textarea::make('data.notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Catatan tambahan untuk jadwal trip ini...')
                            ->helperText('Opsional - Informasi tambahan tentang trip')
                            ->columnSpanFull(),
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data jadwal')
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
