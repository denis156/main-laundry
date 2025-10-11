<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->loginRouteSlug('masuk')
            ->spa()
            ->font('Poppins')
            ->breadcrumbs(false)
            ->sidebarFullyCollapsibleOnDesktop()
            ->collapsibleNavigationGroups(false)
            ->colors([
                'primary' => [
                    50 => 'oklch(0.95 0.04 264.41)',
                    100 => 'oklch(0.90 0.08 264.41)',
                    200 => 'oklch(0.85 0.12 264.41)',
                    300 => 'oklch(0.80 0.15 264.41)',
                    400 => 'oklch(0.72 0.17 264.41)',
                    500 => 'oklch(0.6514 0.185 264.41)',
                    600 => 'oklch(0.58 0.19 264.41)',
                    700 => 'oklch(0.50 0.18 264.41)',
                    800 => 'oklch(0.42 0.16 264.41)',
                    900 => 'oklch(0.35 0.13 264.41)',
                    950 => 'oklch(0.25 0.09 264.41)',
                ],
                'danger' => [
                    50 => 'oklch(0.95 0.04 13.428)',
                    100 => 'oklch(0.90 0.08 13.428)',
                    200 => 'oklch(0.85 0.12 13.428)',
                    300 => 'oklch(0.80 0.15 13.428)',
                    400 => 'oklch(0.75 0.18 13.428)',
                    500 => 'oklch(0.71 0.194 13.428)',
                    600 => 'oklch(0.65 0.20 13.428)',
                    700 => 'oklch(0.55 0.19 13.428)',
                    800 => 'oklch(0.45 0.16 13.428)',
                    900 => 'oklch(0.35 0.13 13.428)',
                    950 => 'oklch(0.27 0.105 12.094)',
                ],
                'gray' => [
                    50 => 'oklch(0.99 0.005 60)',
                    100 => 'oklch(0.98 0.01 60)',
                    200 => 'oklch(0.96 0.015 60)',
                    300 => 'oklch(0.93 0.02 60)',
                    400 => 'oklch(0.88 0.025 60)',
                    500 => 'oklch(0.75 0.02 60)',
                    600 => 'oklch(0.60 0.015 60)',
                    700 => 'oklch(0.45 0.012 60)',
                    800 => 'oklch(0.30 0.010 60)',
                    900 => 'oklch(0.21 0.006 285.885)',
                    950 => 'oklch(0.14 0.005 285.823)',
                ],
                'info' => [
                    50 => 'oklch(0.95 0.03 232.661)',
                    100 => 'oklch(0.90 0.06 232.661)',
                    200 => 'oklch(0.85 0.10 232.661)',
                    300 => 'oklch(0.80 0.13 232.661)',
                    400 => 'oklch(0.77 0.15 232.661)',
                    500 => 'oklch(0.74 0.16 232.661)',
                    600 => 'oklch(0.65 0.15 232.661)',
                    700 => 'oklch(0.55 0.13 232.661)',
                    800 => 'oklch(0.45 0.10 232.661)',
                    900 => 'oklch(0.35 0.08 232.661)',
                    950 => 'oklch(0.29 0.066 243.157)',
                ],
                'success' => [
                    50 => 'oklch(0.95 0.04 163.223)',
                    100 => 'oklch(0.90 0.08 163.223)',
                    200 => 'oklch(0.85 0.12 163.223)',
                    300 => 'oklch(0.80 0.14 163.223)',
                    400 => 'oklch(0.78 0.16 163.223)',
                    500 => 'oklch(0.76 0.177 163.223)',
                    600 => 'oklch(0.68 0.18 163.223)',
                    700 => 'oklch(0.58 0.16 163.223)',
                    800 => 'oklch(0.48 0.13 163.223)',
                    900 => 'oklch(0.40 0.10 163.223)',
                    950 => 'oklch(0.37 0.077 168.94)',
                ],
                'warning' => [
                    50 => 'oklch(0.96 0.04 84.429)',
                    100 => 'oklch(0.92 0.08 84.429)',
                    200 => 'oklch(0.88 0.12 84.429)',
                    300 => 'oklch(0.85 0.15 84.429)',
                    400 => 'oklch(0.83 0.17 84.429)',
                    500 => 'oklch(0.82 0.189 84.429)',
                    600 => 'oklch(0.75 0.19 84.429)',
                    700 => 'oklch(0.65 0.17 84.429)',
                    800 => 'oklch(0.55 0.14 84.429)',
                    900 => 'oklch(0.45 0.12 84.429)',
                    950 => 'oklch(0.41 0.112 45.904)',
                ],
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
