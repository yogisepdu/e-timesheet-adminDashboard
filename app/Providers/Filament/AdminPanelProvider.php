<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Enums\ThemeMode;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Widgets\AdminStatsOverview;
use App\Filament\Widgets\PendingTimeSheetsTable;
use App\Filament\Widgets\ProductionByActivityChart;
use App\Filament\Widgets\TimeSheetStatusChart;
use App\Filament\Widgets\TimeSheetTrendChart;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel

            /*
            |--------------------------------------------------------------------------
            | Basic Panel
            |--------------------------------------------------------------------------
            */

            ->default()

            ->id('admin')

            ->path('admin')


            /*
            |--------------------------------------------------------------------------
            | Authentication
            |--------------------------------------------------------------------------
            */

            ->login(Login::class)


            /*
            |--------------------------------------------------------------------------
            | THEME
            |--------------------------------------------------------------------------
            |
            | Paksa Filament menggunakan Light Mode.
            |
            | Ini penting karena sebelumnya Filament mengikuti
            | dark mode dari sistem operasi/browser.
            |
            */

            ->darkMode(false)

            ->defaultThemeMode(ThemeMode::Light)


            /*
            |--------------------------------------------------------------------------
            | Colors
            |--------------------------------------------------------------------------
            */

            ->colors([
                'primary' => Color::Amber,
            ])


            /*
            |--------------------------------------------------------------------------
            | Resources
            |--------------------------------------------------------------------------
            */

            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )


            /*
            |--------------------------------------------------------------------------
            | Pages
            |--------------------------------------------------------------------------
            */

            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )

            ->pages([
                Dashboard::class,
            ])


            /*
            |--------------------------------------------------------------------------
            | Widgets
            |--------------------------------------------------------------------------
            */

            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            )

            ->widgets([
                AdminStatsOverview::class,

                TimeSheetTrendChart::class,
                TimeSheetStatusChart::class,

                ProductionByActivityChart::class,

                PendingTimeSheetsTable::class,
            ])


            /*
            |--------------------------------------------------------------------------
            | Navigation Groups
            |--------------------------------------------------------------------------
            */

            ->navigationGroups([

                NavigationGroup::make()
                    ->label('Manajemen Pengguna')
                    ->collapsible(),

                NavigationGroup::make()
                    ->label('Master Data Operasional')
                    ->collapsible(),

                NavigationGroup::make()
                    ->label('Laporan & Persetujuan')
                    ->collapsible(),

            ])


            /*
            |--------------------------------------------------------------------------
            | Middleware
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | Authentication Middleware
            |--------------------------------------------------------------------------
            */

            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
