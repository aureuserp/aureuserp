<?php

namespace Webkul\Barcode;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Livewire\Livewire;
use Webkul\Barcode\Livewire\Adjustments;
use Webkul\Barcode\Livewire\Dashboard;
use Webkul\Barcode\Livewire\Operation;
use Webkul\Barcode\Livewire\Transfers;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class BarcodeServiceProvider extends PackageServiceProvider
{
    public static string $name = 'barcode';

    public static string $viewNamespace = 'barcode';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasRoute('web')
            ->hasDependencies([
                'inventories',
            ])
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->installDependencies();
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {})
            ->icon('barcode');
    }

    public function packageBooted(): void
    {
        Livewire::component('barcode-dashboard', Dashboard::class);
        Livewire::component('barcode-adjustments', Adjustments::class);
        Livewire::component('barcode-transfers', Transfers::class);
        Livewire::component('barcode-operation', Operation::class);

        FilamentAsset::register(assets: [
            Css::make('barcode', __DIR__.'/../resources/dist/barcode.css'),
            Js::make('barcode', __DIR__.'/../resources/dist/barcode.js'),
            Js::make('html5-qrcode', __DIR__.'/../resources/dist/html5-qrcode.min.js'),
        ], package: 'barcode');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(BarcodePlugin::make());
        });
    }
}
