<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class TestBootstrapHelper
{
    private static bool $isERPInstalled = false;

    public static function ensurePluginInstalled(string $pluginName): void
    {
        $pluginTables = [
            'projects'    => 'projects_projects',
            'sales'       => 'sales_orders',
            'purchases'   => 'purchases_orders',
            'inventories' => 'inventories_operations',
            'accounts'    => 'accounts_account_moves',
            'products'    => 'products_products',
        ];

        $table = $pluginTables[$pluginName] ?? null;

        if (! $table) {
            throw new InvalidArgumentException("Unknown plugin: {$pluginName}");
        }

        static::ensureERPInstalled();

        if (Schema::hasTable($table)) {
            return;
        }

        Artisan::call("{$pluginName}:install", ['--no-interaction' => true]);

        // Re-register the plugin's routes into the already-booted application.
        // On CI, the app boots before beforeEach installs the plugin, so routes
        // are skipped in PackageServiceProvider::boot(). Loading them here ensures
        // the first test in each file can resolve named routes correctly.
        static::loadPluginRoutes($pluginName);
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
