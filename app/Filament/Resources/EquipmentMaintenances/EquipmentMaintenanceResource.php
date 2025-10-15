<?php

declare(strict_types=1);

namespace App\Filament\Resources\EquipmentMaintenances;

use UnitEnum;
use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Models\EquipmentMaintenance;
use App\Filament\Resources\EquipmentMaintenances\Tables\EquipmentMaintenancesTable;
use App\Filament\Resources\EquipmentMaintenances\Schemas\EquipmentMaintenanceForm;
use App\Filament\Resources\EquipmentMaintenances\Pages\ListEquipmentMaintenances;
use App\Filament\Resources\EquipmentMaintenances\Pages\EditEquipmentMaintenance;
use App\Filament\Resources\EquipmentMaintenances\Pages\CreateEquipmentMaintenance;

class EquipmentMaintenanceResource extends Resource
{
    protected static ?string $model = EquipmentMaintenance::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'perawatan-peralatan';

    protected static ?string $modelLabel = 'Perawatan Peralatan';

    protected static ?string $pluralModelLabel = 'Perawatan Peralatan';

    protected static ?string $navigationLabel = 'Perawatan Peralatan';

    protected static string|UnitEnum|null $navigationGroup = 'Inventori';

    protected static string|BackedEnum|null $navigationIcon = 'solar-settings-linear';

    protected static string|BackedEnum|null $activeNavigationIcon = 'solar-settings-bold';

    public static function form(Schema $schema): Schema
    {
        return EquipmentMaintenanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EquipmentMaintenancesTable::configure($table);
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
            'index' => ListEquipmentMaintenances::route('/'),
            'create' => CreateEquipmentMaintenance::route('/create'),
            'edit' => EditEquipmentMaintenance::route('/{record}/edit'),
        ];
    }
}
