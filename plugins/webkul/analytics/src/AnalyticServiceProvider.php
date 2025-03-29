<?php

namespace Webkul\Analytic;

use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;

class AnalyticServiceProvider extends PackageServiceProvider
{
    public static string $name = 'analytics';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasTranslations()
            ->hasMigrations([
                '2024_12_18_131844_create_analytic_records_table',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        //
    }
}
