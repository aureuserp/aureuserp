<?php

namespace Webkul\Field;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FieldsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'fields';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        $panel
            ->when($panel->getId() == 'admin', function (Panel $panel) {
                $panel
                    ->discoverResources(
                        in: __DIR__.'/Filament/Resources',
                        for: 'Webkul\\Field\\Filament\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Pages',
                        for: 'Webkul\\Field\\Filament\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Clusters',
                        for: 'Webkul\\Field\\Filament\\Clusters'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Widgets',
                        for: 'Webkul\\Field\\Filament\\Widgets'
                    );
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
