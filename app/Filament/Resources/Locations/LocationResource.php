<?php

declare(strict_types=1);

namespace App\Filament\Resources\Locations;

use UnitEnum;
use BackedEnum;
use App\Models\Location;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Locations\Pages\EditLocation;
use App\Filament\Resources\Locations\Pages\ListLocations;
use App\Filament\Resources\Locations\Pages\CreateLocation;
use App\Filament\Resources\Locations\Schemas\LocationForm;
use App\Filament\Resources\Locations\Tables\LocationsTable;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'lokasi';
    protected static ?string $modelLabel = 'Lokasi';
    protected static ?string $pluralModelLabel = 'Lokasi';
    protected static int $globalSearchResultsLimit = 5;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Lokasi';
    protected static string|UnitEnum|null $navigationGroup = 'Lokasi & Operasional';
    protected static string|BackedEnum|null $navigationIcon = 'solar-buildings-2-linear';
    protected static string|BackedEnum|null $activeNavigationIcon = 'solar-buildings-2-bold';

    public static function form(Schema $schema): Schema
    {
        return LocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LocationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLocations::route('/'),
            'create' => CreateLocation::route('/buat'),
            'edit' => EditLocation::route('/{record}/ubah'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
