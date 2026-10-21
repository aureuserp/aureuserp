<?php

use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\KanbanOpportunities;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Wezlo\FilamentKanban\KanbanColumn;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

/**
 * Ids of every card on the pipeline board, across all columns.
 *
 * @return array<int, int>
 */
function pipelineBoardRecordIds(Testable $component): array
{
    return collect($component->instance()->getKanbanColumns())
        ->flatMap(fn (KanbanColumn $column): array => $column->records->modelKeys())
        ->map(fn ($id): int => (int) $id)
        ->values()
        ->all();
}

function pipelineBoardColumn(Testable $component, Stage $stage): KanbanColumn
{
    return collect($component->instance()->getKanbanColumns())
        ->firstOrFail(fn (KanbanColumn $column): bool => $column->value === (string) $stage->getKey());
}

function pipelineOtherUserId(): int
{
    return (int) User::query()->whereKeyNot(auth()->id())->value('id');
}

function pipelineTeam(array $attributes = []): Team
{
    return Team::factory()->create([
        'company_id' => CrmTestHelper::companyId(),
        'is_active'  => true,
        ...$attributes,
    ]);
}

it('opens on the current user\'s opportunities', function () {
    $user = CrmTestHelper::actingAsCrmUser(['opportunity']);

    $mine = CrmTestHelper::opportunity();
    $someoneElses = CrmTestHelper::opportunity(['user_id' => pipelineOtherUserId()]);

    $component = Livewire::test(KanbanOpportunities::class)
        ->assertOk()
        ->assertSet('kanbanFilters', ['user_id' => $user->id, 'team_id' => null, 'priority' => null]);

    expect(pipelineBoardRecordIds($component))
        ->toContain($mine->id)
        ->not->toContain($someoneElses->id);
});

it('opens a team\'s pipeline when ?team= is given', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $team = pipelineTeam();

    $teamDeal = CrmTestHelper::opportunity(['team_id' => $team->id, 'user_id' => pipelineOtherUserId()]);
    $mineWithoutTeam = CrmTestHelper::opportunity();

    $component = Livewire::withQueryParams(['team' => $team->id])
        ->test(KanbanOpportunities::class)
        ->assertOk()
        ->assertSet('kanbanFilters', ['user_id' => null, 'team_id' => $team->id, 'priority' => null]);

    expect(pipelineBoardRecordIds($component))
        ->toContain($teamDeal->id)
        ->not->toContain($mineWithoutTeam->id);
});

it('resets the filters but keeps the keys the filter fields are bound to', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $someoneElses = CrmTestHelper::opportunity(['user_id' => pipelineOtherUserId()]);

    $component = Livewire::test(KanbanOpportunities::class)
        ->set('kanbanFilters.priority', OpportunityPriority::High->value)
        ->call('resetKanbanFilters')
        ->assertSet('kanbanFilters', ['user_id' => null, 'team_id' => null, 'priority' => null]);

    expect(pipelineBoardRecordIds($component))->toContain($someoneElses->id);
});

it('filters the board by priority', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $high = CrmTestHelper::opportunity(['priority' => OpportunityPriority::High]);
    $low = CrmTestHelper::opportunity(['priority' => OpportunityPriority::Low]);

    $component = Livewire::test(KanbanOpportunities::class)
        ->set('kanbanFilters.priority', OpportunityPriority::High->value);

    expect(pipelineBoardRecordIds($component))
        ->toContain($high->id)
        ->not->toContain($low->id);
});

it('searches cards by name', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $match = CrmTestHelper::opportunity(['name' => 'Zebra kanban search deal']);
    $other = CrmTestHelper::opportunity(['name' => 'Unrelated deal']);

    $component = Livewire::test(KanbanOpportunities::class)
        ->set('kanbanSearch', 'Zebra kanban');

    expect(pipelineBoardRecordIds($component))
        ->toContain($match->id)
        ->not->toContain($other->id);
});

it('uses the stages, in order, as columns and colours won stages green', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $wonStage = CrmTestHelper::wonStage();
    $firstStage = CrmTestHelper::firstStage();

    $component = Livewire::test(KanbanOpportunities::class);

    $columns = collect($component->instance()->getKanbanColumns());

    expect($columns->pluck('value')->all())
        ->toBe(Stage::query()->orderBy('sort')->pluck('id')->map(fn ($id): string => (string) $id)->all());

    $board = $component->instance()->getKanbanBoard();

    expect($board->resolveColumnColor(pipelineBoardColumn($component, $wonStage)))->toBe('success')
        ->and($board->resolveColumnColor(pipelineBoardColumn($component, $firstStage)))->toBe('gray');
});

it('sums the expected revenue of each column', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $stage = Stage::factory()->create(['name' => 'Revenue summary stage']);
    $emptyStage = Stage::factory()->create(['name' => 'Empty summary stage']);

    CrmTestHelper::opportunity(['stage_id' => $stage->id, 'expected_revenue' => 1000]);
    CrmTestHelper::opportunity(['stage_id' => $stage->id, 'expected_revenue' => 500]);

    $component = Livewire::test(KanbanOpportunities::class);

    $board = $component->instance()->getKanbanBoard();

    $column = pipelineBoardColumn($component, $stage);

    expect($column->count)->toBe(2)
        ->and($board->resolveColumnSummary($column))->toContain('1,500')
        ->and($board->resolveColumnSummary(pipelineBoardColumn($component, $emptyStage)))->toBeNull();
});

it('hides lost opportunities and leads from the board', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $open = CrmTestHelper::opportunity();
    $lost = CrmTestHelper::opportunity();
    $lost->markAsLost(null, 'Gone');
    $lead = CrmTestHelper::lead();

    $component = Livewire::test(KanbanOpportunities::class);

    expect(pipelineBoardRecordIds($component))
        ->toContain($open->id)
        ->not->toContain($lost->id)
        ->not->toContain($lead->id);
});

it('quick-creates an opportunity in the column\'s stage and the current company', function () {
    $user = CrmTestHelper::actingAsCrmUser(['opportunity']);

    $stage = Stage::factory()->create(['name' => 'Quick create stage']);

    Livewire::test(KanbanOpportunities::class)
        ->callAction('kanbanColumn', data: [
            'name'             => 'Quick kanban deal',
            'expected_revenue' => 2500,
        ], arguments: ['column' => (string) $stage->id])
        ->assertHasNoActionErrors()
        ->assertNotified();

    $lead = Lead::query()->where('name', 'Quick kanban deal')->firstOrFail();

    expect($lead->stage_id)->toBe($stage->id)
        ->and($lead->company_id)->toBe(CrmTestHelper::companyId())
        ->and($lead->type)->toBe(Lead::TYPE_OPPORTUNITY)
        ->and($lead->user_id)->toBe($user->id)
        ->and($lead->team_id)->toBeNull()
        ->and((float) $lead->expected_revenue)->toBe(2500.0);
});

it('requires a name to quick-create an opportunity', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $stage = CrmTestHelper::firstStage();

    Livewire::test(KanbanOpportunities::class)
        ->callAction('kanbanColumn', data: ['name' => ''], arguments: ['column' => (string) $stage->id])
        ->assertHasActionErrors(['name' => 'required']);
});

it('quick-creates in the filtered team when it belongs to the current company', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $team = pipelineTeam();

    Livewire::test(KanbanOpportunities::class)
        ->set('kanbanFilters.team_id', $team->id)
        ->callAction('kanbanColumn', data: ['name' => 'Team quick deal'], arguments: ['column' => (string) CrmTestHelper::firstStage()->id])
        ->assertHasNoActionErrors();

    expect(Lead::query()->where('name', 'Team quick deal')->value('team_id'))->toBe($team->id);
});

it('ignores a tampered team filter from another company when quick-creating', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $otherCompany = Company::factory()->create();

    $foreignTeam = Team::factory()->create(['company_id' => $otherCompany->id, 'is_active' => true]);

    Livewire::test(KanbanOpportunities::class)
        ->set('kanbanFilters.team_id', $foreignTeam->id)
        ->callAction('kanbanColumn', data: ['name' => 'Tampered team deal'], arguments: ['column' => (string) CrmTestHelper::firstStage()->id])
        ->assertHasNoActionErrors();

    $lead = Lead::query()->where('name', 'Tampered team deal')->firstOrFail();

    expect($lead->team_id)->toBeNull()
        ->and($lead->company_id)->toBe(CrmTestHelper::companyId());
});

it('falls back to the first stage when quick-creating into an unknown stage', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    Livewire::test(KanbanOpportunities::class)
        ->callAction('kanbanColumn', data: [
            'name'     => 'Unknown stage deal',
            'stage_id' => 999999999,
        ], arguments: ['column' => (string) CrmTestHelper::firstStage()->id])
        ->assertHasNoActionErrors();

    expect(Lead::query()->where('name', 'Unknown stage deal')->value('stage_id'))
        ->toBe(Stage::query()->orderBy('sort')->value('id'));
});

it('hides quick create without the create permission', function () {
    CrmTestHelper::actingAs(['view_any_crm_opportunity', 'view_crm_opportunity']);

    Livewire::test(KanbanOpportunities::class)
        ->assertOk()
        ->assertActionHidden('kanbanColumn', ['column' => (string) CrmTestHelper::firstStage()->id]);
});

it('moves a card to another stage', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $opportunity = CrmTestHelper::opportunity(['stage_id' => CrmTestHelper::firstStage()->id]);

    $target = Stage::factory()->create(['name' => 'Move target stage']);

    Livewire::test(KanbanOpportunities::class)
        ->call('moveRecord', (string) $opportunity->id, (string) $target->id, []);

    $opportunity->refresh();

    expect($opportunity->stage_id)->toBe($target->id)
        ->and($opportunity->closed_at)->toBeNull();
});

it('marks a card as won when it is dropped into a won stage', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    $opportunity = CrmTestHelper::opportunity(['probability' => 20]);

    $wonStage = CrmTestHelper::wonStage();

    Livewire::test(KanbanOpportunities::class)
        ->call('moveRecord', (string) $opportunity->id, (string) $wonStage->id, []);

    $opportunity->refresh();

    expect($opportunity->stage_id)->toBe($wonStage->id)
        ->and($opportunity->isWon())->toBeTrue()
        ->and((float) $opportunity->probability)->toBe(100.0)
        ->and($opportunity->closed_at)->not->toBeNull();
});

it('refuses to move a card without the update permission', function () {
    CrmTestHelper::actingAs(['view_any_crm_opportunity', 'view_crm_opportunity']);

    $firstStage = CrmTestHelper::firstStage();

    $opportunity = CrmTestHelper::opportunity(['stage_id' => $firstStage->id]);

    Livewire::test(KanbanOpportunities::class)
        ->call('moveRecord', (string) $opportunity->id, (string) CrmTestHelper::wonStage()->id, [])
        ->assertNotified();

    expect($opportunity->refresh()->stage_id)->toBe($firstStage->id);
});

it('forbids the board without the view any permission', function () {
    CrmTestHelper::actingAs(['view_any_crm_lead']);

    Livewire::test(KanbanOpportunities::class)->assertForbidden();
});
