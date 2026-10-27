<?php

use Livewire\Livewire;
use Webkul\Chatter\Models\Message;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ManageActivities as ManageLeadActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions\ScheduleActivityAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages\GraphActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages\KanbanActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages\ListActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByStatusChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByTypeChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByUserChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivityStatsOverview;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ManageActivities as ManageOpportunityActivities;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\Lead;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityType;
use Webkul\Support\Models\Company;
use Wezlo\FilamentKanban\KanbanColumn;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function actingAsActivityPlanner(): User
{
    return CRMHelper::actingAsCRMUser(['activity', 'opportunity', 'lead']);
}

function crmActivityType(string $name = 'Email'): ActivityType
{
    return ActivityType::query()->where('plugin', 'crm')->where('name', $name)->firstOrFail();
}

function myActivitiesOtherUserId(): int
{
    return (int) User::query()->whereKeyNot(auth()->id())->value('id');
}

function scheduledCRMActivity(Lead $lead, array $attributes = []): Activity
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

    expect($pages['index']->getPage())->toBe(ListActivities::class)
        ->and($pages['kanban']->getPage())->toBe(KanbanActivities::class)
        ->and($pages['graph']->getPage())->toBe(GraphActivities::class)
        ->and(array_keys($pages))->toBe(['index', 'kanban', 'graph']);

    expect(ActivityResource::getUrl('index'))->toEndWith('/crm/sales/activities')
        ->and(ActivityResource::getUrl('kanban'))->toEndWith('/crm/sales/activities/kanban')
        ->and(ActivityResource::getUrl('graph'))->toEndWith('/crm/sales/activities/graph');
});

it('shows kanban, list and graph tabs on every activity view', function (string $page, array $active) {
    actingAsActivityPlanner();

    $component = Livewire::test($page)->assertOk();

    $tabs = collect($component->instance()->getRecordNavigationTabs());

    expect($tabs->map(fn ($tab): string => $tab->getLabel())->all())->toBe([
        __('crm::filament/clusters/sales/resources/activity.views.list'),
        __('crm::filament/clusters/sales/resources/activity.views.kanban'),
        __('crm::filament/clusters/sales/resources/activity.views.graph'),
    ])
        ->and($tabs->map(fn ($tab): bool => $tab->isActive())->all())->toBe($active);

    $component->assertActionVisible('scheduleActivity')
        ->assertActionDoesNotExist('listView');
})->with([
    'list'   => [ListActivities::class, [true, false, false]],
    'kanban' => [KanbanActivities::class, [false, true, false]],
    'graph'  => [GraphActivities::class, [false, false, true]],
]);

it('snoozes an activity by seven days from its due date or from today when overdue', function () {
    actingAsActivityPlanner();

    $upcoming = scheduledCRMActivity(CRMHelper::opportunity(), ['date_deadline' => today()->addDays(3)]);
    $overdue = scheduledCRMActivity(CRMHelper::opportunity(), ['date_deadline' => today()->subDays(5)]);

    Livewire::test(ListActivities::class)
        ->callTableAction('snoozeActivity', $upcoming)
        ->callTableAction('snoozeActivity', $overdue);

    expect($upcoming->refresh()->date_deadline->toDateString())->toBe(today()->addDays(10)->toDateString())
        ->and($overdue->refresh()->date_deadline->toDateString())->toBe(today()->addDays(7)->toDateString());
});

it('offers email, SMS and snooze on each activity row', function () {
    actingAsActivityPlanner();

    $activity = scheduledCRMActivity(CRMHelper::opportunity(['email' => 'row@acme.test']));

    Livewire::test(ListActivities::class)
        ->assertTableActionVisible('sendEmail', $activity)
        ->assertTableActionVisible('sendSMS', $activity)
        ->assertTableActionVisible('snoozeActivity', $activity);
});

it('forbids every view without the view any activity permission', function (string $page) {
    CRMHelper::actingAsCRMUser(['opportunity']);

    Livewire::test($page)->assertForbidden();
})->with([
    'kanban' => [KanbanActivities::class],
    'list'   => [ListActivities::class],
    'graph'  => [GraphActivities::class],
]);

it('lists activities on leads and opportunities but not on other records', function () {
    actingAsActivityPlanner();

    $onOpportunity = scheduledCRMActivity(CRMHelper::opportunity());
    $onLead = scheduledCRMActivity(CRMHelper::lead());

    $team = Team::factory()->create(['company_id' => CRMHelper::companyId(), 'is_active' => true]);

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

    $lostOpportunity = CRMHelper::opportunity();
    $lostLead = CRMHelper::lead();

    $onLostOpportunity = scheduledCRMActivity($lostOpportunity);
    $onLostLead = scheduledCRMActivity($lostLead);

    $lostOpportunity->markAsLost(null);
    $lostLead->markAsLost(null);

    Livewire::test(ListActivities::class)
        ->call('loadView', 'all')
        ->assertCanNotSeeTableRecords([$onLostOpportunity, $onLostLead]);
});

it('shows the current user\'s activities by default and everyone\'s in the all view', function () {
    actingAsActivityPlanner();

    $opportunity = CRMHelper::opportunity();

    $mine = scheduledCRMActivity($opportunity);
    $someoneElses = scheduledCRMActivity($opportunity, ['assigned_to' => myActivitiesOtherUserId()]);

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

    $opportunity = CRMHelper::opportunity();

    $activities = [
        'overdue' => scheduledCRMActivity($opportunity, ['date_deadline' => now()->subDays(2)->toDateString()]),
        'today'   => scheduledCRMActivity($opportunity, ['date_deadline' => now()->toDateString()]),
        'planned' => scheduledCRMActivity($opportunity, ['date_deadline' => now()->addDays(5)->toDateString()]),
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

    $opportunity = CRMHelper::opportunity(['name' => 'Opportunity to follow up']);

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
        'another company' => CRMHelper::opportunity(['company_id' => Company::factory()->create()->id]),
        'lost'            => tap(CRMHelper::opportunity(), fn (Lead $lead) => $lead->markAsLost(null)),
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
    $user = CRMHelper::actingAs(['view_any_crm_activity', 'view_crm_activity', 'view_any_crm_opportunity', 'view_crm_opportunity']);

    $opportunity = CRMHelper::opportunity();

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
    CRMHelper::actingAsCRMUser(['activity']);

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

    $lead = CRMHelper::lead();

    Livewire::test(ManageLeadActivities::class, ['record' => $lead->id])
        ->assertActionVisible('scheduleActivity')
        ->callAction('scheduleActivity', data: [
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

    $opportunity = CRMHelper::opportunity();
    $opportunity->markAsLost(null);

    Livewire::test(ManageOpportunityActivities::class, ['record' => $opportunity->id])
        ->assertActionHidden('scheduleActivity');
});

it('reschedules an activity', function () {
    $user = actingAsActivityPlanner();

    $activity = scheduledCRMActivity(CRMHelper::opportunity());

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

    $activity = scheduledCRMActivity(CRMHelper::opportunity());

    Livewire::test(ListActivities::class)
        ->callTableAction('rescheduleActivity', $activity, data: ['date_deadline' => null])
        ->assertHasTableActionErrors(['date_deadline' => 'required']);
});

it('marks an activity as done and logs it', function () {
    $user = actingAsActivityPlanner();

    $lead = CRMHelper::opportunity();

    $activity = scheduledCRMActivity($lead, ['summary' => 'Proposal call']);

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
        ->and($log->lead_type)->toBe(LeadType::OPPORTUNITY)
        ->and($log->done_at)->not->toBeNull();
});

it('cancels an activity without logging it as done', function () {
    actingAsActivityPlanner();

    $lead = CRMHelper::opportunity();

    $activity = scheduledCRMActivity($lead);

    Livewire::test(ListActivities::class)
        ->callTableAction('delete', $activity);

    expect(Activity::query()->whereKey($activity->id)->exists())->toBeFalse()
        ->and(ActivityLog::query()->where('lead_id', $lead->id)->exists())->toBeFalse();
});

it('hides done, reschedule and cancel without the activity permissions', function () {
    CRMHelper::actingAs(['view_any_crm_activity', 'view_crm_activity', 'view_any_crm_opportunity']);

    $activity = scheduledCRMActivity(CRMHelper::opportunity());

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

it('opens the board without a default filter, showing everyone\'s activities', function () {
    actingAsActivityPlanner();

    $opportunity = CRMHelper::opportunity();

    $mine = scheduledCRMActivity($opportunity);
    $someoneElses = scheduledCRMActivity($opportunity, ['assigned_to' => myActivitiesOtherUserId()]);

    $component = Livewire::test(KanbanActivities::class)
        ->assertSet('activeTableView', 'default');

    $ids = collect($component->instance()->getKanbanColumns())
        ->flatMap(fn (KanbanColumn $column): array => $column->records->modelKeys())
        ->all();

    expect($ids)->toContain($mine->id)
        ->toContain($someoneElses->id);
});

it('changes the activity type when a card is moved to another column', function () {
    actingAsActivityPlanner();

    $activity = scheduledCRMActivity(CRMHelper::opportunity());

    $meeting = crmActivityType('Meeting');

    Livewire::test(KanbanActivities::class)
        ->call('moveRecord', (string) $activity->id, (string) $meeting->id, []);

    expect($activity->refresh()->activity_type_id)->toBe($meeting->id);
});

it('renders the graph page without a user scope toggle', function () {
    actingAsActivityPlanner();

    Livewire::test(GraphActivities::class)
        ->assertOk()
        ->assertActionDoesNotExist('toggleScope');
});

it('renders the graph widgets', function (string $widget, string $scope) {
    actingAsActivityPlanner();

    scheduledCRMActivity(CRMHelper::opportunity());

    Livewire::test($widget, ['scope' => $scope])->assertOk();
})->with([
    ActivityStatsOverview::class,
    ActivitiesByStatusChart::class,
    ActivitiesByTypeChart::class,
    ActivitiesByUserChart::class,
])->with(['mine', 'all']);

it('counts the user\'s activities in the mine scope and everyone\'s in the all scope', function () {
    actingAsActivityPlanner();

    $opportunity = CRMHelper::opportunity();

    $mine = scheduledCRMActivity($opportunity);
    $someoneElses = scheduledCRMActivity($opportunity, ['assigned_to' => myActivitiesOtherUserId()]);

    $mineIds = ActivityResource::getScopedQuery('mine')->pluck('id')->all();
    $allIds = ActivityResource::getScopedQuery('all')->pluck('id')->all();

    expect($mineIds)->toContain($mine->id)->not->toContain($someoneElses->id)
        ->and($allIds)->toContain($mine->id, $someoneElses->id);
});

it('filters the board by due status and activity type like the list', function () {
    actingAsActivityPlanner();

    $opportunity = CRMHelper::opportunity();

    $overdue = scheduledCRMActivity($opportunity, ['date_deadline' => now()->subDays(2)->toDateString()]);
    $planned = scheduledCRMActivity($opportunity, ['date_deadline' => now()->addDays(5)->toDateString(), 'activity_type_id' => crmActivityType('Call')->id]);

    $ids = fn (array $filters): array => collect(Livewire::test(KanbanActivities::class)
        ->set('tableFilters', $filters)
        ->instance()
        ->getKanbanColumns())
        ->flatMap(fn (KanbanColumn $column): array => $column->records->modelKeys())
        ->all();

    $callRule = ['type' => 'activityType', 'data' => ['operator' => 'isRelatedTo', 'settings' => ['values' => [(string) crmActivityType('Call')->id]]]];

    expect($ids(['due_status' => ['value' => 'overdue']]))->toContain($overdue->id)->not->toContain($planned->id)
        ->and($ids(['queryBuilder' => ['rules' => ['call' => $callRule]]]))->toContain($planned->id)->not->toContain($overdue->id);
});

it('renders activity cards like the pipeline cards and marks an activity done from the card', function () {
    actingAsActivityPlanner();

    $opportunity = CRMHelper::opportunity(['name' => 'Activity card opportunity']);
    $activity = scheduledCRMActivity($opportunity, ['summary' => 'Card summary call', 'activity_type_id' => crmActivityType('Call')->id]);

    $component = Livewire::test(KanbanActivities::class)
        ->assertSee('Card summary call')
        ->assertSee('Activity card opportunity')
        ->assertSeeHtml('window.location.href')
        ->assertSeeHtml("actionName: 'done'");

    $component->callAction('kanbanCardFooter', arguments: ['record' => (string) $activity->id, 'actionName' => 'done']);

    expect(Activity::query()->find($activity->id))->toBeNull();
});

it('shows the due-status progress of each activity type and filters a column from it', function () {
    actingAsActivityPlanner();

    $call = crmActivityType('Call');
    $opportunity = CRMHelper::opportunity();

    $overdue = scheduledCRMActivity($opportunity, ['activity_type_id' => $call->id, 'date_deadline' => now()->subDays(2)->toDateString()]);
    $planned = scheduledCRMActivity($opportunity, ['activity_type_id' => $call->id, 'date_deadline' => now()->addDays(5)->toDateString()]);

    $component = Livewire::test(KanbanActivities::class);
    $column = collect($component->instance()->getKanbanColumns())->firstWhere('value', (string) $call->id);
    $progress = $component->instance()->kanbanColumnProgress($column);

    expect($progress['overdue'] ?? 0)->toBeGreaterThanOrEqual(1)
        ->and($progress['planned'] ?? 0)->toBeGreaterThanOrEqual(1);

    $component->call('toggleKanbanActivityFilter', (string) $call->id, 'overdue');

    expect(collect($component->instance()->getKanbanColumns())->firstWhere('value', (string) $call->id)->records->modelKeys())
        ->toContain($overdue->id)
        ->not->toContain($planned->id);
});
