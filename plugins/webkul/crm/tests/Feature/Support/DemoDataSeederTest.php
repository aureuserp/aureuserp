<?php

use Illuminate\Support\Collection;
use Webkul\Crm\Database\Seeders\DemoDataSeeder;
use Webkul\Crm\Enums\ActivityDueStatus;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Crm\Models\Tag;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

/**
 * Every lead and opportunity, lost ones included, ignoring company restrictions.
 */
function allCrmLeadIds(): Collection
{
    return Lead::query()->withoutGlobalScopes()->pluck('id');
}

/**
 * Runs the demo seeder and returns the leads and opportunities it created.
 *
 * @return Collection<int, Lead>
 */
function seedCrmDemoData(): Collection
{
    $existingIds = allCrmLeadIds();

    test()->seed(DemoDataSeeder::class);

    return Lead::query()
        ->withoutGlobalScopes()
        ->whereNotIn('id', $existingIds)
        ->get();
}

function crmDemoOwner(): User
{
    return User::query()->where('email', 'admin@example.com')->first()
        ?? User::query()->orderBy('id')->firstOrFail();
}

it('creates 12 leads and 16 opportunities', function () {
    $seeded = seedCrmDemoData();

    expect($seeded)->toHaveCount(28)
        ->and($seeded->where('type', Lead::TYPE_LEAD))->toHaveCount(12)
        ->and($seeded->where('type', Lead::TYPE_OPPORTUNITY))->toHaveCount(16);
});

it('assigns every demo record to the admin user', function () {
    $seeded = seedCrmDemoData();

    expect($seeded->pluck('user_id')->unique()->values()->all())->toBe([crmDemoOwner()->id]);
});

it('creates three won opportunities', function () {
    $seeded = seedCrmDemoData();

    $wonStageIds = Stage::query()->where('is_won', true)->pluck('id');

    $won = $seeded
        ->where('type', Lead::TYPE_OPPORTUNITY)
        ->filter(fn (Lead $lead): bool => $wonStageIds->contains($lead->stage_id) && ! $lead->trashed());

    expect($won)->toHaveCount(3);

    foreach ($won as $opportunity) {
        expect($opportunity->closed_at)->not->toBeNull();
    }
});

it('archives the lost leads and opportunities', function () {
    $seeded = seedCrmDemoData();

    $lost = $seeded->filter(fn (Lead $lead): bool => $lead->trashed());

    expect($lost->where('type', Lead::TYPE_LEAD))->toHaveCount(2)
        ->and($lost->where('type', Lead::TYPE_OPPORTUNITY))->toHaveCount(3);
});

it('tags the demo records and links customers', function () {
    $tagCount = Tag::query()->count();

    $seeded = seedCrmDemoData();

    $taggedLeadIds = Lead::query()
        ->withoutGlobalScopes()
        ->whereKey($seeded->pluck('id'))
        ->whereHas('tags')
        ->pluck('id');

    expect(Tag::query()->count())->toBeGreaterThan($tagCount)
        ->and($taggedLeadIds)->not->toBeEmpty()
        ->and($seeded->whereNotNull('partner_id'))->not->toBeEmpty();
});

it('schedules overdue, today and upcoming activities', function () {
    $seeded = seedCrmDemoData();

    $activities = Activity::query()
        ->withoutGlobalScopes()
        ->where('type', 'activity')
        ->where('messageable_type', (new Lead)->getMorphClass())
        ->whereIn('messageable_id', $seeded->pluck('id'))
        ->get();

    expect($activities)->toHaveCount(10)
        ->and($activities->map(fn (Activity $activity): ActivityDueStatus => $activity->getDueStatus())->unique()->count())->toBe(3);
});

it('creates 24 done activity logs', function () {
    $seeded = seedCrmDemoData();

    expect(ActivityLog::query()->withoutGlobalScopes()->whereIn('lead_id', $seeded->pluck('id'))->count())->toBe(24);
});

it('does nothing when the demo data already exists', function () {
    seedCrmDemoData();

    $leadCount = allCrmLeadIds()->count();
    $logCount = ActivityLog::query()->withoutGlobalScopes()->count();
    $activityCount = Activity::query()->withoutGlobalScopes()->where('type', 'activity')->count();
    $tagCount = Tag::query()->count();

    $this->seed(DemoDataSeeder::class);

    expect(allCrmLeadIds()->count())->toBe($leadCount)
        ->and(ActivityLog::query()->withoutGlobalScopes()->count())->toBe($logCount)
        ->and(Activity::query()->withoutGlobalScopes()->where('type', 'activity')->count())->toBe($activityCount)
        ->and(Tag::query()->count())->toBe($tagCount);
});
