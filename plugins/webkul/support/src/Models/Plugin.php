<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Support\Package;

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
        $pluginClasses = require base_path('bootstrap/plugins.php');

        $packages = [];

        foreach ($pluginClasses as $pluginClass) {
            if (! class_exists($pluginClass)) {
                continue;
            }

            $serviceProviderClass = str_replace('Plugin', 'ServiceProvider', $pluginClass);

            if (! class_exists($serviceProviderClass)) {
                continue;
            }

            $plugin = new $pluginClass;

            $serviceProvider = new $serviceProviderClass(app());

            $package = new Package;

            $serviceProvider->configureCustomPackage($package);

            // Get the directory of the plugin
            $reflection = new \ReflectionClass($serviceProviderClass);
            $package->basePath = dirname($reflection->getFileName(), 2);

            $packages[$plugin->getId()] = $package;
        }

        return $packages;
    }

    public function getPackageAttribute(): ?Package
    {
        $packages = self::getAllPluginPackages();

        return $packages[$this->name] ?? null;
    }

    public function getDependenciesFromConfig(): array
    {
        return $this->package?->dependencies ?? [];
    }

    public function getDependentsFromConfig(): array
    {
        $packages = self::getAllPluginPackages();

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
