<?php

use Filament\Actions\Action;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Webkul\Chatter\Models\Message;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions\ScheduleActivityAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages\GraphActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages\KanbanActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages\ListActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByStatusChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByTypeChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByUserChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivityStatsOverview;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ViewOpportunity;
use Webkul\Crm\Filament\RelationManagers\ActivitiesRelationManager;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\Lead;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityType;
use Webkul\Support\Models\Company;
use Wezlo\FilamentKanban\KanbanColumn;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

function actingAsActivityPlanner(): User
{
    return CrmTestHelper::actingAsCrmUser(['activity', 'opportunity', 'lead']);
}

function crmActivityType(string $name = 'Email'): ActivityType
{
    return ActivityType::query()->where('plugin', 'crm')->where('name', $name)->firstOrFail();
}

function myActivitiesOtherUserId(): int
{
    return (int) User::query()->whereKeyNot(auth()->id())->value('id');
}

function scheduledCrmActivity(Lead $lead, array $attributes = []): Activity
{
    $message = $lead->addMessage([
        'type'             => 'activity',
        'activity_type_id' => crmActivityType()->id,
        'date_deadline'    => now()->addDays(3)->toDateString(),
        'summary'          => 'Send the proposal',
        'assigned_to'      => auth()->id(),
        ...$attributes,
    ]);

    return Activity::query()->findOrFail($message->getKey());
}

/**
 * Whether any activity was scheduled on the given lead, ignoring every scope.
 */
function leadHasAnyActivity(Lead $lead): bool
{
    return Message::query()
        ->withoutGlobalScopes()
        ->where('type', 'activity')
        ->where('messageable_type', $lead->getMorphClass())
        ->where('messageable_id', $lead->getKey())
        ->exists();
}

it('opens on the kanban board and keeps the list and graph on their own urls', function () {
    $pages = ActivityResource::getPages();

    expect($pages['index']->getPage())->toBe(KanbanActivities::class)
        ->and($pages['list']->getPage())->toBe(ListActivities::class)
        ->and($pages['graph']->getPage())->toBe(GraphActivities::class)
        ->and(array_keys($pages))->toBe(['index', 'list', 'graph']);

    expect(ActivityResource::getUrl('index'))->toEndWith('/crm/sales/activities')
        ->and(ActivityResource::getUrl('list'))->toEndWith('/crm/sales/activities/list')
        ->and(ActivityResource::getUrl('graph'))->toEndWith('/crm/sales/activities/graph');
});

it('only offers the views the user is not on', function (string $currentPage, array $expected) {
    actingAsActivityPlanner();

    $names = collect(ActivityResource::getViewSwitcherActions($currentPage))
        ->map(fn (Action $action): string => $action->getName())
        ->all();

    expect($names)->toBe([...$expected, 'scheduleActivity']);
})->with([
    'kanban' => ['index', ['listView', 'graphView']],
    'list'   => ['list', ['kanbanView', 'graphView']],
    'graph'  => ['graph', ['kanbanView', 'listView']],
]);

it('shows the view toggle in each page header', function (string $page, array $visible, string $missing) {
    actingAsActivityPlanner();

    $component = Livewire::test($page)->assertOk();

    foreach ($visible as $name) {
        $component->assertActionVisible($name);
    }

    $component->assertActionDoesNotExist($missing);
})->with([
    'kanban' => [KanbanActivities::class, ['listView', 'graphView', 'scheduleActivity'], 'kanbanView'],
    'list'   => [ListActivities::class, ['kanbanView', 'graphView', 'scheduleActivity'], 'listView'],
    'graph'  => [GraphActivities::class, ['kanbanView', 'listView', 'scheduleActivity'], 'graphView'],
]);

it('forbids every view without the view any activity permission', function (string $page) {
    CrmTestHelper::actingAsCrmUser(['opportunity']);

    Livewire::test($page)->assertForbidden();
})->with([
    'kanban' => [KanbanActivities::class],
    'list'   => [ListActivities::class],
    'graph'  => [GraphActivities::class],
]);

it('lists activities on leads and opportunities but not on other records', function () {
    actingAsActivityPlanner();

    $onOpportunity = scheduledCrmActivity(CrmTestHelper::opportunity());
    $onLead = scheduledCrmActivity(CrmTestHelper::lead());

    $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId(), 'is_active' => true]);

    $onTeam = $team->addMessage([
        'type'             => 'activity',
        'activity_type_id' => crmActivityType()->id,
        'date_deadline'    => now()->addDay()->toDateString(),
        'summary'          => 'Not a CRM activity',
        'assigned_to'      => auth()->id(),
    ]);

    expect(Activity::query()->whereKey($onTeam->id)->exists())->toBeFalse();

    Livewire::test(ListActivities::class)
        ->assertOk()
        ->call('loadView', 'all')
        ->assertCanSeeTableRecords([$onOpportunity, $onLead])
        ->assertCanNotSeeTableRecords([$onTeam]);
});

it('hides the activities of lost leads and opportunities', function () {
    actingAsActivityPlanner();

    $lostOpportunity = CrmTestHelper::opportunity();
    $lostLead = CrmTestHelper::lead();

    $onLostOpportunity = scheduledCrmActivity($lostOpportunity);
    $onLostLead = scheduledCrmActivity($lostLead);

    $lostOpportunity->markAsLost(null);
    $lostLead->markAsLost(null);

    Livewire::test(ListActivities::class)
        ->call('loadView', 'all')
        ->assertCanNotSeeTableRecords([$onLostOpportunity, $onLostLead]);
});

it('shows the current user\'s activities by default and everyone\'s in the all view', function () {
    actingAsActivityPlanner();

    $opportunity = CrmTestHelper::opportunity();

    $mine = scheduledCrmActivity($opportunity);
    $someoneElses = scheduledCrmActivity($opportunity, ['assigned_to' => myActivitiesOtherUserId()]);

    $component = Livewire::test(ListActivities::class)
        ->assertSet('activeTableView', 'my_activities')
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$someoneElses]);

    $component
        ->call('loadView', 'all')
        ->assertCanSeeTableRecords([$mine, $someoneElses]);
});

it('splits the user\'s activities by due status', function (string $view, string $visible) {
    actingAsActivityPlanner();

    $opportunity = CrmTestHelper::opportunity();

    $activities = [
        'overdue' => scheduledCrmActivity($opportunity, ['date_deadline' => now()->subDays(2)->toDateString()]),
        'today'   => scheduledCrmActivity($opportunity, ['date_deadline' => now()->toDateString()]),
        'planned' => scheduledCrmActivity($opportunity, ['date_deadline' => now()->addDays(5)->toDateString()]),
    ];

    Livewire::test(ListActivities::class)
        ->call('loadView', $view)
        ->assertCanSeeTableRecords([$activities[$visible]])
        ->assertCanNotSeeTableRecords(collect($activities)->except($visible)->values()->all());
})->with([
    'overdue' => ['overdue', 'overdue'],
    'today'   => ['today', 'today'],
    'planned' => ['planned', 'planned'],
]);

it('schedules an activity on a chosen opportunity', function () {
    $user = actingAsActivityPlanner();

    $opportunity = CrmTestHelper::opportunity(['name' => 'Opportunity to follow up']);

    $type = crmActivityType('Call');

    Livewire::test(ListActivities::class)
        ->callAction('scheduleActivity', data: [
            'lead_id'          => $opportunity->id,
            'activity_type_id' => $type->id,
            'date_deadline'    => now()->addDays(4)->toDateString(),
            'summary'          => 'Call about the offer',
            'assigned_to'      => $user->id,
        ])
        ->assertHasNoActionErrors()
        ->assertNotified(__('crm::filament/clusters/sales/resources/activity.actions.schedule.notification.success.title'));

    $activity = $opportunity->crmActivities()->firstOrFail();

    expect($activity->activity_type_id)->toBe($type->id)
        ->and($activity->summary)->toBe('Call about the offer')
        ->and($activity->assigned_to)->toBe($user->id)
        ->and($activity->date_deadline->toDateString())->toBe(now()->addDays(4)->toDateString());
});

it('does not schedule on an opportunity outside the picker', function (string $case) {
    $user = actingAsActivityPlanner();

    $target = match ($case) {
        'another company' => CrmTestHelper::opportunity(['company_id' => Company::factory()->create()->id]),
        'lost'            => tap(CrmTestHelper::opportunity(), fn (Lead $lead) => $lead->markAsLost(null)),
    };

    Livewire::test(ListActivities::class)
        ->callAction('scheduleActivity', data: [
            'lead_id'          => $target->id,
            'activity_type_id' => crmActivityType()->id,
            'date_deadline'    => now()->addDay()->toDateString(),
            'assigned_to'      => $user->id,
        ]);

    expect(leadHasAnyActivity($target))->toBeFalse();
})->with(['another company', 'lost']);

it('does not schedule on an opportunity the user cannot edit', function () {
    $user = CrmTestHelper::actingAs(['view_any_crm_activity', 'view_crm_activity', 'view_any_crm_opportunity', 'view_crm_opportunity']);

    $opportunity = CrmTestHelper::opportunity();

    Livewire::test(ListActivities::class)
        ->callAction('scheduleActivity', data: [
            'lead_id'          => $opportunity->id,
            'activity_type_id' => crmActivityType()->id,
            'date_deadline'    => now()->addDay()->toDateString(),
            'assigned_to'      => $user->id,
        ])
        ->assertNotified(__('crm::filament/clusters/sales/resources/activity.actions.schedule.notification.forbidden.title'));

    expect(leadHasAnyActivity($opportunity))->toBeFalse();
});

it('hides scheduling without access to the pipeline', function () {
    CrmTestHelper::actingAsCrmUser(['activity']);

    Livewire::test(ListActivities::class)->assertActionHidden('scheduleActivity');
});

it('pre-fills the due date, summary and assignee from the activity type', function () {
    $user = actingAsActivityPlanner();

    $call = crmActivityType('Call');

    expect(ScheduleActivityAction::defaultsForType($call->id))->toBe([
        'activity_type_id' => $call->id,
        'date_deadline'    => now()->addDays((int) $call->delay_count)->toDateString(),
        'summary'          => $call->summary,
        'assigned_to'      => $call->default_user_id ?? $user->id,
    ]);

    expect(ScheduleActivityAction::defaultsForType(null))->toMatchArray([
        'activity_type_id' => null,
        'date_deadline'    => now()->toDateString(),
        'assigned_to'      => $user->id,
    ]);
});

it('pre-fills the form for the column\'s type from a kanban column', function () {
    actingAsActivityPlanner();

    $call = crmActivityType('Call');

    Livewire::test(KanbanActivities::class)
        ->mountAction('kanbanColumn', ['column' => (string) $call->id])
        ->assertActionDataSet([
            'activity_type_id' => $call->id,
            'summary'          => $call->summary,
            'date_deadline'    => now()->addDays((int) $call->delay_count)->toDateString(),
        ]);
});

it('schedules on the record itself from its page without a picker', function () {
    $user = actingAsActivityPlanner();

    $lead = CrmTestHelper::lead();

    Livewire::test(ActivitiesRelationManager::class, [
        'ownerRecord' => $lead,
        'pageClass'   => ViewOpportunity::class,
    ])
        ->assertActionVisible(TestAction::make('scheduleActivity')->table())
        ->callAction(TestAction::make('scheduleActivity')->table(), data: [
            'activity_type_id' => crmActivityType('Meeting')->id,
            'date_deadline'    => now()->addDays(2)->toDateString(),
            'summary'          => 'Meet on the lead page',
            'assigned_to'      => $user->id,
        ])
        ->assertHasNoActionErrors();

    expect($lead->crmActivities()->where('summary', 'Meet on the lead page')->exists())->toBeTrue();
});

it('hides scheduling on a lost record\'s page', function () {
    actingAsActivityPlanner();

    $opportunity = CrmTestHelper::opportunity();
    $opportunity->markAsLost(null);

    Livewire::test(ActivitiesRelationManager::class, [
        'ownerRecord' => Lead::withTrashed()->findOrFail($opportunity->id),
        'pageClass'   => ViewOpportunity::class,
    ])
        ->assertActionHidden(TestAction::make('scheduleActivity')->table());
});

it('reschedules an activity', function () {
    $user = actingAsActivityPlanner();

    $activity = scheduledCrmActivity(CrmTestHelper::opportunity());

    Livewire::test(ListActivities::class)
        ->callTableAction('rescheduleActivity', $activity, data: [
            'date_deadline' => '2031-03-15',
            'summary'       => 'Moved to March',
            'assigned_to'   => $user->id,
        ])
        ->assertHasNoTableActionErrors();

    $activity->refresh();

    expect($activity->date_deadline->toDateString())->toBe('2031-03-15')
        ->and($activity->summary)->toBe('Moved to March');
});

it('requires a due date to reschedule', function () {
    actingAsActivityPlanner();

    $activity = scheduledCrmActivity(CrmTestHelper::opportunity());

    Livewire::test(ListActivities::class)
        ->callTableAction('rescheduleActivity', $activity, data: ['date_deadline' => null])
        ->assertHasTableActionErrors(['date_deadline' => 'required']);
});

it('marks an activity as done and logs it', function () {
    $user = actingAsActivityPlanner();

    $lead = CrmTestHelper::opportunity();

    $activity = scheduledCrmActivity($lead, ['summary' => 'Proposal call']);

    Livewire::test(ListActivities::class)
        ->callTableAction('markActivityAsDone', $activity, data: ['feedback' => 'Went well'])
        ->assertHasNoTableActionErrors()
        ->assertNotified(__('crm::filament/clusters/sales/resources/activity.actions.done.notification.title'));

    expect(Activity::query()->whereKey($activity->id)->exists())->toBeFalse();

    $doneNote = $lead->messages()->where('type', 'comment')->latest('id')->value('body');

    expect($doneNote)->toStartWith(crmActivityType()->name.' done')
        ->toContain('Proposal call')
        ->toContain('Went well');

    $log = ActivityLog::query()->where('lead_id', $lead->id)->firstOrFail();

    expect($log->activity_type_id)->toBe(crmActivityType()->id)
        ->and($log->done_by)->toBe($user->id)
        ->and($log->summary)->toBe('Proposal call')
        ->and($log->lead_type)->toBe(Lead::TYPE_OPPORTUNITY)
        ->and($log->done_at)->not->toBeNull();
});

it('cancels an activity without logging it as done', function () {
    actingAsActivityPlanner();

    $lead = CrmTestHelper::opportunity();

    $activity = scheduledCrmActivity($lead);

    Livewire::test(ListActivities::class)
        ->callTableAction('delete', $activity);

    expect(Activity::query()->whereKey($activity->id)->exists())->toBeFalse()
        ->and(ActivityLog::query()->where('lead_id', $lead->id)->exists())->toBeFalse();
});

it('hides done, reschedule and cancel without the activity permissions', function () {
    CrmTestHelper::actingAs(['view_any_crm_activity', 'view_crm_activity', 'view_any_crm_opportunity']);

    $activity = scheduledCrmActivity(CrmTestHelper::opportunity());

    Livewire::test(ListActivities::class)
        ->assertTableActionHidden('markActivityAsDone', $activity)
        ->assertTableActionHidden('rescheduleActivity', $activity)
        ->assertTableActionHidden('delete', $activity);
});

it('uses the crm activity types as kanban columns', function () {
    actingAsActivityPlanner();

    $otherPluginType = ActivityType::query()->create([
        'name'       => 'Unused sales type',
        'plugin'     => 'sales',
        'delay_unit' => 'days',
        'delay_from' => 'current_date',
        'is_active'  => true,
    ]);

    $component = Livewire::test(KanbanActivities::class)->assertOk();

    $columnValues = collect($component->instance()->getKanbanColumns())->pluck('value')->all();

    $crmTypeIds = ActivityType::query()->where('plugin', 'crm')->pluck('id')->map(fn ($id): string => (string) $id)->all();

    expect($columnValues)->toContain(...$crmTypeIds)
        ->not->toContain((string) $otherPluginType->id);
});

it('opens the board on the current user\'s activities', function () {
    $user = actingAsActivityPlanner();

    $opportunity = CrmTestHelper::opportunity();

    $mine = scheduledCrmActivity($opportunity);
    $someoneElses = scheduledCrmActivity($opportunity, ['assigned_to' => myActivitiesOtherUserId()]);

    $component = Livewire::test(KanbanActivities::class)
        ->assertSet('kanbanFilters', [...KanbanActivities::emptyKanbanFilters(), 'assigned_to' => $user->id]);

    $ids = collect($component->instance()->getKanbanColumns())
        ->flatMap(fn (KanbanColumn $column): array => $column->records->modelKeys())
        ->all();

    expect($ids)->toContain($mine->id)
        ->not->toContain($someoneElses->id);
});

it('changes the activity type when a card is moved to another column', function () {
    actingAsActivityPlanner();

    $activity = scheduledCrmActivity(CrmTestHelper::opportunity());

    $meeting = crmActivityType('Meeting');

    Livewire::test(KanbanActivities::class)
        ->call('moveRecord', (string) $activity->id, (string) $meeting->id, []);

    expect($activity->refresh()->activity_type_id)->toBe($meeting->id);
});

it('renders the graph page and switches its scope', function () {
    actingAsActivityPlanner();

    Livewire::test(GraphActivities::class)
        ->assertOk()
        ->assertSet('scope', 'mine')
        ->assertActionHasUrl('toggleScope', ActivityResource::getUrl('graph', ['scope' => 'all']));

    $component = Livewire::withQueryParams(['scope' => 'all'])
        ->test(GraphActivities::class)
        ->assertSet('scope', 'all')
        ->assertActionHasUrl('toggleScope', ActivityResource::getUrl('graph', ['scope' => 'mine']));

    expect($component->instance()->getWidgetData())->toBe(['scope' => 'all']);
});

it('renders the graph widgets', function (string $widget, string $scope) {
    actingAsActivityPlanner();

    scheduledCrmActivity(CrmTestHelper::opportunity());

    Livewire::test($widget, ['scope' => $scope])->assertOk();
})->with([
    ActivityStatsOverview::class,
    ActivitiesByStatusChart::class,
    ActivitiesByTypeChart::class,
    ActivitiesByUserChart::class,
])->with(['mine', 'all']);

it('counts the user\'s activities in the mine scope and everyone\'s in the all scope', function () {
    actingAsActivityPlanner();

    $opportunity = CrmTestHelper::opportunity();

    $mine = scheduledCrmActivity($opportunity);
    $someoneElses = scheduledCrmActivity($opportunity, ['assigned_to' => myActivitiesOtherUserId()]);

    $mineIds = ActivityResource::getScopedQuery('mine')->pluck('id')->all();
    $allIds = ActivityResource::getScopedQuery('all')->pluck('id')->all();

    expect($mineIds)->toContain($mine->id)->not->toContain($someoneElses->id)
        ->and($allIds)->toContain($mine->id, $someoneElses->id);
});

it('filters the board by due status and activity type like the list', function () {
    actingAsActivityPlanner();

    $opportunity = CrmTestHelper::opportunity();

    $overdue = scheduledCrmActivity($opportunity, ['date_deadline' => now()->subDays(2)->toDateString()]);
    $planned = scheduledCrmActivity($opportunity, ['date_deadline' => now()->addDays(5)->toDateString(), 'activity_type_id' => crmActivityType('Call')->id]);

    $ids = fn (array $filters): array => collect(Livewire::test(KanbanActivities::class)
        ->set('kanbanFilters', [...KanbanActivities::emptyKanbanFilters(), 'assigned_to' => auth()->id(), ...$filters])
        ->instance()
        ->getKanbanColumns())
        ->flatMap(fn (KanbanColumn $column): array => $column->records->modelKeys())
        ->all();

    expect($ids(['due_status' => 'overdue']))->toContain($overdue->id)->not->toContain($planned->id)
        ->and($ids(['activity_type_id' => [crmActivityType('Call')->id]]))->toContain($planned->id)->not->toContain($overdue->id);
});
