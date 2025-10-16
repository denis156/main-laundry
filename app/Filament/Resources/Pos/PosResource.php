<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pos;

use UnitEnum;
use BackedEnum;
use App\Models\Pos;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Pos\Pages\EditPos;
use App\Filament\Resources\Pos\Pages\ListPos;
use App\Filament\Resources\Pos\Tables\PosTable;
use App\Filament\Resources\Pos\Pages\CreatePos;
use App\Filament\Resources\Pos\Schemas\PosForm;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PosResource extends Resource
{
    protected static ?string $model = Pos::class;

    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'pos';
    protected static ?string $modelLabel = 'Pos';
    protected static ?string $navigationLabel = 'Pos';
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Lokasi';
    protected static string|BackedEnum|null $navigationIcon = 'solar-map-point-wave-linear';
    protected static string|BackedEnum|null $activeNavigationIcon = 'solar-map-point-wave-bold';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PosForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PosTable::configure($table);
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
            'index' => ListPos::route('/'),
            'create' => CreatePos::route('/create'),
            'edit' => EditPos::route('/{record}/edit'),
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
