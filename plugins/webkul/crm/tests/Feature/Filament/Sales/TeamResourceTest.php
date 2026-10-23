<?php

use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages\ListTeams;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Pages\ManageTeamOpportunities;
use Webkul\Crm\Models\Lead;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

function actingAsSalesTeamViewer(): User
{
    return CrmTestHelper::actingAsCrmUser(['team', 'opportunity']);
}

function salesTeamInCompany(string $name, ?int $companyId = null): Team
{
    return Team::factory()->create([
        'name'       => $name,
        'company_id' => $companyId ?? CrmTestHelper::companyId(),
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

    CrmTestHelper::opportunity(['team_id' => $team->id, 'expected_revenue' => 1000]);
    CrmTestHelper::opportunity(['team_id' => $team->id, 'expected_revenue' => 250]);
    CrmTestHelper::opportunity(['team_id' => $team->id, 'expected_revenue' => 5000, 'stage_id' => CrmTestHelper::wonStage()->id]);
    CrmTestHelper::lead(['team_id' => $team->id, 'expected_revenue' => 7000]);
    CrmTestHelper::opportunity(['team_id' => $team->id, 'expected_revenue' => 9000])->markAsLost(null);

    $record = TeamResource::getEloquentQuery()->whereKey($team->id)->firstOrFail();

    expect((int) $record->open_opportunities_count)->toBe(2)
        ->and((float) $record->open_expected_revenue)->toBe(1250.0);

    Livewire::test(ListTeams::class)
        ->searchTable('Revenue team')
        ->assertTableColumnStateSet('open_opportunities_count', 2, $team);
});

it('forbids the teams overview with only the sales plugin permissions', function () {
    CrmTestHelper::actingAs(['view_any_sale_team', 'view_sale_team']);

    Livewire::test(ListTeams::class)->assertForbidden();
});

it('lists only the team\'s opportunities on its pipeline page', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Pipeline page team');
    $otherTeam = salesTeamInCompany('Other pipeline team');

    $inTeam = CrmTestHelper::opportunity(['team_id' => $team->id]);
    $inOtherTeam = CrmTestHelper::opportunity(['team_id' => $otherTeam->id]);
    $withoutTeam = CrmTestHelper::opportunity();
    $leadInTeam = CrmTestHelper::lead(['team_id' => $team->id]);

    Livewire::test(ManageTeamOpportunities::class, ['record' => $team->id])
        ->assertOk()
        ->assertCanSeeTableRecords([$inTeam])
        ->assertCanNotSeeTableRecords([$inOtherTeam, $withoutTeam, $leadInTeam]);
});

it('creates an opportunity inside the team', function () {
    $user = actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Create inside team');

    Livewire::test(ManageTeamOpportunities::class, ['record' => $team->id])
        ->callAction(TestAction::make('create')->table(), data: [
            'name'             => 'Deal created in the team',
            'expected_revenue' => 3000,
        ])
        ->assertHasNoActionErrors()
        ->assertNotified(__('crm::filament/clusters/sales/resources/team/pages/manage-team-opportunities.header-actions.create.notification.title'));

    $lead = Lead::query()->where('name', 'Deal created in the team')->firstOrFail();

    expect($lead->team_id)->toBe($team->id)
        ->and($lead->type)->toBe(Lead::TYPE_OPPORTUNITY)
        ->and($lead->user_id)->toBe($user->id)
        ->and($lead->company_id)->toBe(CrmTestHelper::companyId());
});

it('labels the team pipeline records as opportunities', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Labelled team');

    $table = Livewire::test(ManageTeamOpportunities::class, ['record' => $team->id])->instance()->getTable();

    expect($table->getModelLabel())->toBe(OpportunityResource::getModelLabel())
        ->and($table->getPluralModelLabel())->toBe(OpportunityResource::getPluralModelLabel());
});

it('links the team pipeline to the kanban board filtered on the team', function () {
    actingAsSalesTeamViewer();

    $team = salesTeamInCompany('Kanban link team');

    $url = OpportunityResource::getUrl('index', ['team' => $team->id]);

    Livewire::test(ManageTeamOpportunities::class, ['record' => $team->id])
        ->assertActionVisible(TestAction::make('kanbanView')->table())
        ->assertActionHasUrl(TestAction::make('kanbanView')->table(), $url);

    expect($url)->toContain('team='.$team->id);
});

it('forbids the team pipeline without the opportunity permissions', function () {
    CrmTestHelper::actingAsCrmUser(['team']);

    $team = salesTeamInCompany('Forbidden team');

    Livewire::test(ManageTeamOpportunities::class, ['record' => $team->id])->assertForbidden();
});

it('forbids the team pipeline with the opportunity permissions but without the team permissions', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $team = salesTeamInCompany('Team without team access');

    Livewire::test(ManageTeamOpportunities::class, ['record' => $team->id])->assertForbidden();
});

it('forbids the team pipeline without permission to view a single team', function () {
    CrmTestHelper::actingAs([
        ...CrmTestHelper::permissionsFor(['opportunity']),
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
