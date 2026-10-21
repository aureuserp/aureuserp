<?php

use Illuminate\Support\Carbon;
use Webkul\Chatter\Models\Message;
use Webkul\Crm\Enums\ActivityDueStatus;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\ActivityType;
use Webkul\Crm\Models\Lead;
use Webkul\Sale\Models\Team;

require_once __DIR__.'/../../Helpers/CrmTestHelper.php';

beforeEach(function () {
    CrmTestHelper::bootstrap();

    app()->setLocale('en');
});

/**
 * Schedules an activity on the given lead and returns it as a CRM activity.
 *
 * @param  array<string, mixed>  $attributes
 */
function scheduleCrmActivity(Lead $lead, array $attributes = []): Activity
{
    $message = $lead->addActivity([
        'activity_type_id' => ActivityType::query()->where('plugin', 'crm')->where('name', 'Call')->value('id'),
        'summary'          => 'Discuss the proposal',
        'date_deadline'    => now()->addDays(3),
        ...$attributes,
    ]);

    return Activity::query()->findOrFail($message->id);
}

/**
 * The most recent comment posted on the lead.
 */
function latestCrmComment(Lead $lead): ?Message
{
    return Message::query()
        ->where('messageable_type', $lead->getMorphClass())
        ->where('messageable_id', $lead->id)
        ->where('type', 'comment')
        ->latest('id')
        ->first();
}

it('only includes activity messages scheduled on leads', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();
    $activity = scheduleCrmActivity($opportunity);
    $comment = $opportunity->addMessage(['type' => 'comment', 'body' => 'A plain comment']);

    $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId()]);
    $teamActivity = $team->addActivity(['summary' => 'Team activity', 'date_deadline' => now()->addDay()]);

    $ids = Activity::query()->whereKey([$activity->id, $comment->id, $teamActivity->id])->pluck('id')->all();

    expect($ids)->toBe([$activity->id]);
});

it('loads the lead an activity belongs to', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    expect(scheduleCrmActivity($opportunity)->lead->id)->toBe($opportunity->id);
});

it('filters activities by due status', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    $overdue = scheduleCrmActivity($opportunity, ['date_deadline' => today()->subDays(2)]);
    $today = scheduleCrmActivity($opportunity, ['date_deadline' => today()]);
    $upcoming = scheduleCrmActivity($opportunity, ['date_deadline' => today()->addDays(2)]);
    $undated = scheduleCrmActivity($opportunity);
    $undated->forceFill(['date_deadline' => null])->saveQuietly();

    $ids = [$overdue->id, $today->id, $upcoming->id, $undated->id];

    $scoped = fn (string $scope, mixed ...$arguments): array => Activity::query()
        ->whereKey($ids)
        ->{$scope}(...$arguments)
        ->pluck('id')
        ->sort()
        ->values()
        ->all();

    expect($scoped('overdue'))->toBe([$overdue->id])
        ->and($scoped('dueToday'))->toBe([$today->id])
        ->and($scoped('planned'))->toBe(collect([$upcoming->id, $undated->id])->sort()->values()->all())
        ->and($scoped('dueStatus', ActivityDueStatus::Overdue))->toBe([$overdue->id])
        ->and($scoped('dueStatus', 'today'))->toBe([$today->id])
        ->and($scoped('dueStatus', 'planned'))->toBe(collect([$upcoming->id, $undated->id])->sort()->values()->all());
});

it('rejects an unknown due status', function () {
    CrmTestHelper::actingAs();

    Activity::query()->dueStatus('someday')->get();
})->throws(ValueError::class);

it('returns the due status of an activity', function (int $daysFromToday, ActivityDueStatus $expected) {
    CrmTestHelper::actingAs();

    $activity = scheduleCrmActivity(CrmTestHelper::opportunity(), [
        'date_deadline' => today()->addDays($daysFromToday),
    ]);

    expect($activity->getDueStatus())->toBe($expected);
})->with([
    'overdue'  => [-3, ActivityDueStatus::Overdue],
    'today'    => [0, ActivityDueStatus::Today],
    'upcoming' => [5, ActivityDueStatus::Planned],
]);

it('treats an activity without a deadline as planned', function () {
    expect(ActivityDueStatus::fromDate(null))->toBe(ActivityDueStatus::Planned)
        ->and(ActivityDueStatus::fromDate(Carbon::yesterday()))->toBe(ActivityDueStatus::Overdue)
        ->and(ActivityDueStatus::fromDate(Carbon::today()))->toBe(ActivityDueStatus::Today)
        ->and(ActivityDueStatus::fromDate(Carbon::tomorrow()))->toBe(ActivityDueStatus::Planned);
});

it('posts a done note on the lead and removes the activity when marked as done', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    $activity = scheduleCrmActivity($opportunity, [
        'summary' => 'Follow up',
        'body'    => '<p>Ask about the budget</p>',
    ]);

    $activity->markAsDone('Went well');

    $comment = latestCrmComment($opportunity);

    expect($comment)->not->toBeNull()
        ->and($comment->body)->toStartWith('Call done Follow up')
        ->and($comment->body)->toContain('Original Note')
        ->and($comment->body)->toContain('Ask about the budget')
        ->and($comment->body)->toContain('Feedback')
        ->and($comment->body)->toContain('Went well')
        ->and(Activity::query()->find($activity->id))->toBeNull()
        ->and(Message::query()->find($activity->id))->toBeNull();
});

it('does not echo an empty body as the original note', function (string $emptyBody) {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    $activity = scheduleCrmActivity($opportunity, ['summary' => 'Follow up', 'body' => $emptyBody]);

    $activity->markAsDone();

    $comment = latestCrmComment($opportunity);

    expect($comment->body)->toBe('Call done Follow up')
        ->and($comment->body)->not->toContain('Original Note');
})->with([
    'empty paragraph'    => '<p></p>',
    'whitespace only'    => '<p> </p>',
    'empty string'       => '',
]);

it('names the done note after the activity type only when there is no summary or feedback', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    $activity = scheduleCrmActivity($opportunity, [
        'activity_type_id' => ActivityType::query()->where('plugin', 'crm')->where('name', 'Meeting')->value('id'),
        'summary'          => null,
    ]);

    $activity->markAsDone();

    expect(latestCrmComment($opportunity)->body)->toBe('Meeting done');
});

it('labels and colours every due status', function (ActivityDueStatus $status, string $label, string $color) {
    app()->setLocale('en');

    expect($status->getLabel())->toBe($label)
        ->and($status->getColor())->toBe($color)
        ->and($status->getIcon())->toStartWith('heroicon-');
})->with([
    'overdue' => [ActivityDueStatus::Overdue, 'Overdue', 'danger'],
    'today'   => [ActivityDueStatus::Today, 'Today', 'warning'],
    'planned' => [ActivityDueStatus::Planned, 'Planned', 'success'],
]);

it('labels and colours every opportunity priority', function (OpportunityPriority $priority, string $label, string $color) {
    app()->setLocale('en');

    expect($priority->getLabel())->toBe($label)
        ->and($priority->getColor())->toBe($color);
})->with([
    'low'       => [OpportunityPriority::Low, 'Low', 'gray'],
    'medium'    => [OpportunityPriority::Medium, 'Medium', 'info'],
    'high'      => [OpportunityPriority::High, 'High', 'warning'],
    'very high' => [OpportunityPriority::VeryHigh, 'Very High', 'danger'],
]);

it('translates every enum label in every supported language', function (string $locale) {
    app()->setLocale($locale);

    foreach ([...ActivityDueStatus::cases(), ...OpportunityPriority::cases()] as $case) {
        expect($case->getLabel())->not->toStartWith('crm::');
    }
})->with(['en', 'ar', 'es', 'fr', 'pt_BR']);
