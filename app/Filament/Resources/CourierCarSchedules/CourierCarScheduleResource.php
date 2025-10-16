<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierCarSchedules;

use UnitEnum;
use BackedEnum;
use App\Models\CourierCarSchedule;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
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

    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'jadwal-kurir-mobil';
    protected static ?string $modelLabel = 'Jadwal Kurir Mobil';
    protected static ?string $navigationLabel = 'Jadwal Kurir Mobil';
    protected static string|UnitEnum|null $navigationGroup = 'Lokasi & Operasional';
    protected static string |BackedEnum | null $navigationIcon = 'solar-calendar-mark-linear';
    protected static string |BackedEnum | null $activeNavigationIcon = 'solar-calendar-mark-bold';

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
            'create' => CreateCourierCarSchedule::route('/create'),
            'edit' => EditCourierCarSchedule::route('/{record}/edit'),
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
