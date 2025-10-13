<?php

namespace Webkul\Support;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Webkul\Security\Livewire\AcceptInvitation;
use Webkul\Security\Models\Role;
use Webkul\Security\Policies\RolePolicy;
use Webkul\Support\Console\Commands\InstallERP;

class SupportServiceProvider extends PackageServiceProvider
{
    public static string $name = 'support';

    public static string $viewNamespace = 'support';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasViews()
            ->hasTranslations()
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
                '2024_12_23_103137_create_activity_logs_table',
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
            ])
            ->runsMigrations()
            ->hasCommands([
                InstallERP::class,
            ]);
    }

    public function packageBooted(): void
    {
        include __DIR__ . '/helpers.php';

        Livewire::component('accept-invitation', AcceptInvitation::class);

        Gate::policy(Role::class, RolePolicy::class);

        Event::listen('aureus.installed', 'Webkul\Support\Listeners\Installer@installed');

        /**
         * Route to access template applied image file
         */
        $this->app['router']->get('cache/{filename}', [
            'uses' => 'Webkul\Support\Http\Controllers\ImageCacheController@getImage',
            'as'   => 'image_cache',
        ])->where(['filename' => '[ \w\\.\\/\\-\\@\(\)\=]+']);

        FilamentAsset::register([
            Css::make('support', __DIR__ . '/../resources/dist/support.css'),
        ], 'support');

        $this->app->make(PermissionManager::class)->managePermissions();
    }

    public function packageRegistered(): void
    {
        $this->registerHooks();

        $this->app->singleton(PermissionManager::class, fn () => new PermissionManager());
    }

    protected function registerHooks(): void
    {
        $version = '1.1.0';

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_PROFILE_BEFORE,
            fn(): string => Blade::render(<<<'BLADE'
                <x-filament::dropdown.list>
                    <x-filament::dropdown.list.item>
                        <div class="flex items-center gap-2">
                            <img
                                src="{{ url('cache/logo.png') }}"
                                width="24"
                                height="24"
                            />

                            {{ __('support::support.version', ['version' => $version]) }} 
                        </div>
                    </x-filament::dropdown.list.item>
                </x-filament::dropdown.list>
            BLADE, [
                'version' => $version,
            ]),
        );
    }
}
