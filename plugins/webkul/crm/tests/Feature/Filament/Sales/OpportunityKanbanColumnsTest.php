<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\KanbanOpportunities;
use Webkul\Crm\Models\Stage;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

/**
 * Signs in a user who can use the pipeline board and has the given stage abilities.
 *
 * @param  array<int, string>  $stageAffixes
 */
function actingAsStageColumnManager(array $stageAffixes = CrmTestHelper::ALL_AFFIXES): void
{
    CrmTestHelper::actingAs([
        ...CrmTestHelper::permissionsFor(['opportunity']),
        ...CrmTestHelper::permissionsFor(['stage'], $stageAffixes),
    ]);
}

it('adds a stage from the board just before the won stage(s)', function () {
    actingAsStageColumnManager();

    CrmTestHelper::wonStage();

    Livewire::test(KanbanOpportunities::class)
        ->call('addKanbanColumn', '  Negotiation (board test)  ')
        ->assertNotified(__('crm::filament/kanban.add-column.created', ['name' => 'Negotiation (board test)']));

    $stage = Stage::query()->where('name', 'Negotiation (board test)')->firstOrFail();

    $stages = Stage::query()->orderBy('sort')->get();

    expect($stage->is_won)->toBeFalse()
        ->and($stages->where('is_won', true)->min('sort'))->toBeGreaterThan($stage->sort)
        ->and($stages->where('is_won', false)->max('sort'))->toEqual($stage->sort)
        ->and($stages->last()->is_won)->toBeTrue();
});

it('shows the new stage as a board column right away', function () {
    actingAsStageColumnManager();

    $component = Livewire::test(KanbanOpportunities::class)
        ->call('addKanbanColumn', 'Fresh column stage');

    $stage = Stage::query()->where('name', 'Fresh column stage')->firstOrFail();

    expect(collect($component->instance()->getKanbanColumns())->pluck('value')->all())
        ->toContain((string) $stage->id);
});

it('rejects an invalid stage name from the board', function (string $name) {
    actingAsStageColumnManager();

    $countBefore = Stage::query()->count();

    Livewire::test(KanbanOpportunities::class)
        ->call('addKanbanColumn', $name)
        ->assertNotified();

    expect(Stage::query()->count())->toBe($countBefore);
})->with([
    'blank'        => ['   '],
    'empty'        => [''],
    'too long'     => [str_repeat('a', 256)],
]);

it('rejects a duplicate stage name from the board', function () {
    actingAsStageColumnManager();

    Stage::factory()->create(['name' => 'Duplicate board stage']);

    Livewire::test(KanbanOpportunities::class)
        ->call('addKanbanColumn', 'Duplicate board stage')
        ->assertNotified(__('crm::filament/kanban.add-column.exists', ['name' => 'Duplicate board stage']));

    expect(Stage::query()->where('name', 'Duplicate board stage')->count())->toBe(1);
});

it('forbids adding a stage without the create stage permission', function () {
    actingAsStageColumnManager(['view_any', 'view', 'update', 'delete']);

    $component = Livewire::test(KanbanOpportunities::class);

    expect($component->instance()->canAddKanbanColumn())->toBeFalse();

    $component->call('addKanbanColumn', 'Not allowed stage')->assertForbidden();

    expect(Stage::query()->where('name', 'Not allowed stage')->exists())->toBeFalse();
});

it('edits a stage from the board', function () {
    actingAsStageColumnManager();

    $stage = Stage::factory()->create(['name' => 'Editable board stage']);

    Livewire::test(KanbanOpportunities::class)
        ->callAction('editKanbanColumn', data: [
            'name'         => '  Renamed board stage  ',
            'is_won'       => true,
            'requirements' => 'Signed contract',
        ], arguments: ['column' => (string) $stage->id])
        ->assertHasNoActionErrors()
        ->assertNotified(__('crm::filament/kanban.column-actions.saved', ['name' => 'Renamed board stage']));

    $stage->refresh();

    expect($stage->name)->toBe('Renamed board stage')
        ->and($stage->is_won)->toBeTrue()
        ->and($stage->requirements)->toBe('Signed contract');
});

it('pre-fills the edit form with the stage', function () {
    actingAsStageColumnManager();

    $stage = Stage::factory()->create(['name' => 'Prefilled board stage', 'requirements' => 'Budget known']);

    Livewire::test(KanbanOpportunities::class)
        ->mountAction('editKanbanColumn', ['column' => (string) $stage->id])
        ->assertActionDataSet([
            'name'         => 'Prefilled board stage',
            'requirements' => 'Budget known',
        ]);
});

it('rejects renaming a stage to a name that already exists', function () {
    actingAsStageColumnManager();

    Stage::factory()->create(['name' => 'Existing board stage']);

    $stage = Stage::factory()->create(['name' => 'Stage to rename']);

    Livewire::test(KanbanOpportunities::class)
        ->callAction('editKanbanColumn', data: ['name' => 'Existing board stage'], arguments: ['column' => (string) $stage->id])
        ->assertNotified(__('crm::filament/kanban.add-column.exists', ['name' => 'Existing board stage']));

    expect($stage->refresh()->name)->toBe('Stage to rename')
        ->and(Stage::query()->where('name', 'Existing board stage')->count())->toBe(1);
});

it('keeps the name when saving a stage under its own name', function () {
    actingAsStageColumnManager();

    $stage = Stage::factory()->create(['name' => 'Unchanged board stage']);

    Livewire::test(KanbanOpportunities::class)
        ->callAction('editKanbanColumn', data: ['name' => 'Unchanged board stage', 'requirements' => 'New note'], arguments: ['column' => (string) $stage->id])
        ->assertHasNoActionErrors();

    expect($stage->refresh()->requirements)->toBe('New note');
});

it('forbids editing a stage from the board without the update permission', function () {
    actingAsStageColumnManager(['view_any', 'view', 'create', 'delete']);

    $stage = Stage::factory()->create(['name' => 'Locked board stage']);

    Livewire::test(KanbanOpportunities::class)
        ->callAction('editKanbanColumn', data: ['name' => 'Hacked name'], arguments: ['column' => (string) $stage->id])
        ->assertForbidden();

    expect($stage->refresh()->name)->toBe('Locked board stage');
});

it('deletes an empty stage from the board', function () {
    actingAsStageColumnManager();

    $stage = Stage::factory()->create(['name' => 'Empty board stage']);

    Livewire::test(KanbanOpportunities::class)
        ->callAction('deleteKanbanColumn', arguments: ['column' => (string) $stage->id])
        ->assertNotified(__('crm::filament/kanban.column-actions.deleted', ['name' => 'Empty board stage']));

    expect(Stage::query()->whereKey($stage->id)->exists())->toBeFalse();
});

it('refuses to delete a stage that still holds records', function (string $holder) {
    actingAsStageColumnManager();

    $stage = Stage::factory()->create(['name' => 'Busy board stage']);

    match ($holder) {
        'opportunity'      => CrmTestHelper::opportunity(['stage_id' => $stage->id]),
        'lead'             => CrmTestHelper::lead(['stage_id' => $stage->id]),
        'lost opportunity' => CrmTestHelper::opportunity(['stage_id' => $stage->id])->markAsLost(null),
        'lost lead'        => CrmTestHelper::lead(['stage_id' => $stage->id])->markAsLost(null),
    };

    Livewire::test(KanbanOpportunities::class)
        ->callAction('deleteKanbanColumn', arguments: ['column' => (string) $stage->id])
        ->assertNotified(__('crm::filament/kanban.column-actions.in-use.title', ['name' => 'Busy board stage']));

    expect(Stage::query()->whereKey($stage->id)->exists())->toBeTrue();
})->with(['opportunity', 'lead', 'lost opportunity', 'lost lead']);

it('forbids deleting a stage from the board without the delete permission', function () {
    actingAsStageColumnManager(['view_any', 'view', 'create', 'update']);

    $stage = Stage::factory()->create(['name' => 'Undeletable board stage']);

    Livewire::test(KanbanOpportunities::class)
        ->callAction('deleteKanbanColumn', arguments: ['column' => (string) $stage->id])
        ->assertForbidden();

    expect(Stage::query()->whereKey($stage->id)->exists())->toBeTrue();
});

it('shows the column settings according to the stage permissions', function (array $affixes, bool $canEdit, bool $canDelete) {
    actingAsStageColumnManager($affixes);

    $stage = Stage::factory()->create(['name' => 'Permission board stage']);

    $page = Livewire::test(KanbanOpportunities::class)->instance();

    expect($page->canEditKanbanColumn((string) $stage->id))->toBe($canEdit)
        ->and($page->canDeleteKanbanColumn((string) $stage->id))->toBe($canDelete);
})->with([
    'update and delete' => [['view_any', 'update', 'delete'], true, true],
    'update only'       => [['view_any', 'update'], true, false],
    'delete only'       => [['view_any', 'delete'], false, true],
    'neither'           => [['view_any', 'view', 'create'], false, false],
]);

it('rejects column values that are not stage ids', function (string $value) {
    actingAsStageColumnManager();

    $component = Livewire::test(KanbanOpportunities::class);

    expect($component->instance()->canEditKanbanColumn($value))->toBeFalse()
        ->and($component->instance()->canDeleteKanbanColumn($value))->toBeFalse();

    $component
        ->callAction('deleteKanbanColumn', arguments: ['column' => $value])
        ->assertForbidden();
})->with([
    'text'           => ['abc'],
    'negative'       => ['-1'],
    'decimal'        => ['1.5'],
    'sql'            => ['1 OR 1=1'],
    'empty'          => [''],
    'unknown number' => ['999999999'],
]);
