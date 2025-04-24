<?php

namespace Custom\Security;

use Webkul\Support\Package;
use Webkul\Security\SecurityServiceProvider;

class CustomSecurityServiceProvider extends SecurityServiceProvider
{
    public static string $name = 'security';

    public static string $viewNamespace = 'security';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name);
            // ->isCore()
            // ->hasViews()
            // ->hasTranslations()
            // ->hasRoute('../../../webkul/security/routes/web')
            // ->runsMigrations()
            // ->hasMigrations([
            //     '../../../webkul/security/database/migrations/2024_11_11_112529_create_user_invitations_table',
            //     '../../../webkul/security/database/migrations/2024_11_12_125715_create_teams_table',
            //     '../../../webkul/security/database/migrations/2024_11_12_130019_create_user_team_table',
            //     '../../../webkul/security/database/migrations/2024_12_10_101127_add_default_company_id_column_to_users_table',
            //     '../../../webkul/security/database/migrations/2024_12_13_130906_add_partner_id_to_users_table',
            // ])
            // ->hasSettings([
            //     '../../../webkul/security/database/settings/2024_11_05_042358_create_user_settings',
            // ])
            // ->runsSettings();
    }
}
