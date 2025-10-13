<?php

namespace Webkul\FullCalendar;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Webkul\Support\Console\Commands\InstallCommand;
use Webkul\Support\Console\Commands\UninstallCommand;
use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;

class FullCalendarServiceProvider extends PackageServiceProvider
{
    public static string $name = 'full-calendar';

    public static string $viewNamespace = 'full-calendar';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasInstallCommand(function (InstallCommand $command) {})
            ->hasUninstallCommand(function (UninstallCommand $command) {});
    }

    public function packageBooted(): void
    {
        $this->registerCustomCss();
    }

    public function registerCustomCss()
    {
        FilamentAsset::register(assets: [
            Css::make('full-calendar', __DIR__.'/../resources/dist/app.css'),
            AlpineComponent::make('full-calendar', __DIR__.'/../resources/dist/app.js'),
        ], package: 'full-calendar');
    }
}
