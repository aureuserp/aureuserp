<?php

use Filament\Actions\Action;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Webkul\Crm\Enums\TeamOrderScope;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource as ConfigurationTeamResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\CreateOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages\ListTeams;
use Webkul\Crm\Filament\Support\LeadTableFilters;
use Webkul\Crm\Services\TeamDashboard;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function actingAsSalesTeamViewer(): User
{
    return CRMHelper::actingAsCRMUser(['team', 'opportunity']);
}

function salesTeamInCompany(string $name, ?int $companyId = null): Team
{
    return Team::factory()->create([
        'name'       => $name,
        'company_id' => $companyId ?? CRMHelper::companyId(),
        'is_active'  => true,
        'color'      => null,
    ]);
}

it('lists the current company\'s sales teams', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('CRM pipeline team');
    $foreignTeam = salesTeamInCompany('CRM pipeline team abroad', Company::factory()->create()->id);

    Livewire::test(ListTeams::class)
        ->assertOk()
        ->searchTable('CRM pipeline team')
        ->assertCanSeeTableRecords([$team])
        ->assertCanNotSeeTableRecords([$foreignTeam]);
});

it('shows each team\'s open opportunity count and open expected revenue', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Revenue team');

    CRMHelper::opportunity(['team_id' => $team->id, 'expected_revenue' => 1000]);
    CRMHelper::opportunity(['team_id' => $team->id, 'expected_revenue' => 250]);
    CRMHelper::opportunity(['team_id' => $team->id, 'expected_revenue' => 5000, 'stage_id' => CRMHelper::wonStage()->id]);
    CRMHelper::lead(['team_id' => $team->id, 'expected_revenue' => 7000]);
    CRMHelper::opportunity(['team_id' => $team->id, 'expected_revenue' => 9000])->markAsLost(null);

    $record = TeamResource::getEloquentQuery()->whereKey($team->id)->firstOrFail();

    expect((int) $record->open_opportunities_count)->toBe(2)
        ->and((float) $record->open_expected_revenue)->toBe(1250.0);

    Livewire::test(ListTeams::class)
        ->searchTable('Revenue team')
        ->assertTableColumnStateSet('open_opportunities_count', 2, $team);
});

it('lists the teams with the configuration table columns, one detail per column', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Columns team');

    $component = Livewire::withQueryParams(['layout' => 'list'])
        ->test(ListTeams::class)
        ->searchTable('Columns team')
        ->assertCanSeeTableRecords([$team]);

    expect(array_keys($component->instance()->getTable()->getColumns()))
        ->toBe(['name', 'id', 'company.name', 'user.name', 'color', 'creator.name', 'is_active', 'invoiced_target', 'created_at', 'updated_at']);
});

it('forbids the teams overview with only the sales plugin permissions', function () {
    CRMHelper::actingAs(['view_any_sale_team', 'view_sale_team']);

    Livewire::test(ListTeams::class)->assertForbidden();
});

it('shows the teams as cards by default with list as the second view', function (array $query, ViewLayout $active) {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Card team');

    $component = Livewire::withQueryParams($query)->test(ListTeams::class)->assertOk();

    expect($component->instance()->getViewLayouts())->toBe([ViewLayout::GRID, ViewLayout::LIST])
        ->and($component->instance()->getViewLayout())->toBe($active);

    $component->assertCanSeeTableRecords([$team]);
})->with([
    'grid' => [[], ViewLayout::GRID],
    'list' => [['layout' => 'list'], ViewLayout::LIST],
]);

it('builds the team card figures and counts new opportunities per week', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Dashboard team');

    Order::factory()->create(['team_id' => $team->id, 'invoice_status' => InvoiceStatus::TO_INVOICE, 'company_id' => CRMHelper::companyId()]);
    Order::factory()->create(['team_id' => $team->id, 'invoice_status' => InvoiceStatus::NO, 'company_id' => CRMHelper::companyId()]);

    CRMHelper::opportunity(['team_id' => $team->id]);
    CRMHelper::opportunity(['team_id' => $team->id]);
    CRMHelper::lead(['team_id' => $team->id]);

    $dashboard = app(TeamDashboard::class)->forTeams([$team->id])[$team->id];

    expect($dashboard['orders_to_invoice'])->toBe(1)
        ->and($dashboard['weeks'])->toHaveCount(TeamDashboard::WEEKS)
        ->and(end($dashboard['weeks'])['count'])->toBe(2)
        ->and($dashboard['invoiced_month'])->toEqual(0);
});

it('opens the pipeline filtered by the team from the team card and creates opportunities in the team', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Pipeline link team');

    Livewire::test(ListTeams::class)
        ->assertSeeHtml(e(OpportunityResource::getUrl('index', ['team' => $team->id])))
        ->assertSeeHtml(e(OpportunityResource::getUrl('index', ['team' => $team->id, 'layout' => 'list', 'activeTableView' => 'open'])))
        ->assertSeeHtml(e(OpportunityResource::getUrl('create', ['team' => $team->id])));
});

it('prefills the team when an opportunity is created from a team card', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Prefilled team');
    $otherCompanyTeam = salesTeamInCompany('Other company team', Company::factory()->create()->id);

    Livewire::withQueryParams(['team' => $team->id])
        ->test(CreateOpportunity::class)
        ->assertSchemaStateSet(['team_id' => $team->id]);

    Livewire::withQueryParams(['team' => $otherCompanyTeam->id])
        ->test(CreateOpportunity::class)
        ->assertSchemaStateSet(['team_id' => null]);
});

it('links the team card menu to the team\'s records and reports', function () {
    CRMHelper::actingAsCRMUser(['team', 'opportunity', 'lead', 'quotation', 'lead::analysis', 'pipeline::analysis', 'forecast', 'activity::analysis']);

    $team = salesTeamInCompany('Menu team');
    $reportFilters = ['filters' => ['team_ids' => ['values' => [$team->id]]]];

    $component = Livewire::test(ListTeams::class)
        ->assertSeeHtml(e(LeadResource::getUrl('index', ['activeTableView' => 'all', 'filters' => ['queryBuilder' => ['rules' => ['team' => LeadTableFilters::teamRule($team->id)]]]])))
        ->assertSeeHtml(e(OpportunityResource::getUrl('index', ['team' => $team->id, 'layout' => 'list'])))
        ->assertSeeHtml(e(QuotationResource::getUrl('index', QuotationResource::getTeamOrdersParameters($team->id, TeamOrderScope::SALES_ORDERS))))
        ->assertDontSeeHtml(e(LeadResource::getUrl('create', ['team' => $team->id])))
        ->assertSeeHtml(e(ConfigurationTeamResource::getUrl('edit', ['record' => $team])))
        ->assertSeeHtml(e(PipelineAnalysisResource::getUrl('index', $reportFilters)))
        ->assertSeeHtml(e(ForecastResource::getUrl('index', [...$reportFilters, 'layout' => 'graph'])));

    expect(collect($component->instance()->getTable()->getRecordActions())->filter(fn ($action): bool => $action instanceof Action)->map->getName()->all())
        ->not->toContain('pipeline');
});

it('builds the team card from filament columns and record actions', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Card columns team');

    CRMHelper::opportunity(['team_id' => $team->id, 'expected_revenue' => 400]);

    Livewire::test(ListTeams::class)
        ->searchTable('Card columns team')
        ->assertTableColumnFormattedStateSet('open_opportunities_count', trans_choice('crm::filament/clusters/sales/resources/team.grid.open-opportunities', 1, ['count' => 1]), $team)
        ->assertTableColumnExists('weekly_opportunities')
        ->assertActionVisible(TestAction::make('newOpportunity')->table($team))
        ->assertActionVisible(TestAction::make('viewOpportunities')->table($team))
        ->assertActionHidden(TestAction::make('viewLeads')->table($team))
        ->assertActionVisible(TestAction::make('teamColor')->table($team))
        ->assertActionHidden(TestAction::make('teamColorDot')->table($team));

    $team->update(['color' => '#2c8397']);

    Livewire::test(ListTeams::class)
        ->searchTable('Card columns team')
        ->assertActionVisible(TestAction::make('teamColorDot')->table($team))
        ->assertActionEnabled(TestAction::make('teamColorDot')->table($team))
        ->callAction(TestAction::make('teamColorDot')->table($team), data: ['color' => '#814968'])
        ->assertHasNoActionErrors();

    expect($team->fresh()->color)->toBe('#814968');
});

it('sets and clears the team color with the colour picker', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Colored team');

    Livewire::test(ListTeams::class)
        ->callAction(TestAction::make('teamColor')->table($team), data: ['color' => '#2c8397'])
        ->assertHasNoActionErrors();

    expect($team->fresh()->color)->toBe('#2c8397');

    Livewire::test(ListTeams::class)
        ->callAction(TestAction::make('teamColor')->table($team), data: ['color' => null]);

    expect($team->fresh()->color)->toBeNull();
});

it('hides the team colour picker without the team update permission', function () {
    CRMHelper::actingAs([
        ...CRMHelper::permissionsFor(['team'], ['view_any', 'view']),
        ...CRMHelper::permissionsFor(['opportunity']),
    ]);

    $team = salesTeamInCompany('Locked color team');
    $team->update(['color' => '#2c8397']);

    Livewire::test(ListTeams::class)
        ->assertActionHidden(TestAction::make('teamColor')->table($team))
        ->assertActionVisible(TestAction::make('teamColorDot')->table($team))
        ->assertActionDisabled(TestAction::make('teamColorDot')->table($team));
});

it('turns each team card figure into quotation list filter rules', function () {
    expect(TeamOrderScope::SALES_ORDERS->getFilterRules())->toBe(['state' => ['type' => 'state', 'data' => ['operator' => 'is', 'settings' => ['values' => ['sale']]]]])
        ->and(TeamOrderScope::QUOTATIONS->getFilterRules()['state']['data']['settings']['values'])->toBe(['draft', 'sent'])
        ->and(TeamOrderScope::TO_INVOICE->getFilterRules()['invoice_status']['data']['settings']['values'])->toBe(['to_invoice']);
});
