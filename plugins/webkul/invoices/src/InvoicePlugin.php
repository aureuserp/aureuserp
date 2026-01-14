<?php

namespace Webkul\Invoice;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Webkul\Invoice\Filament\Clusters\Settings\Pages\Products;
use Webkul\PluginManager\Package;

class InvoicePlugin implements Plugin
{
    public function getId(): string
    {
        return 'invoices';
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
                        for: 'Webkul\\Invoice\\Filament\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Pages',
                        for: 'Webkul\\Invoice\\Filament\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Clusters',
                        for: 'Webkul\\Invoice\\Filament\\Clusters'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Widgets',
                        for: 'Webkul\\Invoice\\Filament\\Widgets'
                    )
                    ->navigationItems([
                        NavigationItem::make('settings')
                            ->label(fn () => __('invoices::app.navigation.settings.label'))
                            ->url(fn () => Products::getUrl())
                            ->group('Invoices')
                            ->sort(4)
                            ->visible(fn () => Products::canAccess()),
                    ]);
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
