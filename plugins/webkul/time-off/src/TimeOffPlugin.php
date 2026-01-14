<?php

namespace Webkul\TimeOff;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\FullCalendar\FullCalendarPlugin;
use Webkul\PluginManager\Package;

class TimeOffPlugin implements Plugin
{
    public function getId(): string
    {
        return 'time-off';
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
                        for: 'Webkul\\TimeOff\\Filament\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Pages',
                        for: 'Webkul\\TimeOff\\Filament\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Clusters',
                        for: 'Webkul\\TimeOff\\Filament\\Clusters'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Widgets',
                        for: 'Webkul\\TimeOff\\Filament\\Widgets'
                    );
            })
            ->plugin(
                FullCalendarPlugin::make()
                    ->selectable()
                    ->editable(true)
                    ->setPlugins(['multiMonth'])
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
