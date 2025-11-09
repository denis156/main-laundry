<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\CourierPerformanceTable;
use App\Filament\Widgets\OperationalStatsOverview;
use App\Filament\Widgets\PaymentStatusChart;
use App\Filament\Widgets\RecentTransactionsTable;
use App\Filament\Widgets\RevenueTrendChart;
use App\Filament\Widgets\TransactionStatsOverview;
use App\Filament\Widgets\WebAppInfoWidget;
use App\Filament\Widgets\WorkflowStatusChart;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use UnitEnum;

class Beranda extends BaseDashboard
{
    protected static string $routePath = 'beranda';

    protected static ?string $title = 'Beranda';

    protected ?string $subheading = 'Pantau dan kelola semua aktivitas laundry dalam satu halaman';

    protected static string|BackedEnum|null $navigationIcon = 'solar-monitor-smartphone-linear';

    protected static string|BackedEnum|null $activeNavigationIcon = 'solar-monitor-smartphone-bold';

    protected static string|UnitEnum|null $navigationGroup = 'Monitoring';

    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 4,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class,
            WebAppInfoWidget::class,
            TransactionStatsOverview::class,
            OperationalStatsOverview::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            RevenueTrendChart::class,
            WorkflowStatusChart::class,
            PaymentStatusChart::class,
            CourierPerformanceTable::class,
            RecentTransactionsTable::class,
        ];
    }
}
