<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestBootstrapHelper
{
    private static bool $isERPInstalled = false;

    private static array $installedPlugins = [];

    public static function ensurePluginInstalled(string $pluginName): void
    {
        $pluginTables = [
            'projects'      => 'projects_projects',
            'sales'         => 'sales_orders',
            'purchases'     => 'purchases_orders',
            'inventories'   => 'inventories_operations',
            'accounts'      => 'accounts_account_moves',
            'products'      => 'products_products',
            'manufacturing' => 'manufacturing_orders',
        ];

        $table = $pluginTables[$pluginName] ?? null;

        if (! $table) {
            throw new InvalidArgumentException("Unknown plugin: {$pluginName}");
        }

        static::ensureERPInstalled();

        if (isset(static::$installedPlugins[$pluginName])) {
            return;
        }

        if (! Schema::hasTable($table)) {
            Artisan::call("{$pluginName}:install", ['--no-interaction' => true]);
        }

        static::ensurePluginSeeded($pluginName);

        static::ensurePluginMarkedInstalled($pluginName);

        static::$installedPlugins[$pluginName] = true;

        // Re-register the plugin's routes into the already-booted application.
        // On CI, the app boots before beforeEach installs the plugin, so routes
        // are skipped in PackageServiceProvider::boot(). Loading them here ensures
        // the first test in each file can resolve named routes correctly.
        static::loadPluginRoutes($pluginName);
    }

    private static function ensurePluginSeeded(string $pluginName): void
    {
        $pluginSeeders = [
            'projects'      => ['projects_project_stages', 'Webkul\Project\Database\Seeders\DatabaseSeeder'],
            'sales'         => ['sales_teams', 'Webkul\Sale\Database\Seeders\DatabaseSeeder'],
            'inventories'   => ['inventories_locations', 'Webkul\Inventory\Database\Seeders\DatabaseSeeder'],
            'accounts'      => ['accounts_accounts', 'Webkul\Account\Database\Seeders\DatabaseSeeder'],
            'products'      => ['products_categories', 'Webkul\Product\Database\Seeders\DatabaseSeeder'],
            'manufacturing' => ['manufacturing_work_center_productivity_losses', 'Webkul\Manufacturing\Database\Seeders\DatabaseSeeder'],
        ];

        if (! isset($pluginSeeders[$pluginName])) {
            return;
        }

        [$probeTable, $seederClass] = $pluginSeeders[$pluginName];

        if (! Schema::hasTable($probeTable) || DB::table($probeTable)->exists()) {
            return;
        }

        Artisan::call('db:seed', [
            '--class' => $seederClass,
            '--force' => true,
        ]);
    }

    private static function ensurePluginMarkedInstalled(string $pluginName): void
    {
        if (! Schema::hasTable('plugins')) {
            return;
        }

        DB::table('plugins')->updateOrInsert(
            ['name' => $pluginName],
            [
                'is_installed' => true,
                'is_active'    => true,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        );
    }

    private static function loadPluginRoutes(string $pluginName): void
    {
        $routeFile = base_path("plugins/webkul/{$pluginName}/routes/api.php");

        if (file_exists($routeFile) && ! app()->routesAreCached()) {
            require $routeFile;
        }
    }

    public static function ensureERPInstalled(): void
    {
        if (static::$isERPInstalled) {
            return;
        }

        Artisan::call('migrate:fresh', ['--force' => true]);

        Artisan::call('erp:install', [
            '--force'          => true,
            '--admin-name'     => 'Test Admin',
            '--admin-email'    => 'admin@example.com',
            '--admin-password' => 'admin123',
        ]);

        static::$isERPInstalled = true;
    }
}
