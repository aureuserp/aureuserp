<?php

namespace Webkul\Inventory;

use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

class InventoryServiceProvider extends PackageServiceProvider
{
    public static string $name = 'inventory';

    public function configureCustomPackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                'create_inventory_table',
            ])
            ->hasRoutes(['web', 'admin'])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->runsMigrations()
                    ->copyAndRegisterServiceProviderInApp();
            });
    }

    public function packageRegistered(): void
    {
        //
    }

    public function packageBooted(): void
    {
        //
    }
}