<?php

declare(strict_types=1);

namespace App\Filament\Resources\Resources;

use UnitEnum;
use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Models\Resource as ResourceModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Resources\Pages\EditResource;
use App\Filament\Resources\Resources\Pages\ListResources;
use App\Filament\Resources\Resources\Pages\CreateResource;
use App\Filament\Resources\Resources\Schemas\ResourceForm;
use App\Filament\Resources\Resources\Tables\ResourcesTable;

class ResourceResource extends Resource
{
    protected static ?string $model = ResourceModel::class;

    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'sumber-daya';
    protected static ?string $modelLabel = 'Sumber Daya';
    protected static ?string $pluralModelLabel = 'Sumber Daya';
    protected static int $globalSearchResultsLimit = 5;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Manajemen Sumber Daya';
    protected static string|UnitEnum|null $navigationGroup = 'Lokasi & Operasional';
    protected static string|BackedEnum|null $navigationIcon = 'solar-box-minimalistic-linear';
    protected static string|BackedEnum|null $activeNavigationIcon = 'solar-box-minimalistic-bold';

    public static function form(Schema $schema): Schema
    {
        return ResourceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResourcesTable::configure($table);
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
            'index' => ListResources::route('/'),
            'create' => CreateResource::route('/buat'),
            'edit' => EditResource::route('/{record}/ubah'),
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
