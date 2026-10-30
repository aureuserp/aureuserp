<?php

use Filament\Actions\Testing\TestAction;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Crm\Models\Tag;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityType;
use Webkul\Support\Models\Company;
use Webkul\TableViews\Models\TableView;
use Webkul\TableViews\Models\TableViewFavorite;
use Wezlo\FilamentKanban\KanbanColumn;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

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

function pipelineRule(string $type, array $values, string $operator = 'isRelatedTo'): array
{
    return ['queryBuilder' => ['rules' => [$type => ['type' => $type, 'data' => ['operator' => $operator, 'settings' => ['values' => array_map('strval', $values)]]]]]];
}

function pipelineTeam(array $attributes = []): Team
{
    return Team::factory()->create([
        'company_id' => CRMHelper::companyId(),
        'is_active'  => true,
        ...$attributes,
    ]);
}

it('opens on the My Pipeline tab, showing the user\'s opportunities', function (?string $layout) {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $mine = CRMHelper::opportunity();
    $someoneElses = CRMHelper::opportunity(['user_id' => pipelineOtherUserId()]);

    $component = Livewire::withQueryParams(array_filter(['layout' => $layout]))->test(ListOpportunities::class)
        ->assertOk()
        ->assertSet('activeTableView', 'my_pipeline');

    expect(pipelineBoardRecordIds($component))
        ->toContain($mine->id)
        ->not->toContain($someoneElses->id);

    $component->call('loadView', 'default');

    expect(pipelineBoardRecordIds($component))->toContain($mine->id, $someoneElses->id);
})->with(['kanban' => [null], 'list' => ['list'], 'graph' => ['graph']]);

it('opens a team\'s pipeline when ?team= is given', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $team = pipelineTeam();

    $teamDeal = CRMHelper::opportunity(['team_id' => $team->id, 'user_id' => pipelineOtherUserId()]);
    $mineWithoutTeam = CRMHelper::opportunity();

    $component = Livewire::withQueryParams(['team' => $team->id])
        ->test(ListOpportunities::class)
        ->assertOk()
        ->assertSet('tableFilters.queryBuilder.rules.team.data.settings.values', [(string) $team->id])
        ->assertSet('activeTableView', 'default');

    expect(pipelineBoardRecordIds($component))
        ->toContain($teamDeal->id)
        ->not->toContain($mineWithoutTeam->id);
});

it('resets the table filters on the board', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $someoneElses = CRMHelper::opportunity(['user_id' => pipelineOtherUserId()]);

    $component = Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->set('tableFilters', pipelineRule('priority', [OpportunityPriority::HIGH->value], 'is'))
        ->call('resetTableFiltersForm');

    expect(pipelineBoardRecordIds($component))->toContain($someoneElses->id);
});

it('filters the board by priority', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $high = CRMHelper::opportunity(['priority' => OpportunityPriority::HIGH]);
    $low = CRMHelper::opportunity(['priority' => OpportunityPriority::LOW]);

    $component = Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->set('tableFilters', pipelineRule('priority', [OpportunityPriority::HIGH->value], 'is'));

    expect(pipelineBoardRecordIds($component))
        ->toContain($high->id)
        ->not->toContain($low->id);
});

it('searches cards by name', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $match = CRMHelper::opportunity(['name' => 'Zebra kanban search deal']);
    $other = CRMHelper::opportunity(['name' => 'Unrelated deal']);

    $component = Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->set('tableSearch', 'Zebra kanban');

    expect(pipelineBoardRecordIds($component))
        ->toContain($match->id)
        ->not->toContain($other->id);
});

it('uses the stages, in order, as columns and colours won stages green', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $wonStage = CRMHelper::wonStage();
    $firstStage = CRMHelper::firstStage();

    $component = Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class);

    $columns = collect($component->instance()->getKanbanColumns());

    expect($columns->pluck('value')->all())
        ->toBe(Stage::query()->orderBy('sort')->pluck('id')->map(fn ($id): string => (string) $id)->all());

    $board = $component->instance()->getKanbanBoard();

    expect($board->resolveColumnColor(pipelineBoardColumn($component, $wonStage)))->toBe('success')
        ->and($board->resolveColumnColor(pipelineBoardColumn($component, $firstStage)))->toBe('gray');
});

it('sums the expected revenue of each column', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $stage = Stage::factory()->create(['name' => 'Revenue summary stage']);
    $emptyStage = Stage::factory()->create(['name' => 'Empty summary stage']);

    CRMHelper::opportunity(['stage_id' => $stage->id, 'expected_revenue' => 1000]);
    CRMHelper::opportunity(['stage_id' => $stage->id, 'expected_revenue' => 500]);

    $component = Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class);

    $board = $component->instance()->getKanbanBoard();

    $column = pipelineBoardColumn($component, $stage);

    expect($column->count)->toBe(2)
        ->and($board->resolveColumnSummary($column))->toContain('1,500')
        ->and($board->resolveColumnSummary(pipelineBoardColumn($component, $emptyStage)))->toBeNull();
});

it('hides lost opportunities and leads from the board', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $open = CRMHelper::opportunity();
    $lost = CRMHelper::opportunity();
    $lost->markAsLost(null, 'Gone');
    $lead = CRMHelper::lead();

    $component = Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class);

    expect(pipelineBoardRecordIds($component))
        ->toContain($open->id)
        ->not->toContain($lost->id)
        ->not->toContain($lead->id);
});

it('shows the pipeline list tabs on the board and filters the cards by the chosen tab', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $mine = CRMHelper::opportunity();
    $someoneElses = CRMHelper::opportunity(['user_id' => pipelineOtherUserId()]);
    $lost = CRMHelper::opportunity();
    $lost->markAsLost(null);

    $component = Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)->assertSet('activeTableView', 'default');

    expect(array_keys($component->instance()->getCachedFavoriteTableViews()))->toContain('default', 'my_pipeline', 'won', 'lost');

    $component->call('loadView', 'my_pipeline');

    expect(pipelineBoardRecordIds($component))->toContain($mine->id)->not->toContain($someoneElses->id);

    $component->call('loadView', 'lost');

    expect(pipelineBoardRecordIds($component))->toBe([$lost->id])
        ->and($component->instance()->getCachedFavoriteTableViews()['lost']->getBadge())->toBe(1);
});

it('shows views saved on the pipeline list as board tabs and applies their filters', function () {
    $user = CRMHelper::actingAsCRMUser(['opportunity']);

    $high = CRMHelper::opportunity(['priority' => OpportunityPriority::HIGH]);
    $low = CRMHelper::opportunity(['priority' => OpportunityPriority::LOW]);

    $view = TableView::query()->create([
        'name'            => 'High priority',
        'filterable_type' => ListOpportunities::class,
        'user_id'         => $user->id,
        'is_public'       => false,
        'filters'         => ['tableFilters' => ['queryBuilder' => ['rules' => ['r1' => ['type' => 'priority', 'data' => ['operator' => 'is', 'settings' => ['values' => [OpportunityPriority::HIGH->value]]]]]]]],
    ]);

    TableViewFavorite::query()->create(['view_type' => 'saved', 'view_key' => $view->id, 'filterable_type' => ListOpportunities::class, 'user_id' => $user->id, 'is_favorite' => true]);

    $component = Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)->call('loadView', (string) $view->id);

    expect(pipelineBoardRecordIds($component))->toContain($high->id)->not->toContain($low->id);
});

it('saves a view from the board for the pipeline list', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->set('tableFilters', pipelineRule('priority', [OpportunityPriority::HIGH->value], 'is'))
        ->callAction('createTableView', data: ['name' => 'Board high priority', 'is_favorite' => true, 'is_public' => false])
        ->assertHasNoActionErrors();

    $view = TableView::query()->where('name', 'Board high priority')->firstOrFail();

    expect($view->filterable_type)->toBe(ListOpportunities::class)
        ->and(data_get($view->filters, 'tableFilters.queryBuilder.rules.priority.type'))->toBe('priority')
        ->and(array_keys(Livewire::withQueryParams(['layout' => 'list'])->test(ListOpportunities::class)->instance()->getCachedFavoriteTableViews()))->toContain($view->id);
});

it('quick-creates an opportunity in the column\'s stage and the current company', function () {
    $user = CRMHelper::actingAsCRMUser(['opportunity']);

    $stage = Stage::factory()->create(['name' => 'Quick create stage']);

    Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->callAction('kanbanColumn', data: [
            'name'             => 'Quick kanban deal',
            'expected_revenue' => 2500,
        ], arguments: ['column' => (string) $stage->id])
        ->assertHasNoActionErrors()
        ->assertNotified();

    $lead = Lead::query()->where('name', 'Quick kanban deal')->firstOrFail();

    expect($lead->stage_id)->toBe($stage->id)
        ->and($lead->company_id)->toBe(CRMHelper::companyId())
        ->and($lead->type)->toBe(LeadType::OPPORTUNITY)
        ->and($lead->user_id)->toBe($user->id)
        ->and($lead->team_id)->toBeNull()
        ->and((float) $lead->expected_revenue)->toBe(2500.0);
});

it('requires a name to quick-create an opportunity', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $stage = CRMHelper::firstStage();

    Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->callAction('kanbanColumn', data: ['name' => ''], arguments: ['column' => (string) $stage->id])
        ->assertHasActionErrors(['name' => 'required']);
});

it('quick-creates in the filtered team when it belongs to the current company', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $team = pipelineTeam();

    Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->set('tableFilters', pipelineRule('team', [$team->id]))
        ->callAction('kanbanColumn', data: ['name' => 'Team quick deal'], arguments: ['column' => (string) CRMHelper::firstStage()->id])
        ->assertHasNoActionErrors();

    expect(Lead::query()->where('name', 'Team quick deal')->value('team_id'))->toBe($team->id);
});

it('ignores a tampered team filter from another company when quick-creating', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $otherCompany = Company::factory()->create();

    $foreignTeam = Team::factory()->create(['company_id' => $otherCompany->id, 'is_active' => true]);

    Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->set('tableFilters', pipelineRule('team', [$foreignTeam->id]))
        ->callAction('kanbanColumn', data: ['name' => 'Tampered team deal'], arguments: ['column' => (string) CRMHelper::firstStage()->id])
        ->assertHasNoActionErrors();

    $lead = Lead::query()->where('name', 'Tampered team deal')->firstOrFail();

    expect($lead->team_id)->toBeNull()
        ->and($lead->company_id)->toBe(CRMHelper::companyId());
});

it('falls back to the first stage when quick-creating into an unknown stage', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->callAction('kanbanColumn', data: [
            'name'     => 'Unknown stage deal',
            'stage_id' => 999999999,
        ], arguments: ['column' => (string) CRMHelper::firstStage()->id])
        ->assertHasNoActionErrors();

    expect(Lead::query()->where('name', 'Unknown stage deal')->value('stage_id'))
        ->toBe(Stage::query()->orderBy('sort')->value('id'));
});

it('hides quick create without the create permission', function () {
    CRMHelper::actingAs(['view_any_crm_opportunity', 'view_crm_opportunity']);

    Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->assertOk()
        ->assertActionHidden('kanbanColumn', ['column' => (string) CRMHelper::firstStage()->id]);
});

it('moves a card to another stage', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $opportunity = CRMHelper::opportunity(['stage_id' => CRMHelper::firstStage()->id]);

    $target = Stage::factory()->create(['name' => 'Move target stage']);

    Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->call('moveRecord', (string) $opportunity->id, (string) $target->id, []);

    $opportunity->refresh();

    expect($opportunity->stage_id)->toBe($target->id)
        ->and($opportunity->closed_at)->toBeNull();
});

it('keys columns and cards so a moved card is kept instead of redrawn', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $opportunity = CRMHelper::opportunity(['stage_id' => CRMHelper::firstStage()->id]);

    $target = Stage::factory()->create(['name' => 'Keyed target stage']);

    $component = Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->call('moveRecord', (string) $opportunity->id, (string) $target->id, [(string) $opportunity->id]);

    $component
        ->assertSeeHtml('wire:key="kanban-column-'.$target->id.'"')
        ->assertSeeHtml('wire:key="kanban-card-'.$opportunity->id.'"')
        ->assertSeeHtml('data-kanban-count="'.pipelineBoardColumn($component, $target)->count.'"');

    expect(pipelineBoardColumn($component, $target)->records->modelKeys())->toContain($opportunity->id);
});

it('marks a card as won when it is dropped into a won stage', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $opportunity = CRMHelper::opportunity(['probability' => 20]);

    $wonStage = CRMHelper::wonStage();

    Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->call('moveRecord', (string) $opportunity->id, (string) $wonStage->id, []);

    $opportunity->refresh();

    expect($opportunity->stage_id)->toBe($wonStage->id)
        ->and($opportunity->isWon())->toBeTrue()
        ->and((float) $opportunity->probability)->toBe(100.0)
        ->and($opportunity->closed_at)->not->toBeNull();
});

it('refuses to move a card without the update permission', function () {
    CRMHelper::actingAs(['view_any_crm_opportunity', 'view_crm_opportunity']);

    $firstStage = CRMHelper::firstStage();

    $opportunity = CRMHelper::opportunity(['stage_id' => $firstStage->id]);

    Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->call('moveRecord', (string) $opportunity->id, (string) CRMHelper::wonStage()->id, [])
        ->assertNotified();

    expect($opportunity->refresh()->stage_id)->toBe($firstStage->id);
});

it('forbids the board without the view any permission', function () {
    CRMHelper::actingAs(['view_any_crm_lead']);

    Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)->assertForbidden();
});

describe('list view filters on the board', function () {
    it('filters by the open, unassigned, won and lost tabs', function () {
        $user = CRMHelper::actingAsCRMUser(['opportunity']);

        $open = CRMHelper::opportunity(['stage_id' => CRMHelper::firstStage()->id]);
        $won = CRMHelper::opportunity(['stage_id' => CRMHelper::wonStage()->id]);
        $lost = CRMHelper::opportunity();
        $lost->markAsLost(null);
        $unassigned = CRMHelper::opportunity();
        $unassigned->forceFill(['user_id' => null])->saveQuietly();

        $component = Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class);

        $ids = fn (string $tab): array => pipelineBoardRecordIds($component->call('loadView', $tab));

        expect($ids('open'))->toContain($open->id)->not->toContain($won->id)->not->toContain($lost->id)
            ->and($ids('won'))->toContain($won->id)->not->toContain($open->id)
            ->and($ids('lost'))->toContain($lost->id)->not->toContain($open->id)
            ->and($ids('unassigned'))->toContain($unassigned->id)->not->toContain($open->id);
    });

    it('hides lost opportunities unless the Lost tab is chosen', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $lost = CRMHelper::opportunity();
        $lost->markAsLost(null);

        expect(pipelineBoardRecordIds(Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)))->not->toContain($lost->id);
    });

    it('filters by stages and tags', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $first = CRMHelper::firstStage();
        $tag = Tag::query()->create(['name' => 'Board tag '.uniqid()]);

        $inStage = CRMHelper::opportunity(['stage_id' => $first->id]);
        $tagged = CRMHelper::opportunity(['stage_id' => CRMHelper::wonStage()->id]);
        $tagged->tags()->attach($tag->id);

        $byStage = pipelineBoardRecordIds(Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)->set('tableFilters', pipelineRule('stage', [$first->id])));
        $byTag = pipelineBoardRecordIds(Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)->set('tableFilters', pipelineRule('tags', [$tag->id])));

        expect($byStage)->toContain($inStage->id)->not->toContain($tagged->id)
            ->and($byTag)->toBe([$tagged->id]);
    });

    it('does not move lost opportunities on the board', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $lost = CRMHelper::opportunity(['stage_id' => CRMHelper::firstStage()->id]);
        $lost->markAsLost(null);

        Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
            ->call('loadView', 'lost')
            ->call('moveRecord', (string) $lost->id, (string) CRMHelper::wonStage()->id, [(string) $lost->id]);

        expect(Lead::withTrashed()->find($lost->id)->stage_id)->toBe(CRMHelper::firstStage()->id);
    });
});

function pipelineActivity(Lead $lead, ?string $deadline): void
{
    $lead->addMessage([
        'type'             => 'activity',
        'activity_type_id' => ActivityType::query()->where('plugin', 'crm')->value('id'),
        'date_deadline'    => $deadline,
        'summary'          => 'Pipeline card activity',
        'assigned_to'      => auth()->id(),
    ]);
}

describe('pipeline cards', function () {
    it('opens the record with one click and renders the card details', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $tag = Tag::query()->create(['name' => 'Card tag '.uniqid(), 'color' => '#10b981']);
        $opportunity = CRMHelper::opportunity(['name' => 'Card details '.uniqid(), 'expected_revenue' => 1500, 'priority' => OpportunityPriority::HIGH]);
        $opportunity->tags()->attach($tag->id);

        Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
            ->assertSee($opportunity->name)
            ->assertSee($tag->name)
            ->assertSeeHtml('window.location.href')
            ->assertSeeHtml('setKanbanCardPriority');
    });

    it('sets the priority from the stars and clears it when the same star is clicked again', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $opportunity = CRMHelper::opportunity(['priority' => OpportunityPriority::LOW]);
        $component = Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class);

        $component->call('setKanbanCardPriority', (string) $opportunity->id, OpportunityPriority::HIGH->value);

        expect($opportunity->fresh()->priority)->toBe(OpportunityPriority::HIGH);

        $component->call('setKanbanCardPriority', (string) $opportunity->id, OpportunityPriority::HIGH->value);

        expect($opportunity->fresh()->priority)->toBe(OpportunityPriority::LOW);
    });

    it('sets and clears a card colour with the colour picker', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $opportunity = CRMHelper::opportunity();

        Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
            ->callAction('kanbanCardColor', data: ['color' => '#30c381'], arguments: ['record' => $opportunity->id])
            ->assertHasNoActionErrors();

        expect($opportunity->fresh()->color)->toBe('#30c381');

        Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
            ->callAction('kanbanCardColor', data: ['color' => null], arguments: ['record' => $opportunity->id]);

        expect($opportunity->fresh()->color)->toBeNull();
    });

    it('hides the card colour picker without the edit permission', function () {
        CRMHelper::actingAs(CRMHelper::permissionsFor(['opportunity'], ['view_any', 'view']));

        $opportunity = CRMHelper::opportunity();

        Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
            ->assertActionHidden('kanbanCardColor', ['record' => $opportunity->id]);
    });

    it('counts each stage by activity state and filters a column from the progress bar', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $stage = Stage::query()->where('is_won', false)->orderBy('sort')->firstOrFail();

        $overdue = CRMHelper::opportunity(['stage_id' => $stage->id]);
        pipelineActivity($overdue, now()->subDays(2)->toDateString());
        $planned = CRMHelper::opportunity(['stage_id' => $stage->id]);
        pipelineActivity($planned, now()->addDays(4)->toDateString());
        $none = CRMHelper::opportunity(['stage_id' => $stage->id]);

        $component = Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class);
        $progress = $component->instance()->kanbanColumnProgress(pipelineBoardColumn($component, $stage));

        expect($progress['overdue'])->toBeGreaterThanOrEqual(1)
            ->and($progress['planned'])->toBeGreaterThanOrEqual(1)
            ->and($progress['none'])->toBeGreaterThanOrEqual(1);

        $component->call('toggleKanbanActivityFilter', (string) $stage->id, 'overdue');

        expect(pipelineBoardColumn($component, $stage)->records->modelKeys())
            ->toContain($overdue->id)
            ->not->toContain($planned->id)
            ->not->toContain($none->id);

        $component->call('toggleKanbanActivityFilter', (string) $stage->id, 'overdue');

        expect(pipelineBoardColumn($component, $stage)->records->modelKeys())->toContain($overdue->id, $planned->id, $none->id);
    });

    it('deletes a card only with the delete permission', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $opportunity = CRMHelper::opportunity();

        Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
            ->callAction('deleteKanbanCard', arguments: ['record' => (string) $opportunity->id]);

        expect(Lead::withTrashed()->find($opportunity->id))->toBeNull();

        CRMHelper::actingAs(CRMHelper::permissionsFor(['opportunity'], ['view_any', 'view', 'update']));

        $kept = CRMHelper::opportunity();

        Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
            ->assertActionHidden('deleteKanbanCard', arguments: ['record' => (string) $kept->id]);
    });
});

it('lists a card\'s activities in a modal and opens the schedule form from it', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $opportunity = CRMHelper::opportunity();
    pipelineActivity($opportunity, today()->addDay()->toDateString());

    Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->mountAction('kanbanCardActivities', ['record' => $opportunity->id])
        ->assertActionMounted('kanbanCardActivities')
        ->assertSee('Pipeline card activity')
        ->callAction([
            TestAction::make('kanbanCardActivities')->arguments(['record' => $opportunity->id]),
            TestAction::make('scheduleAnotherKanbanActivity'),
        ])
        ->assertActionMounted('scheduleKanbanActivity');
});
