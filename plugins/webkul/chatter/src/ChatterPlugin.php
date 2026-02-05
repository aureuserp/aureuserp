<?php

namespace Webkul\Chatter;

use Filament\Contracts\Plugin;
use Filament\Panel;

class ChatterPlugin implements Plugin
{
    public function getId(): string
    {
        return 'chatter';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(
                in: __DIR__.'/Filament/Resources',
                for: 'Webkul\\Chatter\\Filament\\Resources'
            )
            ->discoverPages(
                in: __DIR__.'/Filament/Pages',
                for: 'Webkul\\Chatter\\Filament\\Pages'
            )
            ->discoverClusters(
                in: __DIR__.'/Filament/Clusters',
                for: 'Webkul\\Chatter\\Filament\\Clusters'
            )
            ->discoverClusters(
                in: __DIR__.'/Filament/Widgets',
                for: 'Webkul\\Chatter\\Filament\\Widgets'
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
