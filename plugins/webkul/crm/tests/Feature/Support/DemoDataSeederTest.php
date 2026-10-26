<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Models\Product;
use Webkul\Crm\Database\Seeders\DemoDataSeeder;
use Webkul\Crm\Enums\ActivityDueStatus;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\InboundEmail;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LeadConversion;
use Webkul\Crm\Models\MailAlias;
use Webkul\Crm\Models\Stage;
use Webkul\Crm\Models\Tag;
use Webkul\Crm\Models\TeamMember;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\Team;
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

it('creates the demo leads and opportunities, plus two from incoming emails', function () {
    $seeded = seedCrmDemoData();

    expect($seeded)->toHaveCount(30)
        ->and($seeded->firstWhere('name', 'Bulk order for co-working space')->type)->toBe(Lead::TYPE_OPPORTUNITY)
        ->and($seeded->firstWhere('name', 'Partnership opportunity')->type)->toBe(Lead::TYPE_OPPORTUNITY)
        ->and($seeded->pluck('name'))->toContain('Need 40 workstations for our Pune office', 'Consulting for warehouse automation');
});

it('assigns the demo records to the demo salespeople', function () {
    $seeded = seedCrmDemoData();

    $salespeople = User::query()->where('email', 'like', '%@demo-crm.test')->pluck('id', 'name');

    expect($salespeople->keys()->sort()->values()->all())->toBe(['Emma Wilson', 'John Carter', 'Priya Nair'])
        ->and($seeded->pluck('user_id')->unique()->intersect($salespeople->values()))->toHaveCount(3);
});

it('sets up the sales teams with members, assignment rules and email aliases', function () {
    seedCrmDemoData();

    foreach (['Direct Sales' => 'sales', 'Website' => 'website', 'Pre-Sales' => 'presales'] as $name => $alias) {
        $team = Team::query()->withoutGlobalScopes()->where('name', $name)->firstOrFail();

        expect(TeamMember::query()->where('team_id', $team->id)->count())->toBe(2)
            ->and($team->crmTeam->assignment_domain['conditions'][0]['field'])->toBe('country_id')
            ->and(MailAlias::query()->where('team_id', $team->id)->value('alias_name'))->toBe($alias);
    }
});

it('logs lead conversions, quotations, recurring revenue, partner levels and received emails', function () {
    $seeded = seedCrmDemoData();

    expect(LeadConversion::query()->whereIn('lead_name', ['Bulk order for co-working space', 'Partnership opportunity'])->count())->toBe(2)
        ->and(Order::query()->whereIn('opportunity_id', $seeded->pluck('id'))->count())->toBe(Product::query()->exists() ? 3 : 0)
        ->and($seeded->where('recurring_revenue', '>', 0)->pluck('name')->sort()->values()->all())->toBe(['Annual Maintenance Contract', 'CRM Licences (50 seats)', 'Cloud Migration Consulting'])
        ->and(DB::table('crm_partners')->whereNotNull('level_id')->count())->toBeGreaterThanOrEqual(3)
        ->and(InboundEmail::query()->where('status', InboundEmail::STATUS_REPLY_ADDED)->exists())->toBeTrue();
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
