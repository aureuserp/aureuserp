<?php

use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Livewire;
use Webkul\Crm\Filament\Pages\CRMDashboard;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Filament\Widgets\CRMStatsOverview;
use Webkul\Crm\Filament\Widgets\LeadsBySourceChart;
use Webkul\Crm\Filament\Widgets\PipelineByStageChart;
use Webkul\Crm\Filament\Widgets\SalespersonWonRevenueChart;
use Webkul\Crm\Filament\Widgets\TopOpportunitiesTable;
use Webkul\Crm\Filament\Widgets\WonRevenueChart;
use Webkul\Crm\Models\Lead;
use Webkul\Sale\Models\Team;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;
use Webkul\Support\Filament\Widgets\DashboardStatsWidget;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function crmDashboardPermissions(): array
{
    return [
        'page_crm_c_r_m_dashboard',
        'widget_crm_c_r_m_stats_overview',
        'widget_crm_top_opportunities_table',
    ];
}

function crmDashboardStats(array $filters = []): array
{
    $widget = Livewire::test(CRMStatsOverview::class, ['pageFilters' => $filters])->instance();

    return collect((fn () => $this->getStats())->call($widget))
        ->mapWithKeys(fn (Stat $stat): array => [(string) $stat->getLabel() => (string) $stat->getValue()])
        ->all();
}

it('shows the CRM dashboard in the dashboard menu', function () {
    CRMHelper::actingAs(crmDashboardPermissions());

    expect(CRMDashboard::getNavigationGroup())->toBe(NavigationGroup::Dashboard);

    Livewire::test(CRMDashboard::class)->assertOk();
});

it('forbids the CRM dashboard without its permission', function () {
    CRMHelper::actingAs([]);

    Livewire::test(CRMDashboard::class)->assertForbidden();
});

it('counts new leads, new opportunities, won revenue and win rate for the period', function () {
    CRMHelper::actingAs(crmDashboardPermissions());

    CRMHelper::lead();
    CRMHelper::opportunity();
    CRMHelper::wonStage();

    $won = CRMHelper::opportunity(['expected_revenue' => 1500]);
    $won->markAsWon();
    $lost = CRMHelper::opportunity();
    $lost->markAsLost(null);

    $stats = array_values(crmDashboardStats());

    expect($stats[0])->toBe('1')
        ->and($stats[1])->toBe('3')
        ->and($stats[2])->toBe(compact_money(1500, Report::getCurrencyCode()))
        ->and($stats[3])->toBe('50.0%');
});

it('limits the stats to the chosen sales teams', function () {
    CRMHelper::actingAs(crmDashboardPermissions());

    $team = Team::factory()->create(['company_id' => CRMHelper::companyId(), 'is_active' => true]);

    CRMHelper::opportunity(['team_id' => $team->id]);
    CRMHelper::opportunity();

    expect(array_values(crmDashboardStats(['teams' => [$team->id]]))[1])->toBe('1');
});

it('lists the open opportunities with the highest expected revenue', function () {
    CRMHelper::actingAs([...crmDashboardPermissions(), 'view_crm_opportunity']);

    $big = CRMHelper::opportunity(['name' => 'Big dashboard deal', 'expected_revenue' => 90000]);
    $lost = CRMHelper::opportunity(['name' => 'Lost dashboard deal', 'expected_revenue' => 99000]);
    $lost->markAsLost(null);

    Livewire::test(TopOpportunitiesTable::class)
        ->assertCanSeeTableRecords([$big])
        ->assertCanNotSeeTableRecords([$lost]);
});

it('builds the dashboard on the shared dashboard widgets', function () {
    expect(new CRMStatsOverview)->toBeInstanceOf(DashboardStatsWidget::class)
        ->and(collect([WonRevenueChart::class, LeadsBySourceChart::class, PipelineByStageChart::class, SalespersonWonRevenueChart::class])
            ->every(fn (string $chart): bool => is_subclass_of($chart, DashboardChartWidget::class)))->toBeTrue();
});

it('shows only the five largest open opportunities', function () {
    CRMHelper::actingAs([...crmDashboardPermissions(), 'view_crm_opportunity']);

    $opportunities = collect(range(1, 6))->map(fn (int $index): Lead => CRMHelper::opportunity(['expected_revenue' => $index * 1000]));

    Livewire::test(TopOpportunitiesTable::class)
        ->assertCanSeeTableRecords($opportunities->sortByDesc('expected_revenue')->take(5)->values())
        ->assertCanNotSeeTableRecords([$opportunities->first()]);
});

it('renders every dashboard chart with the shared chart theme', function (string $chart, string $type) {
    CRMHelper::actingAs([
        ...crmDashboardPermissions(),
        'widget_crm_won_revenue_chart',
        'widget_crm_pipeline_by_stage_chart',
        'widget_crm_leads_by_source_chart',
        'widget_crm_salesperson_won_revenue_chart',
    ]);

    CRMHelper::wonStage();
    CRMHelper::lead();
    CRMHelper::opportunity(['expected_revenue' => 2500]);
    CRMHelper::opportunity(['expected_revenue' => 4000])->markAsWon();

    $component = Livewire::test($chart)->assertOk();

    expect((fn (): string => $this->getType())->call($component->instance()))->toBe($type)
        ->and((fn (): array => $this->getData())->call($component->instance())['datasets'])->not->toBeEmpty();
})->with([
    'won revenue'  => [WonRevenueChart::class, 'line'],
    'sources'      => [LeadsBySourceChart::class, 'doughnut'],
    'pipeline'     => [PipelineByStageChart::class, 'bar'],
    'salespeople'  => [SalespersonWonRevenueChart::class, 'bar'],
]);
