<?php

declare(strict_types=1);

namespace App\Filament\Resources\Members\Schemas;

use App\Services\MemberService;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\DateTimePicker;

class MemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Member')
                    ->description('Data keanggotaan pelanggan termasuk tier membership dan status aktif')
                    ->collapsible()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 2,
                        ])
                            ->schema([
                                Select::make('customer_id')
                                    ->label('Pelanggan')
                                    ->relationship('customer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->validationAttribute('pelanggan')
                                    ->validationMessages([
                                        'required' => 'Pelanggan wajib dipilih.',
                                    ])
                                    ->placeholder('Pilih pelanggan')
                                    ->columnSpanFull(),

                                Select::make('membership_tier_id')
                                    ->label('Tier Membership')
                                    ->relationship('membershipTier', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    ->validationAttribute('tier membership')
                                    ->validationMessages([
                                        'required' => 'Tier membership wajib dipilih.',
                                    ])
                                    ->placeholder('Pilih tier membership'),

                                TextInput::make('member_number')
                                    ->label('Nomor Member')
                                    ->required()
                                    ->maxLength(50)
                                    ->unique(ignoreRecord: true)
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(fn() => app(MemberService::class)->generateMemberNumber())
                                    ->validationAttribute('nomor member')
                                    ->validationMessages([
                                        'required' => 'Nomor member wajib diisi.',
                                        'unique' => 'Nomor member sudah terdaftar.',
                                        'max' => 'Nomor member tidak boleh lebih dari 50 karakter.',
                                    ])
                                    ->hint('Otomatis')
                                    ->helperText('Nomor member akan di-generate otomatis')
                                    ->placeholder('MBR-YYYYMMDD-XXXX'),

                                DatePicker::make('member_since')
                                    ->label('Member Sejak')
                                    ->required()
                                    ->native(false)
                                    ->default(now())
                                    ->maxDate(now())
                                    ->validationAttribute('tanggal member')
                                    ->validationMessages([
                                        'required' => 'Tanggal member sejak wajib diisi.',
                                        'max' => 'Tanggal member tidak boleh lebih dari hari ini.',
                                    ])
                                    ->placeholder('Pilih tanggal'),

                                TextInput::make('total_points')
                                    ->label('Total Poin')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(999999)
                                    ->validationAttribute('total poin')
                                    ->validationMessages([
                                        'required' => 'Total poin wajib diisi.',
                                        'numeric' => 'Total poin harus berupa angka.',
                                        'min' => 'Total poin tidak boleh kurang dari 0.',
                                        'max' => 'Total poin tidak boleh lebih dari 999999.',
                                    ])
                                    ->suffix('Poin')
                                    ->placeholder('0'),

                                ToggleButtons::make('is_active')
                                    ->label('Status Member')
                                    ->inline()
                                    ->boolean()
                                    ->required()
                                    ->default(true)
                                    ->grouped()
                                    ->icons([
                                        true => 'heroicon-o-check-circle',
                                        false => 'heroicon-o-x-circle',
                                    ])
                                    ->colors([
                                        true => 'success',
                                        false => 'danger',
                                    ]),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),

                Section::make('Tanggal & Waktu')
                    ->description('Riwayat tanggal dan waktu pembuatan dan update terakhir data member')
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
