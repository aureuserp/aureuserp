<?php

namespace Webkul\Purchase;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

class PurchasePlugin implements Plugin
{
    public function getId(): string
    {
        return 'purchases';
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
            ->when($panel->getId() == 'customer', function (Panel $panel) {
                $panel
                    ->discoverResources(
                        in: __DIR__.'/Filament/Customer/Resources',
                        for: 'Webkul\\Purchase\\Filament\\Customer\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Customer/Pages',
                        for: 'Webkul\\Purchase\\Filament\\Customer\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Customer/Clusters',
                        for: 'Webkul\\Purchase\\Filament\\Customer\\Clusters'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Customer/Widgets',
                        for: 'Webkul\\Purchase\\Filament\\Customer\\Widgets'
                    );
            })
            ->when($panel->getId() == 'admin', function (Panel $panel) {
                $panel
                    ->discoverResources(
                        in: __DIR__.'/Filament/Admin/Resources',
                        for: 'Webkul\\Purchase\\Filament\\Admin\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Admin/Pages',
                        for: 'Webkul\\Purchase\\Filament\\Admin\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Admin/Clusters',
                        for: 'Webkul\\Purchase\\Filament\\Admin\\Clusters'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Admin/Widgets',
                        for: 'Webkul\\Purchase\\Filament\\Admin\\Widgets'
                    );
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
