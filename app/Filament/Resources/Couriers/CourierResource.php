<?php

declare(strict_types=1);

namespace App\Filament\Resources\Couriers;

use BackedEnum;
use UnitEnum;
use App\Models\Courier;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Couriers\Pages\EditCourier;
use App\Filament\Resources\Couriers\Pages\ListCouriers;
use App\Filament\Resources\Couriers\Pages\CreateCourier;
use App\Filament\Resources\Couriers\Schemas\CourierForm;
use App\Filament\Resources\Couriers\Tables\CouriersTable;

class CourierResource extends Resource
{
    protected static ?string $model = Courier::class;

    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'kurir';
    protected static ?string $modelLabel = 'Kurir';
    protected static ?string $pluralModelLabel = 'Kurir';
    protected static int $globalSearchResultsLimit = 5;
    protected static ?string $recordTitleAttribute = 'email';
    protected static ?string $navigationLabel = 'Manajemen Kurir';
    protected static string|UnitEnum|null $navigationGroup = 'Lokasi & Operasional';
    protected static string|BackedEnum|null $navigationIcon = 'solar-delivery-linear';
    protected static string|BackedEnum|null $activeNavigationIcon = 'solar-delivery-bold';

    public static function form(Schema $schema): Schema
    {
        return CourierForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CouriersTable::configure($table);
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
            'index' => ListCouriers::route('/'),
            'create' => CreateCourier::route('/buat'),
            'edit' => EditCourier::route('/{record}/ubah'),
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
