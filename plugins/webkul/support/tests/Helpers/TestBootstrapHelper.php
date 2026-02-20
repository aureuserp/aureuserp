<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Support\Models\Currency;

class TestBootstrapHelper
{
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

        // Check if the plugin is marked as installed
        $isInstalled = DB::table('plugins')
            ->where('name', $pluginName)
            ->where('is_installed', true)
            ->exists();

        if ($isInstalled) {
            return;
        }

        test()->artisan("{$pluginName}:install", ['--no-interaction' => true])
            ->assertSuccessful();
    }

    public static function ensureBaseCurrencies(array $requiredIds = [1, 2, 3]): void
    {
        $existingIds = Currency::query()
            ->whereIn('id', $requiredIds)
            ->pluck('id')
            ->all();

        $missingIds = array_values(array_diff($requiredIds, $existingIds));

        if ($missingIds === []) {
            return;
        }

        $sequenceStates = array_map(
            fn (int $id): array => ['id' => $id],
            $missingIds
        );

        Currency::factory()
            ->count(count($missingIds))
            ->sequence(...$sequenceStates)
            ->create();
    }
}
