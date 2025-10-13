<?php

namespace Webkul\Sale;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use ReflectionClass;
use Webkul\Sale\Filament\Clusters\Settings\Pages\ManageProducts;
use Webkul\Support\Package;

class SalePlugin implements Plugin
{
    public function getId(): string
    {
        return 'sales';
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
                $panel->discoverResources(in: $this->getPluginBasePath('/Filament/Resources'), for: 'Webkul\\Sale\\Filament\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Pages'), for: 'Webkul\\Sale\\Filament\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Clusters'), for: 'Webkul\\Sale\\Filament\\Clusters')
                    ->discoverWidgets(in: $this->getPluginBasePath('/Filament/Widgets'), for: 'Webkul\\Sale\\Filament\\Widgets')
                    ->navigationItems([
                        NavigationItem::make('settings')
                            ->label(fn () => __('sales::app.navigation.settings.label'))
                            ->url(fn () => ManageProducts::getUrl())
                            ->group('Sales')
                            ->sort(4)
                            ->visible(fn () => ManageProducts::canAccess()),
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

        return dirname($reflector->getFileName()).($path ?? '');
    }
}
