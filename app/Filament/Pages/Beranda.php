<?php

namespace App\Filament\Pages;

use UnitEnum;
use BackedEnum;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Beranda extends BaseDashboard
{
    protected static string $routePath = 'beranda';
    protected static ?string $title = 'Beranda';
    protected ?string $subheading = 'Pantau dan kelola semua aktivitas laundry dalam satu halaman';
    protected static string|BackedEnum|null $navigationIcon = 'solar-monitor-smartphone-linear';
    protected static string|BackedEnum|null $activeNavigationIcon = 'solar-monitor-smartphone-bold';
    protected static string|UnitEnum|null $navigationGroup = 'Monitoring';

    protected function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class,
            FilamentInfoWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 4,
        ];
    }
}
