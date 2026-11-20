<?php

namespace Webkul\PluginManager;

use Illuminate\Support\Facades\File;
use LogicException;

class PluginMigrationPaths
{
    public static function all(?array $providerClasses = null): array
    {
        $packages = [];

        foreach ($providerClasses ?? static::discoverProviderClasses() as $providerClass) {
            $package = (new $providerClass(app()))->describePackage();

            $packages[$package->name] = $package;
        }

        $result = [];

        foreach (static::sortByDependencies($packages) as $name => $package) {
            $result[$name] = [
                'is_core'             => $package->isCore,
                'dependencies'        => array_values(array_unique($package->dependencies)),
                'migrations'          => static::resolve($package, 'migrations', $package->migrationFileNames),
                'settings_migrations' => static::resolve($package, 'settings', $package->settingFileNames),
            ];
        }

        return $result;
    }

    protected static function discoverProviderClasses(): array
    {
        $classes = [];

        foreach (File::glob(base_path('plugins/*/*/composer.json')) as $composerFile) {
            $providers = json_decode(file_get_contents($composerFile), true)['extra']['laravel']['providers'] ?? [];

            foreach ($providers as $provider) {
                if (is_subclass_of($provider, PackageServiceProvider::class)) {
                    $classes[] = $provider;
                }
            }
        }

        return $classes;
    }

    protected static function sortByDependencies(array $packages): array
    {
        $sorted = [];

        $visiting = [];

        $visit = function (string $name, array $trail) use (&$visit, &$sorted, &$visiting, $packages): void {
            if (isset($sorted[$name]) || ! isset($packages[$name])) {
                return;
            }

            if (isset($visiting[$name])) {
                throw new LogicException('Circular plugin dependency: '.implode(' -> ', [...$trail, $name]));
            }

            $visiting[$name] = true;

            foreach ($packages[$name]->dependencies as $dependency) {
                $visit($dependency, [...$trail, $name]);
            }

            unset($visiting[$name]);

            $sorted[$name] = $packages[$name];
        };

        $coreFirst = collect($packages)->sortBy(fn (Package $package): int => $package->isCore ? 0 : 1)->keys();

        foreach ($coreFirst as $name) {
            $visit($name, []);
        }

        return $sorted;
    }

    protected static function resolve(Package $package, string $directory, array $fileNames): array
    {
        $paths = [];

        foreach ($fileNames as $fileName) {
            $path = $package->basePath("/../database/{$directory}/{$fileName}.php");

            $existing = realpath($path) ?: realpath($path.'.stub');

            if ($existing) {
                $paths[] = $existing;
            }
        }

        return $paths;
    }
}
