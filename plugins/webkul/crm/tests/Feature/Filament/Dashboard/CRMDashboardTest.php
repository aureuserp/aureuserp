<?php

use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Livewire;
use Webkul\Crm\Filament\Pages\CRMDashboard;
use Webkul\Crm\Filament\Widgets\CRMStatsOverview;
use Webkul\Crm\Filament\Widgets\TopOpportunitiesTable;
use Webkul\Sale\Models\Team;
use Webkul\Support\Enums\NavigationGroup;

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
    $won = CRMHelper::opportunity(['expected_revenue' => 1500]);
    $won->markAsWon();
    $lost = CRMHelper::opportunity();
    $lost->markAsLost(null);

    $stats = array_values(crmDashboardStats());

    expect($stats[0])->toBe('1')
        ->and($stats[1])->toBe('3')
        ->and($stats[2])->toContain('1,500')
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
