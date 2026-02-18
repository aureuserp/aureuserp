<?php

namespace Webkul\Security;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\Security\Settings\UserSettings;

class SecurityPlugin implements Plugin
{
    public function getId(): string
    {
        return 'security';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        $panel
            ->when($panel->getId() == 'admin', function (Panel $panel) {
                $panel->passwordReset()
                    ->discoverResources(
                        in: __DIR__.'/Filament/Resources',
                        for: 'Webkul\\Security\\Filament\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Pages',
                        for: 'Webkul\\Security\\Filament\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Clusters',
                        for: 'Webkul\\Security\\Filament\\Clusters'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Widgets',
                        for: 'Webkul\\Security\\Filament\\Widgets'
                    );
            });

        if (
            ! app()->runningInConsole() &&
            ! app(UserSettings::class)?->enable_reset_password
        ) {
            $panel->passwordReset(false);
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
