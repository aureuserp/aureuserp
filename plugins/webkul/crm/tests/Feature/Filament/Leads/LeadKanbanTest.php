<?php

use Livewire\Livewire;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\KanbanLeads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ListLeads;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\KanbanOpportunities;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Crm\Models\Tag;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UTMSource;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

/**
 * The ids of every card on the board, across all columns.
 *
 * @return array<int, int>
 */
function leadKanbanCardIds(KanbanLeads $page): array
{
    return collect($page->getKanbanColumns())
        ->flatMap(fn ($column): array => $column->records->modelKeys())
        ->map(fn (mixed $id): int => (int) $id)
        ->all();
}

function leadKanbanOtherUser(): User
{
    return User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => CrmTestHelper::companyId(),
        'is_active'          => true,
    ]));
}

it('is the index page of the leads resource', function () {
    expect(LeadResource::getPages()['index']->getPage())->toBe(KanbanLeads::class)
        ->and(LeadResource::getPages()['list']->getPage())->toBe(ListLeads::class);
});

it('renders the leads board with the lead permission', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    Livewire::test(KanbanLeads::class)->assertOk();
});

it('forbids the leads board without the lead permission', function (array $permissions) {
    CrmTestHelper::actingAs($permissions);

    Livewire::test(KanbanLeads::class)->assertForbidden();
})->with([
    'no permissions'            => [[]],
    'opportunity permissions'   => [CrmTestHelper::permissionsFor(['opportunity'])],
]);

it('shows only leads on the board, never opportunities', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $lead = CrmTestHelper::lead();
    $opportunity = CrmTestHelper::opportunity();

    $cardIds = leadKanbanCardIds(Livewire::test(KanbanLeads::class)->instance());

    expect($cardIds)->toContain($lead->id)
        ->not->toContain($opportunity->id);
});

it('hides lost leads from the board', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $lead = CrmTestHelper::lead();
    $lostLead = CrmTestHelper::lead();
    $lostLead->markAsLost(null);

    $cardIds = leadKanbanCardIds(Livewire::test(KanbanLeads::class)->instance());

    expect($cardIds)->toContain($lead->id)
        ->not->toContain($lostLead->id);
});

it('uses the non-won stages as columns', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $wonStage = CrmTestHelper::wonStage();

    $columnValues = collect(Livewire::test(KanbanLeads::class)->instance()->getKanbanColumns())->pluck('value')->all();

    $expected = Stage::query()->where('is_won', false)->orderBy('sort')->pluck('id')->map(fn (int $id): string => (string) $id)->all();

    expect($columnValues)->toBe($expected)
        ->not->toContain((string) $wonStage->id);
});

it('puts each lead in the column of its stage', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $stage = Stage::factory()->create(['name' => 'Lead Kanban Column Stage']);
    $lead = CrmTestHelper::lead(['stage_id' => $stage->id]);

    $column = collect(Livewire::test(KanbanLeads::class)->instance()->getKanbanColumns())
        ->firstWhere('value', (string) $stage->id);

    expect($column)->not->toBeNull()
        ->and($column->label)->toBe('Lead Kanban Column Stage')
        ->and($column->records->modelKeys())->toBe([$lead->id])
        ->and($column->count)->toBe(1);
});

it('opens on the current user leads', function () {
    $user = CrmTestHelper::actingAsCrmUser(['lead']);

    $mine = CrmTestHelper::lead();
    $theirs = CrmTestHelper::lead(['user_id' => leadKanbanOtherUser()->id]);

    $component = Livewire::test(KanbanLeads::class);

    expect($component->get('kanbanFilters'))->toBe([...KanbanLeads::emptyKanbanFilters(), 'user_id' => $user->id]);

    expect(leadKanbanCardIds($component->instance()))->toContain($mine->id)
        ->not->toContain($theirs->id);
});

it('shows every salesperson once the salesperson filter is cleared', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $mine = CrmTestHelper::lead();
    $theirs = CrmTestHelper::lead(['user_id' => leadKanbanOtherUser()->id]);

    $component = Livewire::test(KanbanLeads::class)->call('resetKanbanFilters');

    expect(leadKanbanCardIds($component->instance()))->toContain($mine->id, $theirs->id);
});

it('filters the board by sales team and priority', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId()]);

    $inTeam = CrmTestHelper::lead(['team_id' => $team->id, 'priority' => OpportunityPriority::High]);
    $inTeamLow = CrmTestHelper::lead(['team_id' => $team->id, 'priority' => OpportunityPriority::Low]);
    $noTeam = CrmTestHelper::lead(['team_id' => null, 'priority' => OpportunityPriority::High]);

    $component = Livewire::test(KanbanLeads::class)->set('kanbanFilters.team_id', $team->id);

    expect(leadKanbanCardIds($component->instance()))->toContain($inTeam->id, $inTeamLow->id)
        ->not->toContain($noTeam->id);

    $component->set('kanbanFilters.priority', OpportunityPriority::High->value);

    expect(leadKanbanCardIds($component->instance()))->toContain($inTeam->id)
        ->not->toContain($inTeamLow->id)
        ->not->toContain($noTeam->id);
});

it('searches leads by name, contact, company and email', function (string $attribute) {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $match = CrmTestHelper::lead([$attribute => $attribute === 'email' ? 'Zyxwvu-kanban@example.com' : 'Zyxwvu Kanban Search']);
    $other = CrmTestHelper::lead();

    $component = Livewire::test(KanbanLeads::class)->set('kanbanSearch', 'Zyxwvu');

    expect(leadKanbanCardIds($component->instance()))->toBe([$match->id])
        ->not->toContain($other->id);
})->with(['name', 'contact_name', 'company_name', 'email']);

it('quick-creates a lead in the clicked column', function () {
    $user = CrmTestHelper::actingAsCrmUser(['lead']);

    $stage = Stage::factory()->create(['name' => 'Lead Quick Create Stage']);

    Livewire::test(KanbanLeads::class)
        ->callAction('kanbanColumn', [
            'name'         => 'Quick created lead',
            'contact_name' => 'Quick Contact',
            'company_name' => 'Quick Company',
            'email'        => 'quick-lead@example.com',
        ], ['column' => (string) $stage->id])
        ->assertHasNoFormErrors();

    $lead = Lead::query()->where('name', 'Quick created lead')->firstOrFail();

    expect($lead->type)->toBe(Lead::TYPE_LEAD)
        ->and($lead->stage_id)->toBe($stage->id)
        ->and($lead->company_id)->toBe(CrmTestHelper::companyId())
        ->and($lead->user_id)->toBe($user->id)
        ->and($lead->contact_name)->toBe('Quick Contact')
        ->and($lead->team_id)->toBeNull();
});

it('requires a name to quick-create a lead', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    Livewire::test(KanbanLeads::class)
        ->callAction('kanbanColumn', ['name' => null], ['column' => (string) CrmTestHelper::firstStage()->id])
        ->assertHasFormErrors(['name' => 'required']);
});

it('never quick-creates a lead in a won stage', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $wonStage = CrmTestHelper::wonStage();

    Livewire::test(KanbanLeads::class)
        ->callAction('kanbanColumn', ['name' => 'Lead aimed at won'], ['column' => (string) $wonStage->id]);

    $lead = Lead::query()->where('name', 'Lead aimed at won')->firstOrFail();

    expect($lead->stage_id)->toBe(Stage::query()->where('is_won', false)->orderBy('sort')->value('id'));
});

it('quick-creates with the filtered team only when it belongs to the current company', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $ownTeam = Team::factory()->create(['company_id' => CrmTestHelper::companyId()]);
    $foreignTeam = Team::factory()->create(['company_id' => Company::factory()->create()->id]);

    Livewire::test(KanbanLeads::class)
        ->set('kanbanFilters.team_id', $ownTeam->id)
        ->callAction('kanbanColumn', ['name' => 'Lead in own team'], ['column' => (string) CrmTestHelper::firstStage()->id])
        ->set('kanbanFilters.team_id', $foreignTeam->id)
        ->callAction('kanbanColumn', ['name' => 'Lead in foreign team'], ['column' => (string) CrmTestHelper::firstStage()->id]);

    expect(Lead::query()->where('name', 'Lead in own team')->value('team_id'))->toBe($ownTeam->id)
        ->and(Lead::query()->where('name', 'Lead in foreign team')->value('team_id'))->toBeNull();
});

it('hides quick-create without the lead create permission', function () {
    CrmTestHelper::actingAs(['view_any_crm_lead', 'view_crm_lead']);

    Livewire::test(KanbanLeads::class)
        ->assertActionHidden('kanbanColumn')
        ->assertActionHidden('create');
});

it('moves a lead to another stage', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $lead = CrmTestHelper::lead(['stage_id' => CrmTestHelper::firstStage()->id]);
    $stage = Stage::factory()->create(['name' => 'Lead Move Target']);

    Livewire::test(KanbanLeads::class)->call('moveRecord', (string) $lead->id, (string) $stage->id, []);

    expect($lead->fresh()->stage_id)->toBe($stage->id);
});

it('refuses to move a lead without the lead update permission', function () {
    CrmTestHelper::actingAs(['view_any_crm_lead', 'view_crm_lead', 'update_crm_opportunity']);

    $firstStage = CrmTestHelper::firstStage();
    $lead = CrmTestHelper::lead(['stage_id' => $firstStage->id]);
    $stage = Stage::factory()->create(['name' => 'Lead Move Forbidden Target']);

    Livewire::test(KanbanLeads::class)->call('moveRecord', (string) $lead->id, (string) $stage->id, []);

    expect($lead->fresh()->stage_id)->toBe($firstStage->id);
});

it('shows a single "List" toggle on the board', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    Livewire::test(KanbanLeads::class)
        ->assertActionExists('listView')
        ->assertActionHasLabel('listView', 'List')
        ->assertActionHasUrl('listView', LeadResource::getUrl('list'))
        ->assertActionDoesNotExist('kanbanView');
});

it('shows a single "Kanban" toggle on the list', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    Livewire::test(ListLeads::class)
        ->assertActionExists('kanbanView')
        ->assertActionHasLabel('kanbanView', 'Kanban')
        ->assertActionHasUrl('kanbanView', LeadResource::getUrl('index'))
        ->assertActionDoesNotExist('listView');
});

it('builds the view toggle for the other view', function (string $currentPage, string $expectedName) {
    $actions = LeadResource::getViewSwitcherActions($currentPage);

    expect($actions)->toHaveCount(1)
        ->and($actions[0]->getName())->toBe($expectedName);
})->with([
    'kanban shows list' => ['index', 'listView'],
    'list shows kanban' => ['list', 'kanbanView'],
]);

it('shows the "Add Stage" column to users who can create stages', function () {
    CrmTestHelper::actingAsCrmUser(['lead', 'stage']);

    Livewire::test(KanbanLeads::class)->assertSee('Add Stage');
});

it('hides the "Add Stage" column from users who cannot create stages', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $component = Livewire::test(KanbanLeads::class)->assertDontSee('Add Stage');

    expect($component->instance()->canAddKanbanColumn())->toBeFalse();
});

it('adds a stage from the leads board before the won stage, and it appears on the pipeline board', function () {
    CrmTestHelper::actingAsCrmUser(['lead', 'opportunity', 'stage']);

    $wonStage = CrmTestHelper::wonStage();

    $component = Livewire::test(KanbanLeads::class)->call('addKanbanColumn', '  Lead Board Stage  ');

    $stage = Stage::query()->where('name', 'Lead Board Stage')->firstOrFail();

    expect($stage->is_won)->toBeFalse()
        ->and($stage->sort)->toBeLessThan($wonStage->fresh()->sort)
        ->and(collect($component->instance()->getKanbanColumns())->pluck('value'))->toContain((string) $stage->id);

    $pipelineColumns = collect(Livewire::test(KanbanOpportunities::class)->instance()->getKanbanColumns())->pluck('value');

    expect($pipelineColumns)->toContain((string) $stage->id);
});

it('does not add a stage whose name already exists or is blank', function () {
    CrmTestHelper::actingAsCrmUser(['lead', 'stage']);

    $existing = CrmTestHelper::firstStage();
    $stageCount = Stage::query()->count();

    Livewire::test(KanbanLeads::class)
        ->call('addKanbanColumn', $existing->name)
        ->call('addKanbanColumn', '   ');

    expect(Stage::query()->count())->toBe($stageCount);
});

it('forbids adding a stage without the stage create permission', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    Livewire::test(KanbanLeads::class)
        ->call('addKanbanColumn', 'Forbidden Stage')
        ->assertForbidden();

    expect(Stage::query()->where('name', 'Forbidden Stage')->exists())->toBeFalse();
});

it('offers the column gear menu only with the stage permissions', function () {
    $stage = Stage::factory()->create(['name' => 'Lead Gear Stage']);

    CrmTestHelper::actingAsCrmUser(['lead', 'stage']);

    $page = Livewire::test(KanbanLeads::class)->instance();

    expect($page->canEditKanbanColumn((string) $stage->id))->toBeTrue()
        ->and($page->canDeleteKanbanColumn((string) $stage->id))->toBeTrue()
        ->and($page->canEditKanbanColumn('not-a-stage'))->toBeFalse();

    CrmTestHelper::actingAsCrmUser(['lead']);

    $page = Livewire::test(KanbanLeads::class)->instance();

    expect($page->canEditKanbanColumn((string) $stage->id))->toBeFalse()
        ->and($page->canDeleteKanbanColumn((string) $stage->id))->toBeFalse();
});

it('renames a stage from the column gear menu', function () {
    CrmTestHelper::actingAsCrmUser(['lead', 'stage']);

    $stage = Stage::factory()->create(['name' => 'Lead Stage Before Rename']);

    Livewire::test(KanbanLeads::class)
        ->callAction('editKanbanColumn', ['name' => 'Lead Stage After Rename'], ['column' => (string) $stage->id])
        ->assertHasNoFormErrors();

    expect($stage->fresh()->name)->toBe('Lead Stage After Rename');
});

it('deletes an empty stage from the column gear menu but keeps a stage that still has leads', function () {
    CrmTestHelper::actingAsCrmUser(['lead', 'stage']);

    $emptyStage = Stage::factory()->create(['name' => 'Lead Empty Stage']);
    $usedStage = Stage::factory()->create(['name' => 'Lead Used Stage']);
    $lostLead = CrmTestHelper::lead(['stage_id' => $usedStage->id]);
    $lostLead->markAsLost(null);

    Livewire::test(KanbanLeads::class)
        ->callAction('deleteKanbanColumn', [], ['column' => (string) $emptyStage->id])
        ->callAction('deleteKanbanColumn', [], ['column' => (string) $usedStage->id]);

    expect(Stage::query()->whereKey($emptyStage->id)->exists())->toBeFalse()
        ->and(Stage::query()->whereKey($usedStage->id)->exists())->toBeTrue();
});

it('shows no left sub-navigation because the leads section has a single item', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    expect(Livewire::test(KanbanLeads::class)->instance()->getSubNavigation())->toBe([])
        ->and(Livewire::test(ListLeads::class)->instance()->getSubNavigation())->toBe([]);
});

describe('list view filters on the board', function () {
    it('filters by status, tags and source like the list', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $tag = Tag::query()->create(['name' => 'Lead board tag '.uniqid()]);
        $source = UTMSource::query()->create(['name' => 'Lead board source '.uniqid()]);

        $tagged = CrmTestHelper::lead();
        $tagged->tags()->attach($tag->id);
        $fromSource = CrmTestHelper::lead(['utm_source_id' => $source->id]);
        $unassigned = CrmTestHelper::lead();
        $unassigned->forceFill(['user_id' => null])->saveQuietly();
        $lost = CrmTestHelper::lead();
        $lost->markAsLost(null);

        $ids = fn (array $filters): array => leadKanbanCardIds(Livewire::test(KanbanLeads::class)
            ->set('kanbanFilters', [...KanbanLeads::emptyKanbanFilters(), ...$filters])
            ->instance());

        expect($ids([]))->not->toContain($lost->id)
            ->and($ids(['status' => 'lost']))->toBe([$lost->id])
            ->and($ids(['status' => 'unassigned']))->toContain($unassigned->id)->not->toContain($tagged->id)
            ->and($ids(['tags' => [$tag->id]]))->toBe([$tagged->id])
            ->and($ids(['utm_source_id' => $source->id]))->toBe([$fromSource->id]);
    });
});
