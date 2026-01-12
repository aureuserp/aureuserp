<?php

namespace Webkul\Employee;

use Filament\Contracts\Plugin;
use Filament\Panel;

class EmployeePlugin implements Plugin
{
    public function getId(): string
    {
        return 'employees';
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
                        for: 'Webkul\\Employee\\Filament\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Pages',
                        for: 'Webkul\\Employee\\Filament\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Clusters',
                        for: 'Webkul\\Employee\\Filament\\Clusters'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Widgets',
                        for: 'Webkul\\Employee\\Filament\\Widgets'
                    );
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
