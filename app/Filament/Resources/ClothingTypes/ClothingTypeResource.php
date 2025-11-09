<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClothingTypes;

use BackedEnum;
use UnitEnum;
use App\Models\ClothingType;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ClothingTypes\Pages\EditClothingType;
use App\Filament\Resources\ClothingTypes\Pages\ListClothingTypes;
use App\Filament\Resources\ClothingTypes\Pages\CreateClothingType;
use App\Filament\Resources\ClothingTypes\Schemas\ClothingTypeForm;
use App\Filament\Resources\ClothingTypes\Tables\ClothingTypesTable;

class ClothingTypeResource extends Resource
{
    protected static ?string $model = ClothingType::class;

    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'jenis-pakaian';
    protected static ?string $modelLabel = 'Jenis Pakaian';
    protected static ?string $pluralModelLabel = 'Jenis Pakaian';
    protected static int $globalSearchResultsLimit = 5;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Jenis Pakaian';
    protected static string|UnitEnum|null $navigationGroup = 'Layanan & Produk';
    protected static string|BackedEnum|null $navigationIcon = 'solar-t-shirt-linear';
    protected static string|BackedEnum|null $activeNavigationIcon = 'solar-t-shirt-bold';

    public static function form(Schema $schema): Schema
    {
        return ClothingTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClothingTypesTable::configure($table);
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
            'index' => ListClothingTypes::route('/'),
            'create' => CreateClothingType::route('/buat'),
            'edit' => EditClothingType::route('/{record}/ubah'),
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
