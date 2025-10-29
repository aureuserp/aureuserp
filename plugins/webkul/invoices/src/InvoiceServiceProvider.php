<?php

namespace Webkul\Invoice;

use Webkul\Support\Console\Commands\InstallCommand;
use Webkul\Support\Console\Commands\UninstallCommand;
use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;

class InvoiceServiceProvider extends PackageServiceProvider
{
    public static string $name = 'invoices';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasTranslations()
            ->hasMigrations([
                '2025_02_04_082243_alter_products_products_table',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2025_02_26_094022_create_invoices_product_settings',
            ])
            ->runsSettings()
            ->hasDependencies([
                'accounts',
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('invoices');
    }
}
