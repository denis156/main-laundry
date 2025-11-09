<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierCarSchedules;

use UnitEnum;
use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Models\CourierCarSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CourierCarSchedules\Pages\EditCourierCarSchedule;
use App\Filament\Resources\CourierCarSchedules\Pages\ListCourierCarSchedules;
use App\Filament\Resources\CourierCarSchedules\Pages\CreateCourierCarSchedule;
use App\Filament\Resources\CourierCarSchedules\Schemas\CourierCarScheduleForm;
use App\Filament\Resources\CourierCarSchedules\Tables\CourierCarSchedulesTable;

class CourierCarScheduleResource extends Resource
{
    protected static ?string $model = CourierCarSchedule::class;

    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'jadwal-mobil';
    protected static ?string $modelLabel = 'Jadwal Mobil';
    protected static ?string $pluralModelLabel = 'Jadwal Mobil';
    protected static int $globalSearchResultsLimit = 5;
    protected static ?string $recordTitleAttribute = 'trip_date';
    protected static ?string $navigationLabel = 'Jadwal Mobil Kurir';
    protected static string|UnitEnum|null $navigationGroup = 'Lokasi & Operasional';
    protected static string|BackedEnum|null $navigationIcon = 'solar-course-up-linear';
    protected static string|BackedEnum|null $activeNavigationIcon = 'solar-course-up-bold';

    public static function form(Schema $schema): Schema
    {
        return CourierCarScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourierCarSchedulesTable::configure($table);
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
            'index' => ListCourierCarSchedules::route('/'),
            'create' => CreateCourierCarSchedule::route('/buat'),
            'edit' => EditCourierCarSchedule::route('/{record}/ubah'),
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
