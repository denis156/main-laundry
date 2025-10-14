<?php

declare(strict_types=1);

namespace App\Filament\Resources\Materials;

use UnitEnum;
use BackedEnum;
use App\Models\Material;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Materials\Pages\EditMaterial;
use App\Filament\Resources\Materials\Pages\ListMaterials;
use App\Filament\Resources\Materials\Pages\CreateMaterial;
use App\Filament\Resources\Materials\Schemas\MaterialForm;
use App\Filament\Resources\Materials\Tables\MaterialsTable;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'bahan';
    protected static ?string $modelLabel = 'Bahan';
    protected static int $globalSearchResultsLimit = 5;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Bahan';
    protected static string|UnitEnum|null $navigationGroup = 'Inventori';
    protected static string |BackedEnum | null $navigationIcon = 'solar-box-linear';
    protected static string |BackedEnum | null $activeNavigationIcon = 'solar-box-bold';

    public static function form(Schema $schema): Schema
    {
        return MaterialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaterialsTable::configure($table);
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
            'index' => ListMaterials::route('/'),
            'create' => CreateMaterial::route('/create'),
            'edit' => EditMaterial::route('/{record}/edit'),
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
