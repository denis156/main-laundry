<?php

declare(strict_types=1);

namespace App\Filament\Resources\Services;

use BackedEnum;
use App\Models\Service;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Schemas\ServiceForm;
use App\Filament\Resources\Services\Tables\ServicesTable;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'layanan';
    protected static ?string $modelLabel = 'Layanan';
    protected static int $globalSearchResultsLimit = 5;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Manajemen Layanan';
    protected static string |BackedEnum | null $navigationIcon = 'solar-washing-machine-minimalistic-linear';

    public static function form(Schema $schema): Schema
    {
        return ServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServicesTable::configure($table);
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
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
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
