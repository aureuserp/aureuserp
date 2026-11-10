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
        ->and($paths['unregistered-plugin']['migrations'][0])->toEndWith('2030_01_01_000000_create_unregistered_table.php')
        ->and($paths['unregistered-plugin']['settings_migrations'][0])->toEndWith('2030_01_01_000001_create_unregistered_settings.php');
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
