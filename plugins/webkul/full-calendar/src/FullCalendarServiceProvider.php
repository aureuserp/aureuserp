<?php

namespace Webkul\FullCalendar;

use Filament\Panel;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class FullCalendarServiceProvider extends PackageServiceProvider
{
    public static string $name = 'full-calendar';

    public static string $viewNamespace = 'full-calendar';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasViews()
            ->hasTranslations()
            ->hasInstallCommand(function (InstallCommand $command) {})
            ->hasUninstallCommand(function (UninstallCommand $command) {});
    }

    public function packageBooted(): void
    {
        $this->registerCustomCss();
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            // Only register the default instance when a panel hasn't already
            // registered its own (e.g. TimeOffPlugin customises plugins/editable/
            // selectable). Registering unconditionally would clobber that instance
            // because plugins are keyed by id and the last write wins.
            if (! $panel->hasPlugin((new FullCalendarPlugin)->getId())) {
                $panel->plugin(FullCalendarPlugin::make());
            }
        });
    }

    public function registerCustomCss()
    {
        FilamentAsset::register(assets: [
            Css::make('full-calendar', __DIR__.'/../resources/dist/app.css'),
            AlpineComponent::make('full-calendar', __DIR__.'/../resources/dist/app.js'),
        ], package: 'full-calendar');
    }
}
