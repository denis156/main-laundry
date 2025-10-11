<?php

declare(strict_types=1);

namespace App\Filament\Resources\Members\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Members\MemberResource;
use App\Filament\Resources\Members\Widgets\StatsOverviewMembers;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewMembers::class,
        ];
    }
}
