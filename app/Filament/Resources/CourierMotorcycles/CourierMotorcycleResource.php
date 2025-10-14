<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierMotorcycles;

use UnitEnum;
use BackedEnum;
use App\Models\CourierMotorcycle;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CourierMotorcycles\Pages\EditCourierMotorcycle;
use App\Filament\Resources\CourierMotorcycles\Pages\ListCourierMotorcycles;
use App\Filament\Resources\CourierMotorcycles\Pages\CreateCourierMotorcycle;
use App\Filament\Resources\CourierMotorcycles\Schemas\CourierMotorcycleForm;
use App\Filament\Resources\CourierMotorcycles\Tables\CourierMotorcyclesTable;

class CourierMotorcycleResource extends Resource
{
    protected static ?string $model = CourierMotorcycle::class;

    protected static ?int $navigationSort = 5;
    protected static ?string $slug = 'kurir-motor';
    protected static ?string $modelLabel = 'Kurir Motor';
    protected static int $globalSearchResultsLimit = 5;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Kurir Motor';
    protected static string|UnitEnum|null $navigationGroup = 'Menu Master Data';
    protected static string |BackedEnum | null $navigationIcon = 'solar-delivery-bold-duotone';
    protected static string |BackedEnum | null $activeNavigationIcon = 'solar-delivery-bold';

    public static function form(Schema $schema): Schema
    {
        return CourierMotorcycleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourierMotorcyclesTable::configure($table);
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
            'index' => ListCourierMotorcycles::route('/'),
            'create' => CreateCourierMotorcycle::route('/create'),
            'edit' => EditCourierMotorcycle::route('/{record}/edit'),
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
