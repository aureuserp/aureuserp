<?php

use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
use Webkul\Crm\Filament\Support\Reports\ActivityReport;
use Webkul\Crm\Filament\Support\Reports\Dimension;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\ActivityType;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function activityLogReportType(string $name): ActivityType
{
    return ActivityType::query()->create([
        'name'        => $name,
        'plugin'      => 'crm',
        'delay_count' => 0,
        'delay_unit'  => 'days',
        'delay_from'  => 'current_date',
        'is_active'   => true,
    ]);
}

function activityLogReportUser(): User
{
    return User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => CRMHelper::companyId(),
        'is_active'          => true,
    ]));
}

/**
 * Schedules an activity on the lead the way the Chatter panel does.
 */
function activityLogReportSchedule(Lead $lead, ActivityType $type, string $summary = 'Report activity'): Activity
{
    $message = $lead->addMessage([
        'type'             => 'activity',
        'activity_type_id' => $type->id,
        'summary'          => $summary,
        'assigned_to'      => auth()->id(),
        'date_deadline'    => today()->addDays(2),
    ]);

    return Activity::query()->findOrFail($message->id);
}

/**
 * A completed activity written straight to the log.
 *
 * @param  array<string, mixed>  $attributes
 */
function activityLogReportLog(Lead $lead, array $attributes = []): ActivityLog
{
    return ActivityLog::query()->create([
        'lead_id'     => $lead->id,
        'lead_type'   => $lead->type,
        'assigned_to' => auth()->id(),
        'done_by'     => auth()->id(),
        'user_id'     => $lead->user_id,
        'team_id'     => $lead->team_id,
        'stage_id'    => $lead->stage_id,
        'company_id'  => $lead->company_id,
        'done_at'     => now()->subDay(),
        ...$attributes,
    ]);
}

/**
 * The Activities report query over just the given leads' log entries.
 *
 * @param  array<int, Lead>  $leads
 */
function activityLogReportQuery(array $leads): Builder
{
    return ActivityAnalysisResource::getEloquentQuery()
        ->whereIn('crm_activity_logs.lead_id', collect($leads)->pluck('id')->all());
}

/**
 * @param  array{labels: array<int, string>, series: array<int, array{label: string, data: array<int, float>}>}  $result
 * @return array<string, float>
 */
function activityLogReportTotals(array $result): array
{
    return array_combine($result['labels'], $result['series'][0]['data']);
}

describe('logging', function () {
    it('logs an activity marked as done, with the lead details at that moment', function () {
        $user = CRMHelper::actingAs([]);

        $team = Team::factory()->create(['company_id' => CRMHelper::companyId()]);
        $stage = Stage::factory()->create(['name' => 'Activity Log Stage']);
        $lead = CRMHelper::opportunity(['team_id' => $team->id, 'stage_id' => $stage->id]);
        $type = activityLogReportType('Log Test Call');

        $activity = activityLogReportSchedule($lead, $type, 'Discuss the contract');
        $activity->markAsDone('Customer agreed');

        $log = ActivityLog::query()->where('lead_id', $lead->id)->sole();

        expect($log->lead_type)->toBe(LeadType::OPPORTUNITY)
            ->and($log->activity_type_id)->toBe($type->id)
            ->and($log->assigned_to)->toBe($user->id)
            ->and($log->done_by)->toBe($user->id)
            ->and($log->user_id)->toBe($lead->user_id)
            ->and($log->team_id)->toBe($team->id)
            ->and($log->stage_id)->toBe($stage->id)
            ->and($log->company_id)->toBe(CRMHelper::companyId())
            ->and($log->summary)->toBe('Discuss the contract')
            ->and($log->note)->toStartWith('Log Test Call done')
            ->and($log->done_at)->not->toBeNull()
            ->and(Activity::query()->whereKey($activity->id)->exists())->toBeFalse();
    });

    it('logs activities done on leads with the lead type', function () {
        CRMHelper::actingAs([]);

        $lead = CRMHelper::lead();

        activityLogReportSchedule($lead, activityLogReportType('Log Lead Email'))->markAsDone();

        expect(ActivityLog::query()->where('lead_id', $lead->id)->sole()->lead_type)->toBe(LeadType::LEAD);
    });

    it('does not log a cancelled activity', function () {
        CRMHelper::actingAs([]);

        $lead = CRMHelper::opportunity();

        $activity = activityLogReportSchedule($lead, activityLogReportType('Log Cancelled Call'));
        $activity->delete();

        expect(Activity::query()->whereKey($activity->id)->exists())->toBeFalse()
            ->and(ActivityLog::query()->where('lead_id', $lead->id)->exists())->toBeFalse();
    });

    it('keeps the salesperson of the moment even after the lead is reassigned', function () {
        $user = CRMHelper::actingAs([]);

        $lead = CRMHelper::opportunity();

        activityLogReportSchedule($lead, activityLogReportType('Log Reassigned Call'))->markAsDone();

        $lead->update(['user_id' => activityLogReportUser()->id]);

        $result = ActivityReport::aggregate(activityLogReportQuery([$lead]), 'salesperson', null, 'count');

        expect(activityLogReportTotals($result))->toBe([$user->name => 1.0]);
    });
});

describe('counting', function () {
    it('counts only completed activities, never cancelled or still scheduled ones', function () {
        CRMHelper::actingAs([]);

        $lead = CRMHelper::opportunity();
        $type = activityLogReportType('Count Test Call');

        activityLogReportSchedule($lead, $type, 'First done')->markAsDone();
        activityLogReportSchedule($lead, $type, 'Second done')->markAsDone();
        activityLogReportSchedule($lead, $type, 'Cancelled')->delete();
        activityLogReportSchedule($lead, $type, 'Still scheduled');

        $query = ActivityReport::applyFilters(activityLogReportQuery([$lead]), ActivityReport::getDefaultFilters());

        $result = ActivityReport::aggregate($query, 'activity_type', null, 'count');

        expect(activityLogReportTotals($result))->toBe(['Count Test Call' => 2.0]);
    });

    it('builds the default graph: completion month split by activity type', function () {
        CRMHelper::actingAs([]);

        $lead = CRMHelper::opportunity();
        $call = activityLogReportType('Default Graph Call');
        $email = activityLogReportType('Default Graph Email');

        activityLogReportLog($lead, ['activity_type_id' => $call->id, 'done_at' => now()->startOfMonth()->subMonths(2)->addDays(3)]);
        activityLogReportLog($lead, ['activity_type_id' => $call->id, 'done_at' => now()->startOfMonth()->addHours(5)]);
        activityLogReportLog($lead, ['activity_type_id' => $email->id, 'done_at' => now()->startOfMonth()->addHours(6)]);
        activityLogReportLog($lead, ['activity_type_id' => $email->id, 'done_at' => now()->subYears(2)]);

        $filters = ActivityReport::getDefaultFilters();

        $result = ActivityReport::aggregate(
            ActivityReport::applyFilters(activityLogReportQuery([$lead]), $filters),
            $filters['group_by'],
            $filters['split_by'],
            $filters['measure'],
        );

        $twoMonthsAgo = now()->startOfMonth()->subMonths(2)->format('Y-m');
        $thisMonth = now()->startOfMonth()->format('Y-m');

        expect($result['labels'])->toBe([Dimension::monthLabel($twoMonthsAgo), Dimension::monthLabel($thisMonth)]);

        $series = collect($result['series'])->mapWithKeys(fn (array $series): array => [$series['label'] => $series['data']])->all();

        expect($series)->toEqual([
            'Default Graph Call'  => [1.0, 1.0],
            'Default Graph Email' => [0.0, 1.0],
        ]);
    });
});

describe('presets', function () {
    it('applies the quick filter', function (string $preset, array $expected) {
        CRMHelper::actingAs([]);

        $other = activityLogReportUser();

        $opportunity = CRMHelper::opportunity();
        $lead = CRMHelper::lead();

        $logs = [
            'recent mine opportunity' => activityLogReportLog($opportunity, ['done_at' => now()->subMonth()]),
            'old mine opportunity'    => activityLogReportLog($opportunity, ['done_at' => now()->subMonths(13)]),
            'recent done by me lead'  => activityLogReportLog($lead, ['done_at' => now()->subMonth(), 'assigned_to' => $other->id]),
            'recent assigned me lead' => activityLogReportLog($lead, ['done_at' => now()->subMonth(), 'done_by' => $other->id]),
        ];

        $ids = ActivityReport::applyFilters(activityLogReportQuery([$opportunity, $lead]), ['presets' => [$preset]])
            ->pluck('crm_activity_logs.id')
            ->all();

        $expectedIds = collect($expected)->map(fn (string $name): int => $logs[$name]->id)->all();

        expect($ids)->toEqualCanonicalizing($expectedIds);
    })->with([
        'last 12 months' => ['last_12_months', ['recent mine opportunity', 'recent done by me lead', 'recent assigned me lead']],
        'my activities'  => ['my_activities', ['recent mine opportunity', 'old mine opportunity', 'recent assigned me lead']],
        'done by me'     => ['done_by_me', ['recent mine opportunity', 'old mine opportunity', 'recent done by me lead']],
        'opportunities'  => ['opportunities', ['recent mine opportunity', 'old mine opportunity']],
        'leads'          => ['leads', ['recent done by me lead', 'recent assigned me lead']],
    ]);

    it('offers exactly the activity quick filters, with Last 12 Months on by default', function () {
        expect(array_keys(ActivityReport::presets()))->toBe(['last_12_months', 'my_activities', 'done_by_me', 'opportunities', 'leads'])
            ->and(ActivityReport::getDefaultPresets())->toBe(['last_12_months']);
    });

    it('filters by the salesperson and team recorded on the log', function () {
        CRMHelper::actingAs([]);

        $other = activityLogReportUser();
        $team = Team::factory()->create(['company_id' => CRMHelper::companyId()]);
        $lead = CRMHelper::opportunity();

        $mine = activityLogReportLog($lead, ['team_id' => $team->id]);
        $theirs = activityLogReportLog($lead, ['user_id' => $other->id, 'team_id' => null]);

        expect(ActivityReport::applyFilters(activityLogReportQuery([$lead]), ['user_ids' => [$other->id]])->pluck('crm_activity_logs.id')->all())
            ->toBe([$theirs->id])
            ->and(ActivityReport::applyFilters(activityLogReportQuery([$lead]), ['team_ids' => [$team->id]])->pluck('crm_activity_logs.id')->all())
            ->toBe([$mine->id]);
    });
});

describe('grouping', function () {
    it('groups by activity type', function () {
        CRMHelper::actingAs([]);

        $lead = CRMHelper::opportunity();
        $call = activityLogReportType('Group Call');
        $meeting = activityLogReportType('Group Meeting');

        activityLogReportLog($lead, ['activity_type_id' => $call->id]);
        activityLogReportLog($lead, ['activity_type_id' => $call->id]);
        activityLogReportLog($lead, ['activity_type_id' => $meeting->id]);
        activityLogReportLog($lead, ['activity_type_id' => null]);

        $result = ActivityReport::aggregate(activityLogReportQuery([$lead]), 'activity_type', null, 'count');

        expect($result['labels'])->toBe(['None', 'Group Call', 'Group Meeting'])
            ->and($result['series'][0]['data'])->toBe([1.0, 2.0, 1.0]);
    });

    it('groups by salesperson', function () {
        $user = CRMHelper::actingAs([]);

        $other = activityLogReportUser();
        $lead = CRMHelper::opportunity();

        activityLogReportLog($lead);
        activityLogReportLog($lead, ['user_id' => $other->id]);
        activityLogReportLog($lead, ['user_id' => $other->id]);

        $result = ActivityReport::aggregate(activityLogReportQuery([$lead]), 'salesperson', null, 'count');

        expect(activityLogReportTotals($result))->toEqual([
            $user->name  => 1.0,
            $other->name => 2.0,
        ]);
    });

    it('groups by stage in the pipeline order', function () {
        CRMHelper::actingAs([]);

        $earlier = Stage::factory()->create(['name' => 'Group Stage Earlier']);
        $later = Stage::factory()->create(['name' => 'Group Stage Later']);
        $lead = CRMHelper::opportunity();

        activityLogReportLog($lead, ['stage_id' => $later->id]);
        activityLogReportLog($lead, ['stage_id' => $earlier->id]);
        activityLogReportLog($lead, ['stage_id' => $later->id]);

        $result = ActivityReport::aggregate(activityLogReportQuery([$lead]), 'stage', null, 'count');

        expect($result['labels'])->toBe(['Group Stage Earlier', 'Group Stage Later'])
            ->and($result['series'][0]['data'])->toBe([1.0, 2.0]);
    });

    it('groups by the expected closing month of the lead', function () {
        CRMHelper::actingAs([]);

        $may = CRMHelper::opportunity(['expected_closing_date' => '2026-05-10']);
        $august = CRMHelper::opportunity(['expected_closing_date' => '2026-08-01']);
        $undated = CRMHelper::opportunity(['expected_closing_date' => null]);

        activityLogReportLog($may);
        activityLogReportLog($may);
        activityLogReportLog($august);
        activityLogReportLog($undated);

        $result = ActivityReport::aggregate(activityLogReportQuery([$may, $august, $undated]), 'expected_closing', null, 'count');

        expect($result['labels'])->toBe(['None', Dimension::monthLabel('2026-05'), Dimension::monthLabel('2026-08')])
            ->and($result['series'][0]['data'])->toBe([1.0, 2.0, 1.0]);
    });

    it('reads the lead expected closing date live, not from the log', function () {
        CRMHelper::actingAs([]);

        $lead = CRMHelper::opportunity(['expected_closing_date' => '2026-05-10']);

        activityLogReportLog($lead);

        $lead->update(['expected_closing_date' => '2026-09-15']);

        $result = ActivityReport::aggregate(activityLogReportQuery([$lead]), 'expected_closing', null, 'count');

        expect($result['labels'])->toBe([Dimension::monthLabel('2026-09')]);
    });

    it('groups by the record type the activity was done on', function () {
        CRMHelper::actingAs([]);

        $opportunity = CRMHelper::opportunity();
        $lead = CRMHelper::lead();

        activityLogReportLog($opportunity);
        activityLogReportLog($lead);
        activityLogReportLog($lead);

        $result = ActivityReport::aggregate(activityLogReportQuery([$opportunity, $lead]), 'lead_type', null, 'count');

        expect(activityLogReportTotals($result))->toEqual([
            __('crm::models/lead.title')        => 2.0,
            __('crm::models/opportunity.title') => 1.0,
        ]);
    });
});
