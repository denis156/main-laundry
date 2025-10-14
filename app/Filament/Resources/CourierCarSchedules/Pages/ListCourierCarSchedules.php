<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierCarSchedules\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CourierCarSchedules\CourierCarScheduleResource;
use App\Filament\Resources\CourierCarSchedules\Widgets\StatsOverviewCourierCarSchedule;

class ListCourierCarSchedules extends ListRecords
{
    protected static string $resource = CourierCarScheduleResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewCourierCarSchedule::class,
        ];
    }
}
