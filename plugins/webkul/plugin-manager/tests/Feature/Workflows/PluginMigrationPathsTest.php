<?php

use Illuminate\Support\Facades\DB;
use Webkul\Account\AccountServiceProvider;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\PluginManager\PluginMigrationPaths;
use Webkul\Product\ProductServiceProvider;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

it('registers migration paths by default and none when the flag is on', function () {
    $provider = new class(app()) extends PackageServiceProvider
    {
        public function configureCustomPackage(Package $package): void
        {
            $package->name('fake-plugin')
                ->isCore()
                ->hasMigrations(['2025_01_01_000000_create_fake_table'])
                ->runsMigrations();
        }
    };

    $registered = function (bool $flag) use ($provider): array {
        config(['plugin-manager.disable_migration_registration' => $flag]);

        $before = app('migrator')->paths();

        $instance = new ($provider::class)(app());
        $instance->register();
        $instance->boot();

        return array_values(array_diff(app('migrator')->paths(), $before));
    };

    expect($registered(false))->not->toBeEmpty();

    expect($registered(true))->toBeEmpty();
});

it('lists plugin migration and settings paths in dependency order without querying the database', function () {
    $queries = 0;

    DB::listen(function () use (&$queries) {
        $queries++;
    });

    $paths = PluginMigrationPaths::all([AccountServiceProvider::class, ProductServiceProvider::class]);

    $names = array_keys($paths);

    expect($names)->toContain('accounts', 'products')
        ->and(array_search('products', $names))->toBeLessThan(array_search('accounts', $names))
        ->and($paths['accounts']['migrations'])->not->toBeEmpty()
        ->and($paths['accounts']['migrations'][0])->toEndWith('.php')
        ->and($paths['accounts']['settings_migrations'])->not->toBeEmpty()
        ->and($queries)->toBe(0);

    foreach ([...$paths['accounts']['migrations'], ...$paths['accounts']['settings_migrations']] as $path) {
        expect(file_exists($path))->toBeTrue();
    }
});

it('describes a plugin whose provider is not registered in the application', function () {
    $provider = new class(app()) extends PackageServiceProvider
    {
        public function configureCustomPackage(Package $package): void
        {
            $package->name('unregistered-plugin')
                ->hasMigrations(['2030_01_01_000000_create_unregistered_table'])
                ->hasSettings(['2030_01_01_000001_create_unregistered_settings'])
                ->hasDependencies(['accounts']);
        }
    };

    $paths = PluginMigrationPaths::all([$provider::class, AccountServiceProvider::class]);

    $names = array_keys($paths);

    expect($names)->toBe([...array_slice($names, 0, -1), 'unregistered-plugin'])
        ->and(array_search('accounts', $names))->toBeLessThan(array_search('unregistered-plugin', $names))
        ->and($paths['unregistered-plugin']['dependencies'])->toBe(['accounts']);
});

it('only lists migration and settings files that exist on disk', function () {
    $paths = PluginMigrationPaths::all();

    $missing = collect($paths)
        ->flatMap(fn (array $plugin): array => [...$plugin['migrations'], ...$plugin['settings_migrations']])
        ->reject(fn (string $path): bool => is_file($path));

    expect($missing->all())->toBe([])
        ->and($paths['support']['migrations'])->not->toContain(base_path('plugins/webkul/support/database/migrations/2024_11_05_105102_create_plugins_table.php'));
});

it('lists core plugins before the others', function () {
    $paths = PluginMigrationPaths::all();

    $providers = (fn (): array => static::discoverProviderClasses())->bindTo(null, PluginMigrationPaths::class)();

    $coreNames = collect($providers)
        ->map(fn (string $provider): Package => (new $provider(app()))->describePackage())
        ->filter(fn (Package $package): bool => $package->isCore)
        ->map(fn (Package $package): string => $package->name);

    expect(collect($paths)->filter(fn (array $plugin): bool => $plugin['is_core'])->keys()->all())
        ->toEqualCanonicalizing($coreNames->values()->all());

    $order = array_keys($paths);

    $lastCore = $coreNames->map(fn (string $name): int => array_search($name, $order))->max();

    expect($coreNames)->not->toBeEmpty()
        ->and(array_slice($order, 0, $lastCore + 1))->toEqualCanonicalizing($coreNames->values()->all());
});

it('throws on circular dependencies', function () {
    $a = new class(app()) extends PackageServiceProvider
    {
        public function configureCustomPackage(Package $package): void
        {
            $package->name('cycle-a')->hasDependencies(['cycle-b']);
        }
    };

    $b = new class(app()) extends PackageServiceProvider
    {
        public function configureCustomPackage(Package $package): void
        {
            $package->name('cycle-b')->hasDependencies(['cycle-a']);
        }
    };

    PluginMigrationPaths::all([$a::class, $b::class]);
})->throws(LogicException::class, 'Circular plugin dependency');
