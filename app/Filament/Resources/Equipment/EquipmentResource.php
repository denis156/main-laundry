<?php

declare(strict_types=1);

namespace App\Filament\Resources\Equipment;

use UnitEnum;
use BackedEnum;
use App\Models\Equipment;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Equipment\Pages\ListEquipment;
use App\Filament\Resources\Equipment\Pages\EditEquipment;
use App\Filament\Resources\Equipment\Tables\EquipmentTable;
use App\Filament\Resources\Equipment\Schemas\EquipmentForm;
use App\Filament\Resources\Equipment\Pages\CreateEquipment;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'peralatan';

    protected static ?string $modelLabel = 'Peralatan';

    protected static ?string $pluralModelLabel = 'Peralatan';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Peralatan';

    protected static string|UnitEnum|null $navigationGroup = 'Inventori';

    protected static string|BackedEnum|null $navigationIcon = 'solar-washing-machine-linear';

    protected static string|BackedEnum|null $activeNavigationIcon = 'solar-washing-machine-bold';

    public static function form(Schema $schema): Schema
    {
        return EquipmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EquipmentTable::configure($table);
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
            'index' => ListEquipment::route('/'),
            'create' => CreateEquipment::route('/create'),
            'edit' => EditEquipment::route('/{record}/edit'),
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
