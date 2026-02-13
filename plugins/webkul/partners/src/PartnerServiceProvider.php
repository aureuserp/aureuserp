<?php

namespace Webkul\Partner;

use Filament\Panel;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class PartnerServiceProvider extends PackageServiceProvider
{
    public static string $name = 'partners';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasTranslations()
            ->hasRoutes(['api'])
            ->hasMigrations([
                '2024_12_11_101127_create_partners_industries_table',
                '2024_12_11_101127_create_partners_titles_table',
                '2024_12_11_101220_create_partners_partners_table',
                '2024_12_11_101420_create_partners_bank_accounts_table',
                '2024_12_11_101927_create_partners_tags_table',
                '2024_12_11_111929_create_partners_partner_tag_table',
                '2025_03_28_115218_add_address_columns_in_partners_partners_table',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        //
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(PartnerPlugin::make());
        });
    }
}
