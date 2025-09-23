<?php

namespace Webkul\ProductReview;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Webkul\Support\Console\Commands\InstallCommand;
use Webkul\Support\Console\Commands\UninstallCommand;
use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;

class ProductReviewServiceProvider extends PackageServiceProvider
{
    public static string $name = 'product-reviews';

    public static string $viewNamespace = 'product-reviews';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2025_09_23_135130_create_product-reviews_table',
            ])
            ->runsMigrations()
            ->hasSettings([
            ])
            ->runsSettings()
            ->hasDependencies([
                'website',
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {});
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('product-reviews', __DIR__.'/../resources/dist/product-reviews.css'),
        ], 'product-reviews');
    }
}