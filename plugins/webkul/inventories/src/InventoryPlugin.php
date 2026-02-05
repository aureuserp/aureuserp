<?php

namespace Webkul\Inventory;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Webkul\Inventory\Filament\Clusters\Settings\Pages\ManageOperations;
use Webkul\PluginManager\Package;

class InventoryPlugin implements Plugin
{
    public function getId(): string
    {
        return 'inventories';
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
                        for: 'Webkul\\Inventory\\Filament\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Pages',
                        for: 'Webkul\\Inventory\\Filament\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Clusters',
                        for: 'Webkul\\Inventory\\Filament\\Clusters'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Widgets',
                        for: 'Webkul\\Inventory\\Filament\\Widgets'
                    )
                    ->navigationItems([
                        NavigationItem::make('settings')
                            ->label(fn () => __('inventories::app.navigation.settings.label'))
                            ->url(fn () => ManageOperations::getUrl())
                            ->group('Inventory')
                            ->sort(4)
                            ->visible(fn () => ManageOperations::canAccess()),
                    ]);
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
