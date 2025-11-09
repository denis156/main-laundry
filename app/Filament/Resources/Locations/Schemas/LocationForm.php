<?php

namespace App\Filament\Resources\Locations\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class LocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Resort Atau Pos')
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
                                        'pos' => 'Pos',
                                        'resort' => 'Resort',
                                    ])
                                    ->native(false)
                                    ->required(),
                                Select::make('parent_id')
                                    ->label('Resort Induk')
                                    ->relationship('parent', 'name')
                                    ->searchable()
                                    ->searchingMessage('Mencari lokasi...')
                                    ->noSearchResultsMessage('Tidak ada lokasi yang cocok.')
                                    ->preload()
                                    ->native(false),
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('data.district_code')
                                    ->required(),
                                TextInput::make('data.district_name')
                                    ->required(),
                                TextInput::make('data.village_code')
                                    ->required(),
                                TextInput::make('data.village_name')
                                    ->required(),
                                TextInput::make('data.detail_address')
                                    ->required(),
                                TextInput::make('data.address')
                                    ->required(),
                                TextInput::make('data.coordinates')
                                    ->hint('Format: [latitude, longitude]')
                                    ->helperText('Opsional, gunakan format array untuk koordinat.'),
                                Toggle::make('is_active')
                                    ->required(),
                            ])
                    ])
                    ->aside()
                    ->columnSpanFull(),
            ]);
    }
}
