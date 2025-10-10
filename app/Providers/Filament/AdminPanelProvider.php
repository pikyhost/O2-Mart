<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Livewire\ProfileContactDetails;
use App\Models\Setting;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // $settings = Setting::getAllSettings();
        // $faviconPath = $settings["favicon"] ?? null;
        // $favicon = $faviconPath ? Storage::url($faviconPath) : asset('images/clients/client1.png');
        return $panel
            ->default()

            ->id('admin')
            ->path('admin')
            ->brandName('𝑂2 𝑀𝑎𝑟𝑡')
        //    ->brandLogo(fn() => view('filament.app.logo'))
            ->emailVerification()
            ->login()
            ->passwordReset()
//            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'primary' => Color::Orange,
                'gray' => Color::Slate,
            ])
            ->navigationGroups([
                'Categories Management',
                'Service Points',
                'Vehicle Data',
                'Business Hours',
                'Blogs Management',
                'Settings Management'
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->visible(fn() => Filament::auth()->check())
                    ->url(url('/admin/my-profile')) // Adjusted route helper here
                    ->icon('heroicon-m-user-circle'),
                'logout' => MenuItem::make(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->resources([
                config('filament-logger.activity_resource')
            ])
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->visible(fn() => Filament::auth()->check())
                    ->url(url('/admin/my-profile')) // Adjusted route helper here
                    ->icon('heroicon-m-user-circle'),
                'logout' => MenuItem::make(),
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
            ->sidebarFullyCollapsibleOnDesktop()
            ->authMiddleware([
                Authenticate::class,
            ])
            ->spa()
            ->spaUrlExceptions([
                '*/admin/auto-parts/create',
                '*/admin/auto-parts/*/edit',

                '*/admin/batteries/create',
                '*/admin/batteries/*/edit',

                '*/admin/rims/create',
                '*/admin/rims/*/edit',

                '*/admin/tyres/create',
                '*/admin/tyres/*/edit',

                '/admin/categories/*/edit',
                '/admin/categories/create',

                '*/admin/blog-categories/create',
                '*/admin/blog-categories/*/edit',

                '*/admin/categories/*/edit',
                '*/admin/categories/create',

                '*/admin/blogs',
                '*/admin/blogs/*',
            ])
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn() => view('footer')
            )
         //   ->favicon($favicon)
            ->databaseNotifications()
            ->renderHook(PanelsRenderHook::SIDEBAR_NAV_START, fn () => view('navigation-filter'))
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const sidebar = document.querySelector("[data-sidebar]") || document.querySelector(".fi-sidebar-nav");
                        if (!sidebar) return;
                        
                        // Restore scroll position
                        const savedScrollTop = sessionStorage.getItem("filament-sidebar-scroll");
                        if (savedScrollTop) {
                            sidebar.scrollTop = parseInt(savedScrollTop);
                        }
                        
                        // Save scroll position on navigation
                        const navLinks = sidebar.querySelectorAll("a[href]");
                        navLinks.forEach(link => {
                            link.addEventListener("click", function() {
                                sessionStorage.setItem("filament-sidebar-scroll", sidebar.scrollTop);
                            });
                        });
                        
                        // Save on page unload
                        window.addEventListener("beforeunload", function() {
                            if (sidebar) {
                                sessionStorage.setItem("filament-sidebar-scroll", sidebar.scrollTop);
                            }
                        });
                    });
                </script>'
            )
            ->unsavedChangesAlerts()
            ->plugins([
//              \BezhanSalleh\FilamentGoogleAnalytics\FilamentGoogleAnalyticsPlugin::make(),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                SimpleLightBoxPlugin::make(),
                GlobalSearchModalPlugin::make(),
                BreezyCore::make()
                    ->myProfileComponents([
                        ProfileContactDetails::class,
                    ])
                    ->myProfile(
                        hasAvatars: true,
                        shouldRegisterNavigation: true
                    )
                    ->avatarUploadComponent(fn ($fileUpload) => $fileUpload->columnSpan('full')),
            ]);
    }

}
