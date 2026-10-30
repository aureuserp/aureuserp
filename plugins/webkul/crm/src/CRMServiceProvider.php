<?php

namespace Webkul\Crm;

use Filament\Panel;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use Filament\View\PanelsRenderHook;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\HtmlString;
use Livewire\Livewire;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\Crm\Console\Commands\AssignLeads;
use Webkul\Crm\Console\Commands\FetchInboundEmails;
use Webkul\Crm\Console\Commands\ProcessVisitsToLeads;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ListLeads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\SimilarLeads;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages\ListForecast;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages\ListActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\CustomerResource\Pages\ListCustomers;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\SimilarOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ListQuotations;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages\ManageTeamOpportunities;
use Webkul\Crm\Filament\Support\Reports\Widgets\ReportChart;
use Webkul\Crm\Filament\Support\VersionedJs;
use Webkul\Crm\Listeners\CreateLeadFromVisitor;
use Webkul\Crm\Livewire\QuotationInvoiceSummary;
use Webkul\Crm\Models\CRMPartner;
use Webkul\Crm\Models\CRMTeam;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\MailAlias;
use Webkul\Crm\Models\TeamMember;
use Webkul\Crm\Services\ActivityLogRecorder;
use Webkul\Invoice\Livewire\InvoiceSummary;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\Team;
use Webkul\Support\Support\VersionedCss;
use Webkul\Website\Events\VisitorTracked;

class CRMServiceProvider extends PackageServiceProvider
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
                '2026_10_26_000028_create_crm_reveal_rules_table',
                '2026_10_26_000029_create_crm_reveal_rule_countries_table',
                '2026_10_26_000030_create_crm_reveal_rule_states_table',
                '2026_10_26_000031_create_crm_reveal_rule_tags_table',
                '2026_10_26_000032_create_crm_reveal_views_table',
                '2026_10_26_000033_create_crm_lead_visitor_rel_table',
                '2026_10_26_000034_add_reveal_columns_to_crm_leads_table',
                '2026_10_26_000036_create_crm_lead_industries_table',
                '2026_10_26_000037_create_crm_lead_roles_table',
                '2026_10_26_000038_create_crm_lead_seniorities_table',
                '2026_10_26_000039_create_crm_reveal_rule_industries_table',
                '2026_10_26_000040_create_crm_reveal_rule_roles_table',
                '2026_10_26_000041_add_contact_columns_to_crm_reveal_rules_table',
                '2026_10_26_000042_add_color_to_crm_leads_table',
                '2026_10_28_000043_change_color_to_hex_on_crm_leads_table',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2026_10_22_000011_create_crm_settings',
                '2026_10_22_000012_add_use_leads_to_crm_settings',
                '2026_10_22_000025_add_use_recurring_revenues_to_crm_settings',
                '2026_10_23_000027_add_lead_scoring_to_crm_settings',
                '2026_10_26_000035_add_visits_to_leads_to_crm_settings',
                '2026_10_27_000043_create_outgoing_mail_settings',
                '2026_10_27_000044_create_sms_settings',
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
            VersionedCss::make('crm', __DIR__.'/../resources/dist/crm.css'),
            VersionedJs::make('crm-kanban-board', __DIR__.'/../resources/js/kanban-board.js')->loadedOnRequest(),
        ], 'crm');

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): HtmlString => new HtmlString('<script src="'.e(FilamentAsset::getScriptSrc('crm-kanban-board', 'crm')).'"></script>'),
            scopes: [
                ListLeads::class,
                SimilarLeads::class,
                ListOpportunities::class,
                SimilarOpportunities::class,
                ListQuotations::class,
                ListActivities::class,
                ListCustomers::class,
                ManageTeamOpportunities::class,
                ListForecast::class,
            ],
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER,
            fn (): string|View => Livewire::current()->isViewLayout(ViewLayout::KANBAN) ? view('crm::filament.kanban.board-shimmer') : '',
            scopes: [ListLeads::class, ListQuotations::class, ListCustomers::class],
        );

        FilamentView::registerRenderHook(
            TablesRenderHook::TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER,
            fn (): string|View => method_exists($page = Livewire::current(), 'getViewLayouts')
                ? view('crm::filament.view-layout-switcher', ['layouts' => $page->getViewLayouts(), 'activeLayout' => $page->getViewLayout()])
                : '',
        );

        FilamentView::registerRenderHook(
            TablesRenderHook::CONTENT_BEFORE,
            fn (): string|View => method_exists($page = Livewire::current(), 'getViewLayouts')
                ? view('crm::filament.view-layout-shimmer', ['layouts' => $page->getViewLayouts(), 'activeLayout' => $page->getViewLayout()])
                : '',
        );

        Livewire::component('crm-report-chart', ReportChart::class);

        if (class_exists(InvoiceSummary::class)) {
            Livewire::component('crm-quotation-invoice-summary', QuotationInvoiceSummary::class);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([AssignLeads::class, FetchInboundEmails::class, ProcessVisitsToLeads::class]);
        }

        $this->registerListeners();

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
            fn (Partner $partner): HasOne => $partner->hasOne(CRMPartner::class, 'partner_id'),
        );

        Partner::resolveRelationUsing(
            'crmOpportunities',
            fn (Partner $partner): HasMany => $partner->hasMany(Lead::class, 'partner_id')->where('type', LeadType::OPPORTUNITY),
        );

        Partner::resolveRelationUsing(
            'crmSaleOrders',
            fn (Partner $partner): HasMany => $partner->hasMany(Order::class, 'partner_id'),
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
            fn (Team $team): HasOne => $team->hasOne(CRMTeam::class, 'team_id'),
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

    protected function registerListeners(): void
    {
        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        $this->app->make(ActivityLogRecorder::class)->register();

        if (class_exists(VisitorTracked::class)) {
            Event::listen(VisitorTracked::class, CreateLeadFromVisitor::class);
        }
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ActivityLogRecorder::class);

        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(CRMPlugin::make());
        });
    }
}
