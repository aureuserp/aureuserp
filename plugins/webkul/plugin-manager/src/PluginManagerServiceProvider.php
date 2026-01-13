<?php

namespace Webkul\PluginManager;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Event;
use Webkul\PluginManager\Console\Commands\InstallERP;

class PluginManagerServiceProvider extends PackageServiceProvider
{
    public static string $name = 'plugin-manager';

    public static string $viewNamespace = 'plugin-manager';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2024_11_05_105102_create_plugins_table',
            ])
            ->hasSeeder('Webkul\\PluginManager\\Database\\Seeders\\PluginSeeder')
            ->runsMigrations()
            ->runsSeeders()
            ->hasCommands([
                InstallERP::class,
            ]);
    }

    public function packageBooted(): void
    {
        $this->registerCustomCss();

        $this->app->make(PermissionManager::class)->managePermissions();

        Event::listen('aureus.installed', 'Webkul\PluginManager\Listeners\Installer@installed');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(PluginManagerPlugin::make());
        });

        $this->app->singleton(PermissionManager::class, fn () => new PermissionManager);
    }

    public function registerCustomCss()
    {
        FilamentAsset::register([
            Css::make('plugins', __DIR__.'/../resources/dist/plugin.css'),
        ], 'plugins');
    }
}
