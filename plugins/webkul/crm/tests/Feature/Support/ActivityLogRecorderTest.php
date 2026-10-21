<?php

use Webkul\Chatter\Models\Message;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\ActivityType;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Services\ActivityLogRecorder;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../Helpers/CrmTestHelper.php';

beforeEach(function () {
    CrmTestHelper::bootstrap();

    app()->setLocale('en');
});

function crmCallActivityTypeId(): int
{
    return (int) ActivityType::query()->where('plugin', 'crm')->where('name', 'Call')->value('id');
}

/**
 * Schedules a "Call" activity on the lead and returns the base Chatter message.
 *
 * @param  array<string, mixed>  $attributes
 */
function scheduleLoggedCallActivity(Lead $lead, array $attributes = []): Message
{
    return $lead->addActivity([
        'activity_type_id' => crmCallActivityTypeId(),
        'summary'          => 'Discuss pricing',
        'date_deadline'    => today()->addDays(2),
        ...$attributes,
    ]);
}

it('logs an activity marked as done from my activities with a snapshot of the lead', function () {
    $user = CrmTestHelper::actingAs();

    $assignee = User::query()->whereKeyNot($user->id)->firstOrFail();
    $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId()]);

    $opportunity = CrmTestHelper::opportunity([
        'team_id'  => $team->id,
        'stage_id' => CrmTestHelper::firstStage()->id,
    ]);

    $message = scheduleLoggedCallActivity($opportunity, ['assigned_to' => $assignee->id]);

    Activity::query()->findOrFail($message->id)->markAsDone('All good');

    $logs = ActivityLog::query()->where('lead_id', $opportunity->id)->get();

    expect($logs)->toHaveCount(1);

    $log = $logs->first();

    expect($log->lead_type)->toBe(Lead::TYPE_OPPORTUNITY)
        ->and($log->activity_type_id)->toBe(crmCallActivityTypeId())
        ->and($log->user_id)->toBe($opportunity->user_id)
        ->and($log->team_id)->toBe($team->id)
        ->and($log->stage_id)->toBe(CrmTestHelper::firstStage()->id)
        ->and($log->company_id)->toBe($opportunity->company_id)
        ->and($log->assigned_to)->toBe($assignee->id)
        ->and($log->done_by)->toBe($user->id)
        ->and($log->summary)->toBe('Discuss pricing')
        ->and($log->note)->toStartWith('Call done Discuss pricing')
        ->and($log->note)->toContain('All good')
        ->and($log->date_deadline->toDateString())->toBe(today()->addDays(2)->toDateString())
        ->and($log->activity_created_at)->not->toBeNull()
        ->and($log->done_at)->not->toBeNull();
});

it('keeps the lead type of a lead in the log', function () {
    CrmTestHelper::actingAs();

    $lead = CrmTestHelper::lead();

    Activity::query()->findOrFail(scheduleLoggedCallActivity($lead)->id)->markAsDone();

    expect(ActivityLog::query()->where('lead_id', $lead->id)->value('lead_type'))->toBe(Lead::TYPE_LEAD);
});

it('logs an activity marked as done from the chatter panel', function () {
    $user = CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    $message = scheduleLoggedCallActivity($opportunity);

    $opportunity->addMessage(['type' => 'comment', 'body' => 'Call done Discuss pricing']);

    $opportunity->activities()->findOrFail($message->id)->delete();

    $logs = ActivityLog::query()->where('lead_id', $opportunity->id)->get();

    expect($logs)->toHaveCount(1)
        ->and($logs->first()->note)->toBe('Call done Discuss pricing')
        ->and($logs->first()->done_by)->toBe($user->id)
        ->and($logs->first()->summary)->toBe('Discuss pricing');
});

it('does not log a cancelled activity', function (bool $asCrmActivity) {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    $message = scheduleLoggedCallActivity($opportunity);

    $asCrmActivity
        ? Activity::query()->findOrFail($message->id)->delete()
        : $opportunity->activities()->findOrFail($message->id)->delete();

    expect(ActivityLog::query()->where('lead_id', $opportunity->id)->exists())->toBeFalse();
})->with([
    'from my activities'     => true,
    'from the chatter panel' => false,
]);

it('does not log an activity cancelled after an unrelated comment', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    $message = scheduleLoggedCallActivity($opportunity);

    $opportunity->addMessage(['type' => 'comment', 'body' => 'Customer asked for a discount']);

    $opportunity->activities()->findOrFail($message->id)->delete();

    expect(ActivityLog::query()->where('lead_id', $opportunity->id)->exists())->toBeFalse();
});

it('does not log a done message posted on another lead', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();
    $otherOpportunity = CrmTestHelper::opportunity();

    $message = scheduleLoggedCallActivity($opportunity);

    $otherOpportunity->addMessage(['type' => 'comment', 'body' => 'Call done Discuss pricing']);

    $opportunity->activities()->findOrFail($message->id)->delete();

    expect(ActivityLog::query()->whereIn('lead_id', [$opportunity->id, $otherOpportunity->id])->exists())->toBeFalse();
});

it('ignores activities done on records that are not leads', function () {
    CrmTestHelper::actingAs();

    $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId()]);

    $logCount = ActivityLog::query()->count();

    $activity = $team->addActivity([
        'activity_type_id' => crmCallActivityTypeId(),
        'summary'          => 'Team review',
        'date_deadline'    => today(),
    ]);

    $team->addMessage(['type' => 'comment', 'body' => 'Call done Team review']);

    $team->activities()->findOrFail($activity->id)->delete();

    expect(ActivityLog::query()->count())->toBe($logCount);
});

it('logs each done activity exactly once', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    $first = scheduleLoggedCallActivity($opportunity, ['summary' => 'First call']);
    $second = scheduleLoggedCallActivity($opportunity, ['summary' => 'Second call']);

    Activity::query()->findOrFail($first->id)->markAsDone();

    expect(ActivityLog::query()->where('lead_id', $opportunity->id)->count())->toBe(1);

    $opportunity->activities()->findOrFail($second->id)->delete();

    expect(ActivityLog::query()->where('lead_id', $opportunity->id)->pluck('summary')->all())->toBe(['First call']);
});

it('uses one done message for one activity only', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    $first = scheduleLoggedCallActivity($opportunity);
    $second = scheduleLoggedCallActivity($opportunity);

    $opportunity->addMessage(['type' => 'comment', 'body' => 'Call done Discuss pricing']);

    $opportunity->activities()->findOrFail($first->id)->delete();
    $opportunity->activities()->findOrFail($second->id)->delete();

    expect(ActivityLog::query()->where('lead_id', $opportunity->id)->count())->toBe(1);
});

it('logs every activity when several are marked as done', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    foreach (['First call', 'Second call', 'Third call'] as $summary) {
        Activity::query()->findOrFail(scheduleLoggedCallActivity($opportunity, ['summary' => $summary])->id)->markAsDone();
    }

    expect(ActivityLog::query()->where('lead_id', $opportunity->id)->pluck('summary')->sort()->values()->all())
        ->toBe(['First call', 'Second call', 'Third call']);
});

it('loads the related records of a log entry', function () {
    $user = CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    Activity::query()->findOrFail(scheduleLoggedCallActivity($opportunity)->id)->markAsDone();

    $log = ActivityLog::query()->where('lead_id', $opportunity->id)->firstOrFail();

    expect($log->lead->id)->toBe($opportunity->id)
        ->and($log->activityType->name)->toBe('Call')
        ->and($log->doneBy->id)->toBe($user->id)
        ->and($log->user->id)->toBe($opportunity->user_id)
        ->and($log->stage->id)->toBe($opportunity->stage_id);
});

it('does not log a cancelled activity without type or summary after an unrelated comment', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    $message = $opportunity->addMessage([
        'type'             => 'activity',
        'activity_type_id' => null,
        'summary'          => null,
        'assigned_to'      => auth()->id(),
        'date_deadline'    => today(),
    ]);

    $opportunity->addMessage(['type' => 'comment', 'body' => 'Any comment at all']);

    $opportunity->activities()->findOrFail($message->id)->delete();

    expect(ActivityLog::query()->where('lead_id', $opportunity->id)->exists())->toBeFalse();
});

it('registers its listeners only once', function () {
    CrmTestHelper::actingAs();

    $recorder = app(ActivityLogRecorder::class);

    $recorder->register();
    $recorder->register();

    $opportunity = CrmTestHelper::opportunity();

    Activity::query()->findOrFail(scheduleLoggedCallActivity($opportunity)->id)->markAsDone();

    expect(ActivityLog::query()->where('lead_id', $opportunity->id)->count())->toBe(1);
});
