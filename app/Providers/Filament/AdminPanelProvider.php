<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Filament\Navigation\MenuItem;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Auth\Login;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;
use App\Filament\Widgets;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Route;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('ticketing')
            ->path('ticketing')
            ->login(Login::class)
            ->registration(Register::class)
            ->passwordReset()
            ->emailVerification()
            ->favicon(asset('storage/assets/gems.png'))
            ->brandLogo(function () {
                $isAuthRoute = Route::is([
                    'filament.ticketing.auth.login',
                    'filament.ticketing.auth.register',
                ]);

                // If it is an authentication page, return an empty string to hide the logo.
                if ($isAuthRoute) {
                    return url('storage/assets/logo.png');
                }

                // Return the custom brand logo using a standard <img> tag.
                return url('storage/assets/emp-care-logo-1.png');
            })
            ->brandLogoHeight('2rem')
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('15rem')
            ->databaseNotifications(true)
            ->databaseNotificationsPolling('20s')
            ->colors([
                'danger' => Color::Red,
                'gray' => Color::Gray,
                'info' => Color::Purple,
                'primary' => Color::hex('#118bf0'),
                'success' => Color::Green,
                'warning' => Color::Amber,
                'secondary' => Color::Zinc,
                'deptHead' => Color::hex('#ff7572'),
                'it' => Color::hex('#b56bff'),
                'admin' => Color::hex('#3496ff'),
                'records' => Color::hex('#57caff'),
                'payroll' => Color::hex('#1dffb0'),
                'claims' => Color::hex('#58fa5d'),
                'rsp' => Color::hex('#e3f85d'),
                'ld' => Color::hex('#ffd152'),
                'pm' => Color::hex('#ff9a42'),
                'extra' => Color::hex('#ee75de'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // ->widgets([
            //     // Widgets\AccountWidget::class,
            //     // Widgets\FilamentInfoWidget::class,
            //     Widgets\Stats::class,
            //     Widgets\Column::class,
            //     Widgets\Pie::class,
            // ])
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
            ])
            ->plugins([
                FilamentEditProfilePlugin::make()
                    ->slug('my-profile')
                    ->setTitle('My Profile')
                    ->setIcon('heroicon-o-user')
                    ->setSort(10)
                    ->shouldRegisterNavigation(false)
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars',
                    )
                    ->shouldShowBrowserSessionsForm(false)
                    ->customProfileComponents([
                        \App\Livewire\CustomProfileComponent::class,
                    ]),
                FilamentApexChartsPlugin::make(),
                AuthUIEnhancerPlugin::make()
                    ->formPanelPosition('left')
                    ->mobileFormPanelPosition('bottom')
                    ->formPanelWidth('40%')
                    ->emptyPanelBackgroundImageUrl(asset('images/AuthPagePanel1.png'))
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => auth()->user()->name)
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle')
            ])
            ->viteTheme('resources/css/filament/ticketing/theme.css')
            ->renderHook(
                'panels::body.end',
                fn () => view('components.filament-new-tab-listener'),
            );
    }
}
