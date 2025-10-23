<?php

namespace Webkul\Accounting;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Webkul\Accounting\Filament\Clusters\Settings\Pages\ManageProducts;
use Filament\Panel;
use ReflectionClass;
use Webkul\Support\Package;

class AccountingPlugin implements Plugin
{
    public function getId(): string
    {
        return 'accounting';
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
                $panel->discoverResources(in: $this->getPluginBasePath('/Filament/Resources'), for: 'Webkul\\Accounting\\Filament\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Pages'), for: 'Webkul\\Accounting\\Filament\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Clusters'), for: 'Webkul\\Accounting\\Filament\\Clusters')
                    ->discoverWidgets(in: $this->getPluginBasePath('/Filament/Widgets'), for: 'Webkul\\Accounting\\Filament\\Widgets')
                    ->navigationItems([
                        NavigationItem::make('settings')
                            ->label(fn () => __('accounting::app.navigation.settings.label'))
                            ->url(fn () => ManageProducts::getUrl())
                            ->group(fn () => __('accounting::app.navigation.settings.group'))
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

        return dirname($reflector->getFileName()).($path ?? '');
    }
}
