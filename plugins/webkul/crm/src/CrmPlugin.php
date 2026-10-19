<?php

namespace Webkul\Crm;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;
use Wezlo\FilamentKanban\FilamentKanbanPlugin;

class CrmPlugin implements Plugin
{
    public function getId(): string
    {
        return 'crm';
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
            ->when($panel->getId() == 'admin', function (Panel $panel): void {
                $panel
                    ->discoverResources(
                        in: __DIR__.'/Filament/Resources',
                        for: 'Webkul\\Crm\\Filament\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Pages',
                        for: 'Webkul\\Crm\\Filament\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Clusters',
                        for: 'Webkul\\Crm\\Filament\\Clusters'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Widgets',
                        for: 'Webkul\\Crm\\Filament\\Widgets'
                    );

                if (! $panel->hasPlugin('filament-kanban')) {
                    $panel->plugin(FilamentKanbanPlugin::make());
                }
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
