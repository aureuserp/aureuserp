<?php

namespace Webkul\ProductReview;

use Filament\Contracts\Plugin;
use Filament\Panel;
use ReflectionClass;
use Webkul\Support\Package;

class ProductReviewPlugin implements Plugin
{
    public function getId(): string
    {
        return 'product-reviews';
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
            ->when($panel->getId() == 'customer', function (Panel $panel) {
                $panel
                    ->discoverResources(in: $this->getPluginBasePath('/Filament/Customer/Resources'), for: 'Webkul\\ProductReview\\Filament\\Customer\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Customer/Pages'), for: 'Webkul\\ProductReview\\Filament\\Customer\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Customer/Clusters'), for: 'Webkul\\ProductReview\\Filament\\Customer\\Clusters')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Customer/Widgets'), for: 'Webkul\\ProductReview\\Filament\\Customer\\Widgets');
            })
            ->when($panel->getId() == 'admin', function (Panel $panel) {
                $panel
                    ->discoverResources(in: $this->getPluginBasePath('/Filament/Admin/Resources'), for: 'Webkul\\ProductReview\\Filament\\Admin\\Resources')
                    ->discoverPages(in: $this->getPluginBasePath('/Filament/Admin/Pages'), for: 'Webkul\\ProductReview\\Filament\\Admin\\Pages')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Admin/Clusters'), for: 'Webkul\\ProductReview\\Filament\\Admin\\Clusters')
                    ->discoverClusters(in: $this->getPluginBasePath('/Filament/Admin/Widgets'), for: 'Webkul\\ProductReview\\Filament\\Admin\\Widgets');
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