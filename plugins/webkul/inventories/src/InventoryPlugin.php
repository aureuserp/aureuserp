<?php

namespace Webkul\Inventory;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use ReflectionClass;
use Webkul\Inventory\Filament\Clusters\Settings\Pages\ManageOperations;
use Webkul\Support\Package;

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
                    ->discoverResources(in: $this->getPluginBasePath('/Filament/Resources'), for: 'Webkul\\Inventory\\Filament\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Pages'), for: 'Webkul\\Inventory\\Filament\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Clusters'), for: 'Webkul\\Inventory\\Filament\\Clusters')
                    ->discoverWidgets(in: $this->getPluginBasePath('/Filament/Widgets'), for: 'Webkul\\Inventory\\Filament\\Widgets')
                    ->navigationItems([
                        NavigationItem::make('settings')
                            ->label(fn () => __('inventories::app.navigation.settings.label'))
                            ->url(fn () => ManageOperations::getUrl())
                            ->group('Inventory')
                            ->sort(4)
                            ->visible(fn() => ManageOperations::canAccess()),
                    ]);
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }

    protected function getPluginBasePath($path = null): string
    {
        $reflector = new ReflectionClass(get_class($this));

        return dirname($reflector->getFileName()) . ($path ?? '');
    }
}
