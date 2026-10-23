<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages\KanbanActivities;
use Webkul\Crm\Models\Lead;
use Webkul\Support\Models\ActivityType;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

function actingAsActivityTypeColumnManager(array $affixes = CrmTestHelper::ALL_AFFIXES): void
{
    CrmTestHelper::actingAs([
        ...CrmTestHelper::permissionsFor(['activity', 'opportunity']),
        ...CrmTestHelper::permissionsFor(['activity::type'], $affixes),
    ]);
}

function boardActivityType(string $name, string $plugin = 'crm'): ActivityType
{
    return ActivityType::query()->create([
        'name'       => $name,
        'summary'    => $name,
        'plugin'     => $plugin,
        'delay_unit' => 'days',
        'delay_from' => 'current_date',
        'is_active'  => true,
    ]);
}

function scheduleOnBoardType(ActivityType $type, ?Lead $lead = null): void
{
    ($lead ?? CrmTestHelper::opportunity())->addMessage([
        'type'             => 'activity',
        'activity_type_id' => $type->id,
        'date_deadline'    => now()->addDay()->toDateString(),
        'summary'          => 'Uses the board type',
        'assigned_to'      => auth()->id(),
    ]);
}

it('adds a crm activity type with the installed defaults', function () {
    actingAsActivityTypeColumnManager();

    $component = Livewire::test(KanbanActivities::class)
        ->call('addKanbanColumn', '  Site Visit (board test)  ')
        ->assertNotified(__('crm::filament/kanban.add-column.created', ['name' => 'Site Visit (board test)']));

    $type = ActivityType::query()->where('name', 'Site Visit (board test)')->firstOrFail();

    expect($type->plugin)->toBe('crm')
        ->and($type->summary)->toBe('Site Visit (board test)')
        ->and((int) $type->delay_count)->toBe(0)
        ->and($type->delay_unit)->toBe('days')
        ->and($type->delay_from)->toBe('current_date')
        ->and($type->category)->toBe('default')
        ->and($type->decoration_type)->toBe('alert')
        ->and($type->chaining_type)->toBe('suggest')
        ->and($type->icon)->toBe('heroicon-o-clock')
        ->and($type->is_active)->toBeTrue();

    expect(collect($component->instance()->getKanbanColumns())->pluck('value')->all())
        ->toContain((string) $type->id);
});

it('rejects a duplicate crm activity type name', function () {
    actingAsActivityTypeColumnManager();

    boardActivityType('Duplicate board type');

    Livewire::test(KanbanActivities::class)
        ->call('addKanbanColumn', 'Duplicate board type')
        ->assertNotified(__('crm::filament/kanban.add-column.exists', ['name' => 'Duplicate board type']));

    expect(ActivityType::query()->where('plugin', 'crm')->where('name', 'Duplicate board type')->count())->toBe(1);
});

it('allows a name that only another plugin uses', function () {
    actingAsActivityTypeColumnManager();

    boardActivityType('Shared plugin type name', 'sales');

    Livewire::test(KanbanActivities::class)
        ->call('addKanbanColumn', 'Shared plugin type name');

    expect(ActivityType::query()->where('plugin', 'crm')->where('name', 'Shared plugin type name')->exists())->toBeTrue();
});

it('rejects a blank or too long activity type name', function (string $name) {
    actingAsActivityTypeColumnManager();

    $countBefore = ActivityType::query()->count();

    Livewire::test(KanbanActivities::class)
        ->call('addKanbanColumn', $name)
        ->assertNotified();

    expect(ActivityType::query()->count())->toBe($countBefore);
})->with([
    'blank'    => ['   '],
    'too long' => [str_repeat('b', 256)],
]);

it('forbids adding an activity type without the create permission', function () {
    actingAsActivityTypeColumnManager(['view_any', 'view', 'update', 'delete']);

    $component = Livewire::test(KanbanActivities::class);

    expect($component->instance()->canAddKanbanColumn())->toBeFalse();

    $component->call('addKanbanColumn', 'Forbidden board type')->assertForbidden();

    expect(ActivityType::query()->where('name', 'Forbidden board type')->exists())->toBeFalse();
});

it('renames a crm activity type from the board', function () {
    actingAsActivityTypeColumnManager();

    $type = boardActivityType('Type before rename');

    Livewire::test(KanbanActivities::class)
        ->callAction('editKanbanColumn', data: ['name' => 'Type after rename'], arguments: ['column' => (string) $type->id])
        ->assertHasNoActionErrors()
        ->assertNotified(__('crm::filament/kanban.column-actions.saved', ['name' => 'Type after rename']));

    expect($type->refresh()->name)->toBe('Type after rename');
});

it('rejects renaming an activity type to an existing crm name', function () {
    actingAsActivityTypeColumnManager();

    boardActivityType('Taken board type');

    $type = boardActivityType('Type to keep');

    Livewire::test(KanbanActivities::class)
        ->callAction('editKanbanColumn', data: ['name' => 'Taken board type'], arguments: ['column' => (string) $type->id])
        ->assertNotified(__('crm::filament/kanban.add-column.exists', ['name' => 'Taken board type']));

    expect($type->refresh()->name)->toBe('Type to keep');
});

it('deletes an unused crm activity type from the board', function () {
    actingAsActivityTypeColumnManager();

    $type = boardActivityType('Unused board type');

    Livewire::test(KanbanActivities::class)
        ->callAction('deleteKanbanColumn', arguments: ['column' => (string) $type->id])
        ->assertNotified(__('crm::filament/kanban.column-actions.deleted', ['name' => 'Unused board type']));

    expect(ActivityType::query()->whereKey($type->id)->exists())->toBeFalse();
});

it('refuses to delete an activity type while activities are scheduled with it', function (bool $onLostRecord) {
    actingAsActivityTypeColumnManager();

    $type = boardActivityType('Busy board type');

    $lead = CrmTestHelper::opportunity();

    scheduleOnBoardType($type, $lead);

    if ($onLostRecord) {
        $lead->markAsLost(null);
    }

    Livewire::test(KanbanActivities::class)
        ->callAction('deleteKanbanColumn', arguments: ['column' => (string) $type->id])
        ->assertNotified(__('crm::filament/kanban.column-actions.in-use.title', ['name' => 'Busy board type']));

    expect(ActivityType::query()->whereKey($type->id)->exists())->toBeTrue();
})->with([
    'on an open opportunity' => [false],
    'on a lost opportunity'  => [true],
]);

it('cannot manage activity types of other plugins from the board', function () {
    actingAsActivityTypeColumnManager();

    $salesType = boardActivityType('Sales plugin board type', 'sales');

    scheduleOnBoardType($salesType);

    $component = Livewire::test(KanbanActivities::class);

    expect(collect($component->instance()->getKanbanColumns())->pluck('value')->all())
        ->toContain((string) $salesType->id)
        ->and($component->instance()->canEditKanbanColumn((string) $salesType->id))->toBeFalse()
        ->and($component->instance()->canDeleteKanbanColumn((string) $salesType->id))->toBeFalse();

    $component
        ->callAction('editKanbanColumn', data: ['name' => 'Hijacked'], arguments: ['column' => (string) $salesType->id])
        ->assertForbidden();

    expect($salesType->refresh()->name)->toBe('Sales plugin board type');
});

it('hides the column settings without the update and delete permissions', function () {
    actingAsActivityTypeColumnManager(['view_any', 'view', 'create']);

    $type = boardActivityType('Read-only board type');

    $page = Livewire::test(KanbanActivities::class)->instance();

    expect($page->canEditKanbanColumn((string) $type->id))->toBeFalse()
        ->and($page->canDeleteKanbanColumn((string) $type->id))->toBeFalse();
});

it('lets the crm activity type permissions alone manage a crm column', function () {
    actingAsActivityTypeColumnManager();

    $type = boardActivityType('Crm permission board type');

    $page = Livewire::test(KanbanActivities::class)->instance();

    expect($page->canEditKanbanColumn((string) $type->id))->toBeTrue()
        ->and($page->canDeleteKanbanColumn((string) $type->id))->toBeTrue();
});

it('does not let the support activity type permissions manage a crm column', function () {
    CrmTestHelper::actingAs([
        ...CrmTestHelper::permissionsFor(['activity', 'opportunity']),
        ...array_map(fn (string $affix): string => "{$affix}_support_activity::type", CrmTestHelper::ALL_AFFIXES),
    ]);

    $type = boardActivityType('Support permission board type');

    $page = Livewire::test(KanbanActivities::class)->instance();

    expect($page->canAddKanbanColumn())->toBeFalse()
        ->and($page->canEditKanbanColumn((string) $type->id))->toBeFalse()
        ->and($page->canDeleteKanbanColumn((string) $type->id))->toBeFalse();
});

it('rejects column values that are not activity type ids', function (string $value) {
    actingAsActivityTypeColumnManager();

    $page = Livewire::test(KanbanActivities::class)->instance();

    expect($page->canEditKanbanColumn($value))->toBeFalse()
        ->and($page->canDeleteKanbanColumn($value))->toBeFalse();
})->with(['abc', '-1', '1.5', '']);
