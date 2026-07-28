<?php

namespace Webkul\Support;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use RuntimeException;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Security\Livewire\AcceptInvitation;
use Webkul\Security\Models\Role;
use Webkul\Security\Policies\RolePolicy;
use Webkul\Support\Database\Dialects\DatabaseDialect;
use Webkul\Support\Database\Dialects\MySqlDialect;
use Webkul\Support\Database\Dialects\PostgresDialect;
use Webkul\Support\Livewire\QuickNavigation;
use Webkul\Support\Traits\HasFilamentDefaults;
use Webkul\Support\Traits\HasRouterMacros;
use Webkul\Support\Traits\HasRtlSupport;

class SupportServiceProvider extends PackageServiceProvider
{
    use HasFilamentDefaults;
    use HasRouterMacros;
    use HasRtlSupport;

    public static string $name = 'support';

    public static string $viewNamespace = 'support';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasViews()
            ->hasTranslations()
            ->hasRoutes(['api', 'web'])
            ->hasMigrations([
                '2024_11_05_105102_create_plugins_table',
                '2024_11_05_105112_create_plugin_dependencies_table',
                '2024_12_06_061927_create_currencies_table',
                '2024_12_10_092651_create_countries_table',
                '2024_12_10_092657_create_states_table',
                '2024_12_10_092657_create_companies_table',
                '2024_12_10_100944_create_user_allowed_companies_table',
                '2024_12_10_101420_create_banks_table',
                '2024_12_12_114620_create_activity_plans_table',
                '2024_12_12_115256_create_activity_types_table',
                '2024_12_12_115728_create_activity_plan_templates_table',
                '2024_12_17_082318_create_activity_type_suggestions_table',
                '2025_01_03_061444_create_email_templates_table',
                '2025_01_03_061445_create_email_logs_table',
                '2025_01_03_105625_create_unit_of_measure_categories_table',
                '2025_01_03_105627_create_unit_of_measures_table',
                '2025_01_07_125015_add_partner_id_to_companies_table',
                '2025_01_09_111545_create_utm_mediums_table',
                '2025_01_09_114324_create_utm_sources_table',
                '2025_01_10_094256_create_utm_stages_table',
                '2025_01_10_094325_create_utm_campaigns_table',
                '2025_04_04_061507_add_address_columns_in_companies_table',
                '2025_04_04_062023_alter_companies_table',
                '2025_08_08_104317_alter_utm_stages_table',
                '2025_08_08_104814_alter_utm_campaigns_table',
                '2025_10_10_080114_create_currency_rates_table',
                '2025_11_14_102615_alter_currency_rates_table',
                '2026_03_18_000001_alter_unit_of_measures_factor_precision',
                '2026_04_02_000001_create_calendars_table',
                '2026_04_29_065935_add_resource_columns_in_calendar_leaves_table',
                '2026_05_01_065935_add_resource_columns_in_calendar_attendances_table',
                '2026_07_10_000000_fix_unit_of_measures_factor_precision',
                '2026_07_16_000001_create_quick_navigation_favorites_table',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2026_06_12_000001_create_brand_settings',
            ])
            ->runsSettings()
            ->hasSeeder('Webkul\\Support\\Database\\Seeders\\DatabaseSeeder');
    }

    public function packageBooted(): void
    {
        Livewire::component('accept-invitation', AcceptInvitation::class);

        Livewire::component('quick-navigation', QuickNavigation::class);

        Gate::policy(Role::class, RolePolicy::class);

        $this->app['router']->get('cache/{filename}', [
            'uses' => 'Webkul\Support\Http\Controllers\ImageCacheController@getImage',
            'as'   => 'image_cache',
        ])->where(['filename' => '[ \w\\.\\/\\-\\@\(\)\=]+']);

        FilamentAsset::register([
            Css::make('support', __DIR__.'/../resources/dist/support.css'),
        ], 'support');

        $this->registerFilamentDefaults();

        $this->registerRtlSupport();
    }

    public function packageRegistered(): void
    {
        $this->app->scoped(SettingsRegistry::class);

        $this->app->singleton(DatabaseDialect::class, function () {
            $driver = DB::connection()->getDriverName();

            return match ($driver) {
                'pgsql' => new PostgresDialect,
                'mysql', 'mariadb' => new MySqlDialect,
                default => throw new RuntimeException(
                    "No DatabaseDialect implementation is registered for the [{$driver}] database driver. ".
                    'Supported drivers: mysql, mariadb, pgsql.'
                ),
            };
        });

        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(SupportPlugin::make());
        });

        $this->registerLanguageSwitch();

        $this->registerHooks();

        $this->registerRouterMacros();
    }
}
