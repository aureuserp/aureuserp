<?php

namespace Webkul\Recruitment;

use Filament\Contracts\Plugin;
use Filament\Panel;
use ReflectionClass;
use Webkul\Support\Package;

class RecruitmentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'recruitments';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel
            ->when($panel->getId() == 'admin', function (Panel $panel) {
                $panel->discoverResources(in: $this->getPluginBasePath('/Filament/Resources'), for: 'Webkul\\Recruitment\\Filament\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Pages'), for: 'Webkul\\Recruitment\\Filament\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Clusters'), for: 'Webkul\\Recruitment\\Filament\\Clusters')
                    ->discoverWidgets(in: $this->getPluginBasePath('/Filament/Widgets'), for: 'Webkul\\Recruitment\\Filament\\Widgets');
            });
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
