<?php

namespace Webkul\Website;

use Filament\Panel;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Auth\Events\Login;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Support\Support\VersionedCss;
use Webkul\Website\Console\Commands\DeleteInactiveVisitors;
use Webkul\Website\Console\Commands\ImportIpCountries;
use Webkul\Website\Http\Responses\LogoutResponse;
use Webkul\Website\Listeners\TrackWebsiteVisit;
use Webkul\Website\Services\VisitorTracker;

class WebsiteServiceProvider extends PackageServiceProvider
{
    public static string $name = 'website';

    public static string $viewNamespace = 'website';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2025_03_10_094011_create_website_pages_table',
                '2025_03_10_064655_alter_partners_partners_table',
                '2026_08_03_100000_add_last_login_at_to_partners_partners_table',
                '2026_08_13_000001_make_website_pages_translatable',
                '2026_10_26_000001_create_website_visitors_tables',
                '2026_10_26_000002_create_website_ip_countries_table',
            ])
            ->runsMigrations()
            ->hasSeeder('Webkul\\Website\\Database\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasSettings([
                '2025_03_10_094021_create_website_contact_settings',
                '2026_10_26_000003_add_contact_form_to_website_contact_settings',
            ])
            ->runsSettings()
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('website');
    }

    public function packageBooted(): void
    {
        $this->registerCustomCss();

        if (! Package::isPluginInstalled(self::$name)) {
            Route::get('/', function () {
                return redirect()->route('filament.admin.auth.login');
            });

            return;
        }

        PortalContributions::register();

        Event::listen(Login::class, function (Login $event): void {
            if ($event->guard !== 'customer') {
                return;
            }

            $event->user->forceFill(['last_login_at' => now()])->saveQuietly();

            app(VisitorTracker::class)->linkPartner($event->user, request()->cookie(VisitorTracker::COOKIE));
        });

        EncryptCookies::except([VisitorTracker::COOKIE, VisitorTracker::TIMEZONE_COOKIE]);

        Event::listen(RequestHandled::class, TrackWebsiteVisit::class);

        if ($this->app->runningInConsole()) {
            $this->commands([DeleteInactiveVisitors::class, ImportIpCountries::class]);
        }

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command('website:import-ip-countries')
                ->monthlyOn(5, '03:00')
                ->withoutOverlapping()
                ->when(fn (): bool => Package::isPluginInstalled(self::$name));

            $schedule->command('website:delete-inactive-visitors')
                ->daily()
                ->withoutOverlapping()
                ->when(fn (): bool => Package::isPluginInstalled(self::$name));
        });
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(WebsitePlugin::make());
        });

        $this->app->bind(\Filament\Auth\Http\Responses\Contracts\LogoutResponse::class, LogoutResponse::class);
    }

    public function registerCustomCss()
    {
        FilamentAsset::register([
            VersionedCss::make('website', __DIR__.'/../resources/dist/website.css'),
        ], 'website');
    }
}
