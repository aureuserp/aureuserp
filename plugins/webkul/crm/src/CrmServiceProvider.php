<?php

namespace Webkul\Crm;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Livewire\Livewire;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\Crm\Console\Commands\AssignLeads;
use Webkul\Crm\Console\Commands\FetchInboundEmails;
use Webkul\Crm\Filament\Support\Reports\Widgets\ReportChart;
use Webkul\Crm\Models\CrmPartner;
use Webkul\Crm\Models\CrmTeam;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\MailAlias;
use Webkul\Crm\Models\TeamMember;
use Webkul\Crm\Services\ActivityLogRecorder;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Sale\Models\Order;
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
                '2026_10_22_000007_add_similarity_columns_to_crm_leads_table',
                '2026_10_22_000008_create_crm_lead_merges_table',
                '2026_10_22_000009_create_crm_teams_table',
                '2026_10_22_000010_create_crm_team_members_table',
                '2026_10_22_000013_create_crm_mail_servers_table',
                '2026_10_22_000014_create_crm_mail_aliases_table',
                '2026_10_22_000015_create_crm_inbound_emails_table',
                '2026_10_22_000017_add_opportunity_id_to_sales_orders_table',
                '2026_10_22_000018_create_crm_lead_conversions_tables',
                '2026_10_22_000019_create_crm_lead_mass_conversions_tables',
                '2026_10_22_000020_create_crm_partner_levels_tables',
                '2026_10_22_000021_rename_crm_lead_tags_to_crm_tag_rel',
                '2026_10_22_000022_create_crm_stage_team_rel_table',
                '2026_10_22_000023_create_crm_quotation_partners_table',
                '2026_10_22_000024_create_crm_recurring_plans_table',
                '2026_10_23_000026_create_crm_lead_scoring_tables',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2026_10_22_000011_create_crm_settings',
                '2026_10_22_000012_add_use_leads_to_crm_settings',
                '2026_10_22_000025_add_use_recurring_revenues_to_crm_settings',
                '2026_10_23_000027_add_lead_scoring_to_crm_settings',
            ])
            ->runsSettings()
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

        if ($this->app->runningInConsole()) {
            $this->commands([AssignLeads::class, FetchInboundEmails::class]);
        }

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command('crm:assign-leads --scheduled')
                ->everyMinute()
                ->withoutOverlapping()
                ->when(fn (): bool => Package::isPluginInstalled('crm'));

            $schedule->command('crm:fetch-mail')
                ->everyMinute()
                ->withoutOverlapping()
                ->runInBackground()
                ->when(fn (): bool => Package::isPluginInstalled('crm'));
        });

        Partner::resolveRelationUsing(
            'crmPartner',
            fn (Partner $partner): HasOne => $partner->hasOne(CrmPartner::class, 'partner_id'),
        );

        Order::resolveRelationUsing(
            'opportunity',
            fn (Order $order): BelongsTo => $order->belongsTo(Lead::class, 'opportunity_id')->withTrashed(),
        );

        Team::resolveRelationUsing(
            'opportunities',
            fn (Team $team): HasMany => $team->hasMany(Lead::class, 'team_id'),
        );

        Team::resolveRelationUsing(
            'crmTeam',
            fn (Team $team): HasOne => $team->hasOne(CrmTeam::class, 'team_id'),
        );

        Team::resolveRelationUsing(
            'crmAlias',
            fn (Team $team): HasOne => $team->hasOne(MailAlias::class, 'team_id'),
        );

        Team::resolveRelationUsing(
            'crmMembers',
            fn (Team $team): HasMany => $team->hasMany(TeamMember::class, 'team_id'),
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
