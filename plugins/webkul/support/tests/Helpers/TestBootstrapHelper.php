<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class TestBootstrapHelper
{
    private const BOOTSTRAP_CONNECTION = 'test_bootstrap';

    private static bool $isERPInstalled = false;

    private static array $installedPlugins = [];

    private static array $bootedPluginProviders = [];

    private static array $pluginTables = [
        'projects'      => 'projects_projects',
        'sales'         => 'sales_orders',
        'purchases'     => 'purchases_orders',
        'inventories'   => 'inventories_operations',
        'accounts'      => 'accounts_account_moves',
        'products'      => 'products_products',
        'manufacturing' => 'manufacturing_orders',
    ];

    public static function ensureAllPluginsInstalled(): void
    {
        foreach (array_keys(static::$pluginTables) as $pluginName) {
            static::ensurePluginInstalled($pluginName);
        }
    }

    public static function ensurePluginInstalled(string $pluginName): void
    {
        static::ensureERPInstalled();

        if (isset(static::$installedPlugins[$pluginName])) {
            return;
        }

        $table = static::$pluginTables[$pluginName] ?? null;

        static::onCommittedConnection(function () use ($pluginName, $table) {
            $isInstalled = $table
                ? Schema::hasTable($table)
                : DB::table('plugins')->where('name', $pluginName)->where('is_installed', true)->exists();

            if (! $isInstalled) {
                Artisan::call("{$pluginName}:install", ['--no-interaction' => true]);
            }

            foreach (static::$pluginTables as $candidate => $candidateTable) {
                if (isset(static::$installedPlugins[$candidate]) || ! Schema::hasTable($candidateTable)) {
                    continue;
                }

                static::ensurePluginSeeded($candidate);

                static::ensurePluginMarkedInstalled($candidate);

                static::$installedPlugins[$candidate] = true;
            }

            if (! $table) {
                static::ensurePluginMarkedInstalled($pluginName);

                static::$installedPlugins[$pluginName] = true;
            }
        });

        static::bootInstalledPluginProviders();

        static::restartTestTransaction();
    }

    private static function onCommittedConnection(callable $callback): void
    {
        $default = config('database.default');

        config(['database.connections.'.self::BOOTSTRAP_CONNECTION => config("database.connections.{$default}")]);

        DB::purge(self::BOOTSTRAP_CONNECTION);

        config(['database.default' => self::BOOTSTRAP_CONNECTION]);

        try {
            $callback();
        } finally {
            config(['database.default' => $default]);
        }
    }

    private static function restartTestTransaction(): void
    {
        $connection = DB::connection();

        if ($connection->transactionLevel() > 0) {
            $connection->rollBack();

            $connection->beginTransaction();
        }
    }

    private static function bootInstalledPluginProviders(): void
    {
        config(['database.connections.'.self::BOOTSTRAP_CONNECTION => config('database.connections.'.config('database.default'))]);

        $installed = DB::connection(self::BOOTSTRAP_CONNECTION)
            ->table('plugins')
            ->where('is_installed', true)
            ->pluck('name')
            ->all();

        foreach (array_keys(app()->getLoadedProviders()) as $providerClass) {
            if (! is_subclass_of($providerClass, PackageServiceProvider::class)) {
                continue;
            }

            $pluginName = property_exists($providerClass, 'name') ? $providerClass::$name : null;

            if (! $pluginName || isset(static::$bootedPluginProviders[$pluginName])) {
                continue;
            }

            if (! in_array($pluginName, $installed, true)) {
                continue;
            }

            $provider = app()->getProvider($providerClass);

            if ($provider instanceof PackageServiceProvider) {
                $provider->packageBooted();
            }

            static::registerPanelPlugin($pluginName);

            static::loadPluginRoutes($pluginName);

            static::$bootedPluginProviders[$pluginName] = true;
        }
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

        Package::$plugins = Plugin::all()->keyBy('name');
    }

    private static function registerPanelPlugin(string $pluginName): void
    {
        foreach (Filament::getPanels() as $panel) {
            if ($panel->hasPlugin($pluginName)) {
                $panel->getPlugin($pluginName)->register($panel);
            }
        }
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

        static::rememberProvidersBootedBeforeFresh();

        static::ensureWorkerDatabase();

        static::dropStalePluginMigrationPaths();

        Artisan::call('migrate:fresh', ['--force' => true]);

        Artisan::call('erp:install', [
            '--force'          => true,
            '--admin-name'     => 'Test Admin',
            '--admin-email'    => 'admin@example.com',
            '--admin-password' => 'admin123',
        ]);

        static::$isERPInstalled = true;

        static::bootInstalledPluginProviders();
    }

    private static function dropStalePluginMigrationPaths(): void
    {
        $coreNames = [];

        foreach (array_keys(app()->getLoadedProviders()) as $providerClass) {
            if (! is_subclass_of($providerClass, PackageServiceProvider::class)) {
                continue;
            }

            $provider = app()->getProvider($providerClass);

            if (! $provider instanceof PackageServiceProvider) {
                continue;
            }

            $package = Closure::bind(fn () => $this->package, $provider, get_class($provider))();

            if ($package?->isCore) {
                $coreNames[] = $package->shortName();
            }
        }

        $migrator = app('migrator');

        Closure::bind(function () use ($coreNames): void {
            $this->paths = array_values(array_filter($this->paths, function (string $path) use ($coreNames): bool {
                if (! preg_match('#plugins/webkul/([^/]+)/#', $path, $matches)) {
                    return true;
                }

                return in_array($matches[1], $coreNames, true);
            }));
        }, $migrator, get_class($migrator))();
    }

    private static function rememberProvidersBootedBeforeFresh(): void
    {
        foreach (Package::$plugins ?? [] as $name => $plugin) {
            if ($plugin->is_installed) {
                static::$bootedPluginProviders[$name] = true;
            }
        }
    }

    private static function ensureWorkerDatabase(): void
    {
        $token = env('TEST_TOKEN');

        if (blank($token)) {
            return;
        }

        $default = config('database.default');

        $connection = config("database.connections.{$default}");

        $workerDatabase = $connection['database'].'_p'.$token;

        if ($connection['driver'] === 'pgsql') {
            $pdo = new PDO(
                "pgsql:host={$connection['host']};port={$connection['port']};dbname={$connection['database']}",
                $connection['username'],
                $connection['password'],
            );

            $exists = $pdo->query('SELECT 1 FROM pg_database WHERE datname = '.$pdo->quote($workerDatabase))->fetchColumn();

            if (! $exists) {
                try {
                    $pdo->exec('CREATE DATABASE "'.$workerDatabase.'"');
                } catch (PDOException $e) {
                    if (! str_contains($e->getMessage(), 'already exists')) {
                        throw $e;
                    }
                }
            }
        } else {
            $pdo = new PDO(
                "mysql:host={$connection['host']};port={$connection['port']}",
                $connection['username'],
                $connection['password'],
            );

            $pdo->exec('CREATE DATABASE IF NOT EXISTS `'.$workerDatabase."` CHARACTER SET utf8mb4 COLLATE 'utf8mb4_unicode_ci'");
        }

        config(["database.connections.{$default}.database" => $workerDatabase]);

        DB::purge($default);

        putenv("DB_DATABASE={$workerDatabase}");

        $_ENV['DB_DATABASE'] = $workerDatabase;

        $_SERVER['DB_DATABASE'] = $workerDatabase;

        static::ensureWorkerViewPath($token);
    }

    private static function ensureWorkerViewPath(string $token): void
    {
        $viewPath = storage_path('framework/testing/views/'.$token);

        if (! is_dir($viewPath)) {
            mkdir($viewPath, 0755, true);
        }

        config(['view.compiled' => $viewPath]);

        putenv("VIEW_COMPILED_PATH={$viewPath}");

        $_ENV['VIEW_COMPILED_PATH'] = $viewPath;

        $_SERVER['VIEW_COMPILED_PATH'] = $viewPath;
    }
}
