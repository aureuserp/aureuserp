<?php

namespace Webkul\Chatter;

use Filament\Contracts\Plugin;
use Filament\Panel;
use ReflectionClass;

class ChatterPlugin implements Plugin
{
    public function getId(): string
    {
        return 'chatter';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(in: $this->getPluginBasePath('/Filament/Resources'), for: 'Webkul\\Chatter\\Filament\\Resources')
            ->discoverPages(in: $this->getPluginBasePath('/Filament/Pages'), for: 'Webkul\\Chatter\\Filament\\Pages')
            ->discoverClusters(in: $this->getPluginBasePath('/Filament/Clusters'), for: 'Webkul\\Chatter\\Filament\\Clusters')
            ->discoverClusters(in: $this->getPluginBasePath('/Filament/Widgets'), for: 'Webkul\\Chatter\\Filament\\Widgets');
    }

    public function boot(Panel $panel): void
    {
        //
    }

    protected function getPluginBasePath($path = null): string
    {
        $reflector = new ReflectionClass(get_class($this));

        return dirname($reflector->getFileName()).($path ?? '');
    }
}
