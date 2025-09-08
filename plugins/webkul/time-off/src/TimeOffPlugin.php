<?php

namespace Webkul\TimeOff;

use Filament\Contracts\Plugin;
use Filament\Panel;
use ReflectionClass;
use Webkul\FullCalendar\FullCalendarPlugin;
use Webkul\Support\Package;

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
                $panel->discoverResources(in: $this->getPluginBasePath('/Filament/Resources'), for: 'Webkul\\TimeOff\\Filament\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Pages'), for: 'Webkul\\TimeOff\\Filament\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Clusters'), for: 'Webkul\\TimeOff\\Filament\\Clusters')
                    ->discoverWidgets(in: $this->getPluginBasePath('/Filament/Widgets'), for: 'Webkul\\TimeOff\\Filament\\Widgets');
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

    protected function getPluginBasePath($path = null): string
    {
        $reflector = new ReflectionClass(get_class($this));

        return dirname($reflector->getFileName()).($path ?? '');
    }
}
