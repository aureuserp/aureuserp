<?php

namespace Webkul\Crm;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Livewire;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\Crm\Filament\Support\Reports\Widgets\ReportChart;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Services\ActivityLogRecorder;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Sale\Models\Team;

class CrmServiceProvider extends PackageServiceProvider
{
    public static string $name = 'crm';

    public static string $viewNamespace = 'crm';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2026_10_19_000001_create_crm_tags_table',
                '2026_10_19_000002_create_crm_lost_reasons_table',
                '2026_10_19_000003_create_crm_stages_table',
                '2026_10_19_000004_create_crm_leads_table',
                '2026_10_19_000005_add_lead_columns_to_crm_leads_table',
                '2026_10_21_000006_create_crm_activity_logs_table',
            ])
            ->runsMigrations()
            ->hasDependencies([
                'contacts',
                'sales',
            ])
            ->hasSeeder('Webkul\\Crm\\Database\\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {
                $command->endWith(function (): void {
                    ChatterCleanupService::purgeForModels([Lead::class]);
                });
            })
            ->icon('crm');
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make('crm', __DIR__.'/../resources/dist/crm.css'),
        ], 'crm');

        Livewire::component('crm-report-chart', ReportChart::class);

        $this->app->make(ActivityLogRecorder::class)->register();

        Team::resolveRelationUsing(
            'opportunities',
            fn (Team $team): HasMany => $team->hasMany(Lead::class, 'team_id'),
        );
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ActivityLogRecorder::class);

        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(CrmPlugin::make());
        });
    }
}
