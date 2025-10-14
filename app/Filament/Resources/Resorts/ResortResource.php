<?php

declare(strict_types=1);

namespace App\Filament\Resources\Resorts;

use UnitEnum;
use BackedEnum;
use App\Models\Resort;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Resorts\Pages\EditResort;
use App\Filament\Resources\Resorts\Pages\ListResorts;
use App\Filament\Resources\Resorts\Pages\CreateResort;
use App\Filament\Resources\Resorts\Schemas\ResortForm;
use App\Filament\Resources\Resorts\Tables\ResortsTable;

class ResortResource extends Resource
{
    protected static ?string $model = Resort::class;

    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'resort';
    protected static ?string $modelLabel = 'Resort';
    protected static int $globalSearchResultsLimit = 5;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Resort';
    protected static string|UnitEnum|null $navigationGroup = 'Menu Master Data';
    protected static string |BackedEnum | null $navigationIcon = 'solar-buildings-2-linear';
    protected static string |BackedEnum | null $activeNavigationIcon = 'solar-buildings-2-bold';

    public static function form(Schema $schema): Schema
    {
        return ResortForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResortsTable::configure($table);
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
            'index' => ListResorts::route('/'),
            'create' => CreateResort::route('/create'),
            'edit' => EditResort::route('/{record}/edit'),
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
