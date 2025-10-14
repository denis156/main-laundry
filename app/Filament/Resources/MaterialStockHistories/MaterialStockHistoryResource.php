<?php

declare(strict_types=1);

namespace App\Filament\Resources\MaterialStockHistories;

use UnitEnum;
use BackedEnum;
use App\Models\MaterialStockHistory;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Filament\Resources\MaterialStockHistories\Pages\EditMaterialStockHistory;
use App\Filament\Resources\MaterialStockHistories\Pages\ListMaterialStockHistories;
use App\Filament\Resources\MaterialStockHistories\Pages\CreateMaterialStockHistory;
use App\Filament\Resources\MaterialStockHistories\Schemas\MaterialStockHistoryForm;
use App\Filament\Resources\MaterialStockHistories\Tables\MaterialStockHistoriesTable;

class MaterialStockHistoryResource extends Resource
{
    protected static ?string $model = MaterialStockHistory::class;

    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'riwayat-stock-bahan';
    protected static ?string $modelLabel = 'Riwayat Stock Bahan';
    protected static ?string $navigationLabel = 'Riwayat Stock Bahan';
    protected static string|UnitEnum|null $navigationGroup = 'Inventori';
    protected static string |BackedEnum | null $navigationIcon = 'solar-clipboard-list-linear';
    protected static string |BackedEnum | null $activeNavigationIcon = 'solar-clipboard-list-bold';

    public static function form(Schema $schema): Schema
    {
        return MaterialStockHistoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaterialStockHistoriesTable::configure($table);
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
            'index' => ListMaterialStockHistories::route('/'),
            'create' => CreateMaterialStockHistory::route('/create'),
            'edit' => EditMaterialStockHistory::route('/{record}/edit'),
        ];
    }
}
