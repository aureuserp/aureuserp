<?php

namespace Webkul\Security;

use Illuminate\Foundation\AliasLoader;
use Webkul\Security\Facades\Bouncer as BouncerFacade;
use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;

class SecurityServiceProvider extends PackageServiceProvider
{
    public static string $name = 'security';

    public static string $viewNamespace = 'security';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasViews()
            ->hasTranslations()
            ->hasRoute('web')
            ->runsMigrations()
            ->hasMigrations([
                '2024_11_11_112529_create_user_invitations_table',
                '2024_11_12_125715_create_teams_table',
                '2024_11_12_130019_create_user_team_table',
                '2024_12_10_101127_add_default_company_id_column_to_users_table',
                '2024_12_13_130906_add_partner_id_to_users_table',
                '2025_08_01_073954_alter_users_table',
                '2025_08_01_071239_alter_teams_table',
            ])
            ->hasSettings([
                '2024_11_05_042358_create_user_settings',
                '2025_07_29_064223_create_currency_settings',
            ])
            ->runsSettings();
    }

    public function packageBooted(): void
    {
        require_once __DIR__.'/Helpers/helpers.php';
    }

    public function packageRegistered(): void
    {
        $loader = AliasLoader::getInstance();

        $loader->alias('bouncer', BouncerFacade::class);

        $this->app->singleton('bouncer', Bouncer::class);
    }
}
