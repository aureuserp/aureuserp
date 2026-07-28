<?php

namespace Webkul\Maintenance;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\FullCalendar\FullCalendarPlugin;
use Webkul\PluginManager\Package;

class MaintenancePlugin implements Plugin
{
    public function getId(): string
    {
        return 'maintenance';
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
                        for: 'Webkul\\Maintenance\\Filament\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Pages',
                        for: 'Webkul\\Maintenance\\Filament\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Clusters',
                        for: 'Webkul\\Maintenance\\Filament\\Clusters'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Widgets',
                        for: 'Webkul\\Maintenance\\Filament\\Widgets'
                    );
            })
            ->plugin(
                FullCalendarPlugin::make()
                    ->selectable()
                    ->setPlugins(['multiMonth'])
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
