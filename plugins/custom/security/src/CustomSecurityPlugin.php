<?php

namespace Custom\Security;

use Filament\Facades\Filament;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\Security\Settings\UserSettings;
use Webkul\Security\SecurityPlugin;

class CustomSecurityPlugin implements Plugin
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
                    ->discoverResources(in: $this->getPluginBasePath('/Filament/Resources'), for: 'Custom\\Security\\Filament\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Pages'), for: 'Custom\\Security\\Filament\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Clusters'), for: 'Custom\\Security\\Filament\\Clusters')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Widgets'), for: 'Custom\\Security\\Filament\\Widgets');
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

    protected function getPluginBasePath($path = null): string
    {
        $reflector = new \ReflectionClass(get_class($this));

        return dirname($reflector->getFileName()) . ($path ?? '');
    }
}
