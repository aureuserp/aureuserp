<?php

namespace Webkul\Project;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class ProjectPlugin implements Plugin
{
    public function getId(): string
    {
        return 'projects';
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
                $panel
                    ->discoverResources(
                        in: __DIR__.'/Filament/Resources',
                        for: 'Webkul\\Project\\Filament\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Pages',
                        for: 'Webkul\\Project\\Filament\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Clusters',
                        for: 'Webkul\\Project\\Filament\\Clusters'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Widgets',
                        for: 'Webkul\\Project\\Filament\\Widgets'
                    );
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
