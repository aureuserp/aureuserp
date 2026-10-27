<?php

use Livewire\Livewire;
use Webkul\Crm\Enums\LeadColor;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Enums\PipelineView;
use Webkul\Crm\Enums\TeamOrderScope;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource as ConfigurationTeamResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages\ListTeams;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages\ManageTeamOpportunities;
use Webkul\Crm\Models\Lead;
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

    Livewire::withQueryParams(['layout' => 'list'])
        ->test(ListTeams::class)
        ->searchTable('Revenue team')
        ->assertTableColumnStateSet('open_opportunities_count', 2, $team);
});

it('forbids the teams overview with only the sales plugin permissions', function () {
    CRMHelper::actingAs(['view_any_sale_team', 'view_sale_team']);

    Livewire::test(ListTeams::class)->assertForbidden();
});

it('lists only the team\'s opportunities on its pipeline page', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Pipeline page team');
    $otherTeam = salesTeamInCompany('Other pipeline team');

    $inTeam = CRMHelper::opportunity(['team_id' => $team->id]);
    $inOtherTeam = CRMHelper::opportunity(['team_id' => $otherTeam->id]);
    $withoutTeam = CRMHelper::opportunity();
    $leadInTeam = CRMHelper::lead(['team_id' => $team->id]);

    Livewire::withQueryParams(['view' => 'list'])
        ->test(ManageTeamOpportunities::class, ['record' => $team->id])
        ->assertOk()
        ->assertCanSeeTableRecords([$inTeam])
        ->assertCanNotSeeTableRecords([$inOtherTeam, $withoutTeam, $leadInTeam]);
});

it('creates an opportunity inside the team', function () {
    $user = actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Create inside team');

    Livewire::test(ManageTeamOpportunities::class, ['record' => $team->id])
        ->callAction('create', data: [
            'name'             => 'Deal created in the team',
            'expected_revenue' => 3000,
        ])
        ->assertHasNoActionErrors()
        ->assertNotified(__('crm::filament/clusters/sales/resources/team/pages/manage-team-opportunities.header-actions.create.notification.title'));

    $lead = Lead::query()->where('name', 'Deal created in the team')->firstOrFail();

    expect($lead->team_id)->toBe($team->id)
        ->and($lead->type)->toBe(LeadType::OPPORTUNITY)
        ->and($lead->user_id)->toBe($user->id)
        ->and($lead->company_id)->toBe(CRMHelper::companyId());
});

it('labels the team pipeline records as opportunities', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Labelled team');

    $table = Livewire::test(ManageTeamOpportunities::class, ['record' => $team->id])->instance()->getTable();

    expect($table->getModelLabel())->toBe(OpportunityResource::getModelLabel())
        ->and($table->getPluralModelLabel())->toBe(OpportunityResource::getPluralModelLabel());
});

it('shows kanban, list and graph tabs on the team pipeline with kanban by default', function (array $query, array $active) {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Tabbed team');

    $tabs = collect(Livewire::withQueryParams($query)->test(ManageTeamOpportunities::class, ['record' => $team->id])->instance()->getRecordNavigationTabs());

    expect($tabs->map(fn ($tab): string => $tab->getLabel())->all())->toBe([
        PipelineView::KANBAN->getLabel(),
        PipelineView::LIST->getLabel(),
        PipelineView::GRAPH->getLabel(),
    ])
        ->and($tabs->map(fn ($tab): bool => $tab->isActive())->all())->toBe($active);
})->with([
    'kanban' => [[], [true, false, false]],
    'list'   => [['view' => 'list'], [false, true, false]],
    'graph'  => [['view' => 'graph'], [false, false, true]],
    'bad'    => [['view' => 'nope'], [true, false, false]],
]);

it('shows only the team\'s opportunities on the team kanban board', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Board team');
    $otherTeam = salesTeamInCompany('Other board team');

    $inTeam = CRMHelper::opportunity(['team_id' => $team->id]);
    $inOtherTeam = CRMHelper::opportunity(['team_id' => $otherTeam->id]);

    $component = Livewire::test(ManageTeamOpportunities::class, ['record' => $team->id]);

    $ids = collect($component->instance()->getKanbanColumns())->flatMap(fn ($column) => $column->records->modelKeys())->all();

    expect($ids)->toContain($inTeam->id)
        ->not->toContain($inOtherTeam->id)
        ->and($component->instance()->getTableRecords())->toHaveCount(0);
});

it('counts the team\'s opportunities per stage on the team graph', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Graph team');

    CRMHelper::opportunity(['team_id' => $team->id]);
    CRMHelper::opportunity(['team_id' => salesTeamInCompany('Not in graph')->id]);

    $chart = Livewire::withQueryParams(['view' => 'graph'])
        ->test(ManageTeamOpportunities::class, ['record' => $team->id])
        ->instance()
        ->getRecordsGraph()['data'];

    expect(array_sum($chart['datasets'][0]['data']))->toBe(1);
});

it('shows the teams as cards by default with list as the second tab', function (array $query, array $active) {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Card team');

    $component = Livewire::withQueryParams($query)->test(ListTeams::class)->assertOk();

    expect(collect($component->instance()->getRecordNavigationTabs())->map(fn ($tab): bool => $tab->isActive())->all())->toBe($active);

    $component->assertCanSeeTableRecords([$team]);
})->with([
    'grid' => [[], [true, false]],
    'list' => [['layout' => 'list'], [false, true]],
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

it('forbids the team pipeline without the opportunity permissions', function () {
    CRMHelper::actingAsCRMUser(['team']);

    $team = salesTeamInCompany('Forbidden team');

    Livewire::test(ManageTeamOpportunities::class, ['record' => $team->id])->assertForbidden();
});

it('forbids the team pipeline with the opportunity permissions but without the team permissions', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $team = salesTeamInCompany('Team without team access');

    Livewire::test(ManageTeamOpportunities::class, ['record' => $team->id])->assertForbidden();
});

it('forbids the team pipeline without permission to view a single team', function () {
    CRMHelper::actingAs([
        ...CRMHelper::permissionsFor(['opportunity']),
        'view_any_crm_team',
    ]);

    $team = salesTeamInCompany('Team without view access');

    expect(ManageTeamOpportunities::canAccess(['record' => $team]))->toBeFalse();
});

it('opens the team pipeline with both the team and the opportunity permissions', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Team with full access');

    expect(ManageTeamOpportunities::canAccess(['record' => $team]))->toBeTrue();

    Livewire::test(ManageTeamOpportunities::class, ['record' => $team->id])->assertSuccessful();
});

it('links the team card menu to the team\'s records and reports', function () {
    CRMHelper::actingAsCRMUser(['team', 'opportunity', 'lead', 'quotation', 'lead::analysis', 'pipeline::analysis', 'forecast', 'activity::analysis']);

    $team = salesTeamInCompany('Menu team');
    $reportFilters = ['filters' => ['team_ids' => ['values' => [$team->id]]]];

    Livewire::test(ListTeams::class)
        ->assertSeeHtml(e(LeadResource::getUrl('index', ['team' => $team->id])))
        ->assertSeeHtml(e(TeamResource::getUrl('opportunities', ['record' => $team, 'view' => 'list'])))
        ->assertSeeHtml(e(QuotationResource::getUrl('index', ['activeTableView' => 'default', 'team' => $team->id, 'scope' => TeamOrderScope::SALES_ORDERS->value])))
        ->assertDontSeeHtml(e(LeadResource::getUrl('create', ['team' => $team->id])))
        ->assertSeeHtml(e(ConfigurationTeamResource::getUrl('edit', ['record' => $team])))
        ->assertDontSee(__('crm::filament/clusters/sales/resources/team.table.actions.pipeline.label'))
        ->assertSeeHtml(e(PipelineAnalysisResource::getUrl('index', $reportFilters)))
        ->assertSeeHtml(e(ForecastResource::getUrl('graph', $reportFilters)));
});

it('sets and clears the team color from the card menu', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Colored team');

    Livewire::test(ListTeams::class)->call('setTeamColor', $team->id, LeadColor::TEAL->value);

    expect($team->fresh()->color)->toBe(LeadColor::TEAL->getHex());

    Livewire::test(ListTeams::class)->call('setTeamColor', $team->id, 0);

    expect($team->fresh()->color)->toBeNull();
});

it('ignores team color changes without the team update permission', function () {
    CRMHelper::actingAs([
        ...CRMHelper::permissionsFor(['team'], ['view_any', 'view']),
        ...CRMHelper::permissionsFor(['opportunity']),
    ]);

    $team = salesTeamInCompany('Locked color team');

    Livewire::test(ListTeams::class)->call('setTeamColor', $team->id, LeadColor::TEAL->value);

    expect($team->fresh()->color)->toBeNull();
});

it('limits the team quotations to confirmed sales orders with the sales orders scope', function () {
    $query = TeamOrderScope::SALES_ORDERS->apply(Order::query());

    expect($query->toRawSql())->toContain("`state` = 'sale'");
});
