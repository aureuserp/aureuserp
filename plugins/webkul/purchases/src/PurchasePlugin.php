<?php

namespace Webkul\Purchase;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use ReflectionClass;
use Webkul\Purchase\Filament\Admin\Clusters\Settings\Pages\ManageProducts;
use Webkul\Support\Package;

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
                    ->discoverResources(in: $this->getPluginBasePath('/Filament/Customer/Resources'), for: 'Webkul\\Purchase\\Filament\\Customer\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Customer/Pages'), for: 'Webkul\\Purchase\\Filament\\Customer\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Customer/Clusters'), for: 'Webkul\\Purchase\\Filament\\Customer\\Clusters')
                    ->discoverWidgets(in: $this->getPluginBasePath('/Filament/Customer/Widgets'), for: 'Webkul\\Purchase\\Filament\\Customer\\Widgets');
            })
            ->when($panel->getId() == 'admin', function (Panel $panel) {
                $panel
                    ->discoverResources(in: $this->getPluginBasePath('/Filament/Admin/Resources'), for: 'Webkul\\Purchase\\Filament\\Admin\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Admin/Pages'), for: 'Webkul\\Purchase\\Filament\\Admin\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Admin/Clusters'), for: 'Webkul\\Purchase\\Filament\\Admin\\Clusters')
                    ->discoverWidgets(in: $this->getPluginBasePath('/Filament/Admin/Widgets'), for: 'Webkul\\Purchase\\Filament\\Admin\\Widgets')
                    ->navigationItems([
                        NavigationItem::make('settings')
                            ->label(fn () => __('purchases::app.navigation.settings.label'))
                            ->url(fn () => ManageProducts::getUrl())
                            ->group('Purchase')
                            ->sort(4)
                            ->visible(fn() => ManageProducts::canAccess()),
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
