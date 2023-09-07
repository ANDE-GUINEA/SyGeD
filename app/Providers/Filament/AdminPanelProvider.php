<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Awcodes\Overlook\OverlookPlugin;
use App\Filament\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Awcodes\Overlook\Widgets\OverlookWidget;
use lockscreen\FilamentLockscreen\Lockscreen;
use Filament\FontProviders\GoogleFontProvider;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Awcodes\FilamentStickyHeader\StickyHeaderPlugin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use lockscreen\FilamentLockscreen\Http\Middleware\Locker;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Resources\DecretResource\Widgets\DecretChart;
use App\Filament\Resources\DecretResource\Widgets\LatestDecret;

class AdminPanelProvider extends PanelProvider
{
    protected int | string | array $columnSpan = 'full';
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->favicon(asset('storage/logo/gn.png'))
            ->breadcrumbs(false)
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile()
            // ->registration()
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])->font(
                'Inter',
                'Poppins',
                provider: GoogleFontProvider::class
            )->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                StatsOverview::class,
                DecretChart::class,
                LatestDecret::class,
                // OverlookWidget::class,
            ])
            // ->favicon(asset('images/favicon.png')),
            // ->darkMode(false),
            // ->darkMode(false),
            ->plugins([
                BreezyCore::make()
                    ->enableTwoFactorAuthentication(
                        force: false, // force the user to enable 2FA before they can use the application (default = false)
                        // action: CustomTwoFactorPage::class // optionally, use a custom 2FA page
                    )->enableSanctumTokens(
                        permissions: ['my', 'custom', 'permissions'] // optional, customize the permissions (default = ["create", "view", "update", "delete"])
                    ),
                FilamentShieldPlugin::make(),
                StickyHeaderPlugin::make()
                    // ->floating()
                    ->colored(),
                OverlookPlugin::make()
                    ->sort(2)
                    ->columns([
                        'default' => 1,
                        'sm' => 2,
                        'md' => 3,
                        'lg' => 4,
                        'xl' => 5,
                        '2xl' => null,
                    ]),
            ])->plugin(
                new Lockscreen(),

            )
            // ->sidebarCollapsibleOnDesktop()
            ->topNavigation()
            // ->sidebarFullyCollapsibleOnDesktop()
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
                // Locker::class,
                Locker::class,
            ]);
    }
}
