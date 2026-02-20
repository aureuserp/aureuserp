<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestBootstrapHelper
{
    private static bool $systemDataSeeded = false;

    public static function ensurePluginInstalled(string $pluginName): void
    {
        // Map plugin names to a representative table
        $pluginTables = [
            'projects' => 'projects_projects',
            'sales' => 'sales_orders',
            'inventories' => 'inventories_operations',
            'accounts' => 'accounts_account_moves',
        ];

        $table = $pluginTables[$pluginName] ?? null;

        if (! $table) {
            throw new InvalidArgumentException("Unknown plugin: {$pluginName}");
        }

        // Check if the plugin's tables already exist
        if (Schema::hasTable($table)) {
            return;
        }

        // Ensure base system data exists before installing plugins
        static::ensureSystemDataSeeded();

        // Install the plugin; --no-interaction prevents prompts when dependencies are already installed
        Artisan::call("{$pluginName}:install", ['--no-interaction' => true]);
    }

    public static function ensureSystemDataSeeded(): void
    {
        if (static::$systemDataSeeded) {
            return;
        }

        // Check if system is already seeded
        if (Schema::hasTable('currencies') && DB::table('currencies')->exists()) {
            static::$systemDataSeeded = true;

            return;
        }

        // Install the complete ERP system with all base data
        Artisan::call('erp:install', [
            '--force' => true,
            '--admin-name' => 'Test Admin',
            '--admin-email' => 'admin@test.com',
            '--admin-password' => 'password',
        ]);

        static::$systemDataSeeded = true;
    }
}
