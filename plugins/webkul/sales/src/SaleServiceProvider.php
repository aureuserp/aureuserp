<?php

namespace Webkul\Sale;

use Webkul\Support\Console\Commands\InstallCommand;
use Webkul\Support\Console\Commands\UninstallCommand;
use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;

class SaleServiceProvider extends PackageServiceProvider
{
    public static string $name = 'sales';

    public static string $viewNamespace = 'sales';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2025_01_28_061110_create_sales_teams_table',
                '2025_01_28_074033_create_sales_team_members_table',
                '2025_01_28_102329_create_add_columns_to_product_categories_table',
                '2025_01_28_122700_create_sales_order_templates_table',
                '2025_02_05_053212_create_sales_orders_table',
                '2025_02_05_080609_create_sales_order_template_products_table',
                '2025_02_05_102851_create_sales_order_lines_table',
                '2025_03_05_073635_create_sales_order_options_table',
                '2025_03_05_124300_create_sales_order_line_taxes_table',
                '2025_03_05_124300_create_sales_tag_table',
                '2025_03_05_124400_create_sales_order_tags_table',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2025_02_05_094022_create_sales_product_settings',
                '2025_02_05_094025_create_sales_price_settings',
                '2025_02_05_095000_create_sales_invoice_settings',
                '2025_02_05_095005_create_sales_quotation_and_order_settings',
            ])
            ->runsSettings()
            ->hasDependencies([
                'invoices',
                'payments',
            ])
            ->hasSeeder('Webkul\\Sale\\Database\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {});
    }

    public function packageBooted(): void
    {
        // Livewire::component('quotation-summary', Summary::class);
    }
}
