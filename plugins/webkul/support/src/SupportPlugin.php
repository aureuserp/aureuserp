<?php

namespace Webkul\Support;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\HtmlString;
use ReflectionClass;

class SupportPlugin implements Plugin
{
    public function getId(): string
    {
        return 'support';
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
                    ->discoverResources(in: $this->getPluginBasePath('/Filament/Resources'), for: 'Webkul\\Support\\Filament\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Pages'), for: 'Webkul\\Support\\Filament\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Clusters'), for: 'Webkul\\Support\\Filament\\Clusters')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Widgets'), for: 'Webkul\\Support\\Filament\\Widgets');
            });
    }

    public function boot(Panel $panel): void
    {
        FilamentView::registerRenderHook(
            name: 'panels::scripts.before',
            hook: fn () => new HtmlString(html: "
            <script>
                document.addEventListener('livewire:navigated', function() {
                    setTimeout(() => {
                        const activeSidebarItem = document.querySelector('nav .fi-sidebar-item-active');

                        const sidebarWrapper = document.querySelector('nav.fi-sidebar-nav');
    
                        sidebarWrapper.scrollTo(0, activeSidebarItem.offsetTop - 250);
                    }, 0);
                });
            </script>
        "));
    }

    protected function getPluginBasePath($path = null): string
    {
        $reflector = new ReflectionClass(get_class($this));

        return dirname($reflector->getFileName()).($path ?? '');
    }
}
