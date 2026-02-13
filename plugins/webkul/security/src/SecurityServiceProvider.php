<?php

namespace Webkul\Security;

use Filament\Panel;
use Illuminate\Foundation\AliasLoader;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Security\Facades\Bouncer as BouncerFacade;

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
            ->hasRoute('api')
            ->runsMigrations()
            ->hasMigrations([
                '2024_11_11_112529_create_user_invitations_table',
                '2024_11_12_125715_create_teams_table',
                '2024_11_12_130019_create_user_team_table',
                '2024_12_10_101127_add_default_company_id_column_to_users_table',
                '2024_12_13_130906_add_partner_id_to_users_table',
                '2025_08_01_071239_alter_teams_table',
                '2025_08_01_073954_alter_users_table',
                '2025_08_21_082229_alter_roles_table',
                '2025_08_21_101646_alter_users_table',
                '2026_01_23_074142_add_multi_factor_auth_columns_in_users_table',
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
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(SecurityPlugin::make());
        });

        $loader = AliasLoader::getInstance();

        $loader->alias('bouncer', BouncerFacade::class);

        $this->app->singleton('bouncer', Bouncer::class);
        $this->app->singleton(PermissionRegistrar::class);
    }
}
