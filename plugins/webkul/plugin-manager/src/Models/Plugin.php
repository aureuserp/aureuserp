<?php

namespace Webkul\PluginManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ReflectionClass;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class Plugin extends Model implements Sortable
{
    use SortableTrait;

    protected $fillable = [
        'name',
        'author',
        'summary',
        'description',
        'latest_version',
        'license',
        'is_active',
        'is_installed',
        'sort',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Plugin::class,
            'plugin_dependencies',
            'plugin_id',
            'dependency_id'
        );
    }

    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(
            Plugin::class,
            'plugin_dependencies',
            'dependency_id',
            'plugin_id'
        );
    }

    protected static function getAllPluginPackages(): array
    {
        $packages = [];

        $panels = app('filament')->getPanels();

        foreach ($panels as $panel) {
            foreach ($panel->getPlugins() as $pluginId => $plugin) {
                $pluginClass = get_class($plugin);

                $serviceProviderClass = str_replace('Plugin', 'ServiceProvider', $pluginClass);

                if (! class_exists($serviceProviderClass)) {
                    continue;
                }

                $reflection = new ReflectionClass($serviceProviderClass);

                if (! $reflection->isSubclassOf(PackageServiceProvider::class)) {
                    continue;
                }

                $serviceProvider = new $serviceProviderClass(app());

                $package = new Package;

                $serviceProvider->configureCustomPackage($package);

                if ($package->isCore) {
                    continue;
                }

                $package->basePath = dirname($reflection->getFileName(), 2);

                $packages[$pluginId] = $package;
            }
        }

        return $packages;
    }

    public function getPackageAttribute(): ?Package
    {
        $packages = static::getAllPluginPackages();

        return $packages[$this->name] ?? null;
    }

    public function getDependenciesFromConfig(): array
    {
        return $this->package?->dependencies ?? [];
    }

    public function getDependentsFromConfig(): array
    {
        $packages = static::getAllPluginPackages();

        $dependents = [];

        foreach ($packages as $pluginName => $package) {
            if ($pluginName === $this->name) {
                continue;
            }

            if (! in_array($this->name, $package->dependencies)) {
                continue;
            }

            $dependents[] = $pluginName;
        }

        return $dependents;
    }
}
