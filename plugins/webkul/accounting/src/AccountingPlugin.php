<?php

namespace Webkul\Accounting;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Webkul\Accounting\Filament\Clusters\Settings\Pages\ManageProducts;
use Webkul\PluginManager\Package;

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
                $panel->discoverResources(
                    in: __DIR__.'/Filament/Resources',
                    for: 'Webkul\\Accounting\\Filament\\Resources'
                )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Pages',
                        for: 'Webkul\\Accounting\\Filament\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Clusters',
                        for: 'Webkul\\Accounting\\Filament\\Clusters'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Widgets',
                        for: 'Webkul\\Accounting\\Filament\\Widgets'
                    )
                    ->navigationItems([
                        NavigationItem::make('settings')
                            ->label(fn () => __('accounting::app.navigation.settings.label'))
                            ->url(fn () => ManageProducts::getUrl())
                            ->group(fn () => __('accounting::app.navigation.settings.group'))
                            ->sort(7)
                            ->visible(fn () => ManageProducts::canAccess()),
                    ]);
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
