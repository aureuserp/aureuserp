<?php

use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\Crm\Enums\MeasureFormat;
use Webkul\Crm\Filament\Support\Reports\ActivityReport;
use Webkul\Crm\Filament\Support\Reports\Dimension;
use Webkul\Crm\Filament\Support\Reports\ForecastReport;
use Webkul\Crm\Filament\Support\Reports\LeadReport;
use Webkul\Crm\Filament\Support\Reports\LeadsAnalysisReport;
use Webkul\Crm\Filament\Support\Reports\Measure;
use Webkul\Crm\Filament\Support\Reports\PipelineReport;
use Webkul\Crm\Filament\Support\Reports\Sql;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

/**
 * A query over just the given leads, so other records never affect the totals.
 *
 * @param  array<int, Lead>  $leads
 */
function reportEngineLeadQuery(array $leads): Builder
{
    return Lead::withTrashed()->whereIn('crm_leads.id', collect($leads)->pluck('id')->all());
}

function reportEngineUser(): User
{
    return User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => CRMHelper::companyId(),
        'is_active'          => true,
    ]));
}

/**
 * The aggregated series as label => data (or label => value for a single group).
 *
 * @param  array{labels: array<int, string>, series: array<int, array{label: string, data: array<int, float>}>}  $result
 * @return array<string, array<string, float>>
 */
function reportEngineTable(array $result): array
{
    return collect($result['series'])
        ->mapWithKeys(fn (array $series): array => [$series['label'] => array_combine($result['labels'], $series['data'])])
        ->all();
}

describe('default filters', function () {
    it('starts the forecast on prorated revenue by expected closing, with My Pipeline, Upcoming Closings and Active', function () {
        $filters = ForecastReport::getDefaultFilters();

        expect($filters['measure'])->toBe('prorated_revenue')
            ->and($filters['group_by'])->toBe('expected_closing')
            ->and($filters['split_by'])->toBeNull()
            ->and($filters['presets'])->toEqualCanonicalizing(['my_pipeline', 'upcoming_closings', 'active'])
            ->and($filters['user_ids'])->toBe([])
            ->and($filters['team_ids'])->toBe([]);
    });

    it('starts the pipeline analysis on expected revenue by stage, split by expected closing, Active only', function () {
        $filters = PipelineReport::getDefaultFilters();

        expect($filters['measure'])->toBe('expected_revenue')
            ->and($filters['group_by'])->toBe('stage')
            ->and($filters['split_by'])->toBe('expected_closing')
            ->and($filters['presets'])->toBe(['active']);
    });

    it('starts the leads analysis on count by creation month, split by team, Created This Year', function () {
        $filters = LeadsAnalysisReport::getDefaultFilters();

        expect($filters['measure'])->toBe('count')
            ->and($filters['group_by'])->toBe('created_at')
            ->and($filters['split_by'])->toBe('team')
            ->and($filters['presets'])->toBe(['created_this_year']);
    });

    it('starts the activities analysis on count by completion month, split by activity type, Last 12 Months', function () {
        $filters = ActivityReport::getDefaultFilters();

        expect($filters['measure'])->toBe('count')
            ->and($filters['group_by'])->toBe('done_at')
            ->and($filters['split_by'])->toBe('activity_type')
            ->and($filters['presets'])->toBe(['last_12_months']);
    });

    it('offers the expected filter keys for every report', function (string $report) {
        expect(array_keys($report::getDefaultFilters()))
            ->toBe(['measure', 'group_by', 'split_by', 'presets', 'user_ids', 'team_ids']);
    })->with([ForecastReport::class, PipelineReport::class, LeadsAnalysisReport::class, ActivityReport::class]);

    it('only offers the record type dimension on the leads analysis', function () {
        expect(ForecastReport::dimensions())->not->toHaveKey('type')
            ->and(PipelineReport::dimensions())->not->toHaveKey('type')
            ->and(LeadsAnalysisReport::dimensions())->toHaveKey('type');
    });

    it('offers only the count measure for activities', function () {
        expect(array_keys(ActivityReport::measures()))->toBe(['count'])
            ->and(array_keys(LeadsAnalysisReport::measures()))
            ->toBe(['count', 'expected_revenue', 'prorated_revenue', 'probability', 'days_to_assign', 'days_to_close']);
    });

    it('falls back to the default measure and to no dimension for unknown keys', function () {
        expect(ForecastReport::getMeasure('unknown')->aggregate)->toBe(ForecastReport::measures()['prorated_revenue']->aggregate)
            ->and(ForecastReport::getDimension('unknown'))->toBeNull()
            ->and(ForecastReport::getDimension(null))->toBeNull()
            ->and(ForecastReport::getDimension('stage'))->toBeInstanceOf(Dimension::class);
    });
});

describe('applyFilters', function () {
    it('applies the My Pipeline and Unassigned presets', function () {
        $user = CRMHelper::actingAs([]);

        $mine = CRMHelper::opportunity();
        $theirs = CRMHelper::opportunity(['user_id' => reportEngineUser()->id]);
        $unassigned = CRMHelper::opportunity();
        Lead::query()->whereKey($unassigned->id)->update(['user_id' => null]);

        $leads = [$mine, $theirs, $unassigned];

        expect(ForecastReport::applyFilters(reportEngineLeadQuery($leads), ['presets' => ['my_pipeline']])->pluck('crm_leads.id')->all())
            ->toBe([$mine->id])
            ->and(ForecastReport::applyFilters(reportEngineLeadQuery($leads), ['presets' => ['unassigned']])->pluck('crm_leads.id')->all())
            ->toBe([$unassigned->id]);
    });

    it('applies the Upcoming Closings, Active and Lost presets', function () {
        CRMHelper::actingAs([]);

        $upcoming = CRMHelper::opportunity(['expected_closing_date' => today()->addMonth()]);
        $past = CRMHelper::opportunity(['expected_closing_date' => today()->subMonths(2)->startOfMonth()]);
        $lost = CRMHelper::opportunity(['expected_closing_date' => today()->addMonth()]);
        $lost->markAsLost(null);

        $leads = [$upcoming, $past, $lost];

        expect(ForecastReport::applyFilters(reportEngineLeadQuery($leads), ['presets' => ['upcoming_closings', 'active']])->pluck('crm_leads.id')->all())
            ->toBe([$upcoming->id])
            ->and(ForecastReport::applyFilters(reportEngineLeadQuery($leads), ['presets' => ['lost']])->pluck('crm_leads.id')->all())
            ->toBe([$lost->id]);
    });

    it('applies the Won and Ongoing presets', function () {
        CRMHelper::actingAs([]);

        $won = CRMHelper::opportunity(['stage_id' => CRMHelper::wonStage()->id]);
        $ongoing = CRMHelper::opportunity(['stage_id' => CRMHelper::firstStage()->id]);

        $leads = [$won, $ongoing];

        expect(PipelineReport::applyFilters(reportEngineLeadQuery($leads), ['presets' => ['won']])->pluck('crm_leads.id')->all())
            ->toBe([$won->id])
            ->and(PipelineReport::applyFilters(reportEngineLeadQuery($leads), ['presets' => ['ongoing']])->pluck('crm_leads.id')->all())
            ->toBe([$ongoing->id]);
    });

    it('applies the Leads, Opportunities and Created This Year presets', function () {
        CRMHelper::actingAs([]);

        $lead = CRMHelper::lead();
        $opportunity = CRMHelper::opportunity();
        $oldOpportunity = CRMHelper::opportunity(['created_at' => now()->subYear()->startOfYear()]);

        $leads = [$lead, $opportunity, $oldOpportunity];

        expect(LeadsAnalysisReport::applyFilters(reportEngineLeadQuery($leads), ['presets' => ['leads']])->pluck('crm_leads.id')->all())
            ->toBe([$lead->id])
            ->and(LeadsAnalysisReport::applyFilters(reportEngineLeadQuery($leads), ['presets' => ['opportunities', 'created_this_year']])->pluck('crm_leads.id')->all())
            ->toBe([$opportunity->id]);
    });

    it('ignores presets the report does not offer', function () {
        CRMHelper::actingAs([]);

        $lead = CRMHelper::lead();

        expect(ForecastReport::presets())->not->toHaveKey('leads')
            ->and(ForecastReport::applyFilters(reportEngineLeadQuery([$lead]), ['presets' => ['leads', 'no_such_preset']])->count())
            ->toBe(1);
    });

    it('filters by salespeople and sales teams', function () {
        CRMHelper::actingAs([]);

        $otherUser = reportEngineUser();
        $team = Team::factory()->create(['company_id' => CRMHelper::companyId()]);

        $mineInTeam = CRMHelper::opportunity(['team_id' => $team->id]);
        $mineNoTeam = CRMHelper::opportunity(['team_id' => null]);
        $theirsInTeam = CRMHelper::opportunity(['team_id' => $team->id, 'user_id' => $otherUser->id]);

        $leads = [$mineInTeam, $mineNoTeam, $theirsInTeam];

        expect(ForecastReport::applyFilters(reportEngineLeadQuery($leads), ['user_ids' => [$otherUser->id]])->pluck('crm_leads.id')->all())
            ->toBe([$theirsInTeam->id])
            ->and(ForecastReport::applyFilters(reportEngineLeadQuery($leads), ['team_ids' => [$team->id]])->pluck('crm_leads.id')->sort()->values()->all())
            ->toBe(collect([$mineInTeam->id, $theirsInTeam->id])->sort()->values()->all())
            ->and(ForecastReport::applyFilters(reportEngineLeadQuery($leads), ['user_ids' => [], 'team_ids' => []])->count())
            ->toBe(3);
    });
});

describe('aggregate', function () {
    it('totals a relation dimension, labelled with the related names in their sort order', function () {
        CRMHelper::actingAs([]);

        $firstStage = Stage::factory()->create(['name' => 'Report Stage Alpha']);
        $secondStage = Stage::factory()->create(['name' => 'Report Stage Beta']);

        $leads = [
            CRMHelper::opportunity(['stage_id' => $secondStage->id]),
            CRMHelper::opportunity(['stage_id' => $firstStage->id]),
            CRMHelper::opportunity(['stage_id' => $firstStage->id]),
        ];

        $result = PipelineReport::aggregate(reportEngineLeadQuery($leads), 'stage', null, 'count');

        expect($result['labels'])->toBe(['Report Stage Alpha', 'Report Stage Beta'])
            ->and($result['series'])->toHaveCount(1)
            ->and($result['series'][0]['label'])->toBe('Count')
            ->and($result['series'][0]['data'])->toBe([2.0, 1.0]);
    });

    it('labels a missing relation as "None"', function () {
        CRMHelper::actingAs([]);

        $team = Team::factory()->create(['company_id' => CRMHelper::companyId(), 'name' => 'Report Team Gamma']);

        $leads = [
            CRMHelper::opportunity(['team_id' => $team->id]),
            CRMHelper::opportunity(['team_id' => null]),
        ];

        $result = PipelineReport::aggregate(reportEngineLeadQuery($leads), 'team', null, 'count');

        expect($result['labels'])->toBe(['None', 'Report Team Gamma'])
            ->and($result['series'][0]['data'])->toBe([1.0, 1.0]);
    });

    it('totals a salesperson dimension by user name', function () {
        $user = CRMHelper::actingAs([]);
        $otherUser = reportEngineUser();

        $leads = [
            CRMHelper::opportunity(),
            CRMHelper::opportunity(),
            CRMHelper::opportunity(['user_id' => $otherUser->id]),
        ];

        $result = PipelineReport::aggregate(reportEngineLeadQuery($leads), 'salesperson', null, 'count');

        expect(reportEngineTable($result)['Count'])->toEqual([
            $user->name      => 2.0,
            $otherUser->name => 1.0,
        ]);
    });

    it('totals a month dimension with YYYY-MM keys, "None" first and months in order', function () {
        CRMHelper::actingAs([]);

        $leads = [
            CRMHelper::opportunity(['expected_closing_date' => '2026-03-15']),
            CRMHelper::opportunity(['expected_closing_date' => null]),
            CRMHelper::opportunity(['expected_closing_date' => '2026-01-10']),
            CRMHelper::opportunity(['expected_closing_date' => '2026-01-31']),
            CRMHelper::opportunity(['expected_closing_date' => '2025-12-01']),
        ];

        $result = ForecastReport::aggregate(reportEngineLeadQuery($leads), 'expected_closing', null, 'count');

        expect($result['labels'])->toBe([
            'None',
            Dimension::monthLabel('2025-12'),
            Dimension::monthLabel('2026-01'),
            Dimension::monthLabel('2026-03'),
        ])
            ->and($result['series'][0]['data'])->toBe([1.0, 1.0, 2.0, 1.0])
            ->and(Dimension::monthLabel('2026-01'))->toBe('January 2026');
    });

    it('sorts month keys chronologically and keeps a single "None" first', function () {
        $dimension = ForecastReport::dimensions()['expected_closing'];

        expect($dimension->sortKeys(['2026-03', null, '2025-11', '', '2026-01']))->toBe([null, '2025-11', '2026-01', '2026-03'])
            ->and($dimension->labels([null, '2026-03']))->toBe(['None', Dimension::monthLabel('2026-03')]);
    });

    it('totals an options dimension with its option labels', function () {
        CRMHelper::actingAs([]);

        $leads = [
            CRMHelper::lead(),
            CRMHelper::opportunity(),
            CRMHelper::opportunity(),
        ];

        $result = LeadsAnalysisReport::aggregate(reportEngineLeadQuery($leads), 'type', null, 'count');

        expect(reportEngineTable($result)['Count'])->toEqual([
            __('crm::models/lead.title')        => 1.0,
            __('crm::models/opportunity.title') => 2.0,
        ]);
    });

    it('splits a group into one series per split value', function () {
        CRMHelper::actingAs([]);

        $firstStage = Stage::factory()->create(['name' => 'Report Split Alpha']);
        $secondStage = Stage::factory()->create(['name' => 'Report Split Beta']);

        $leads = [
            CRMHelper::lead(['stage_id' => $firstStage->id]),
            CRMHelper::opportunity(['stage_id' => $firstStage->id]),
            CRMHelper::opportunity(['stage_id' => $secondStage->id]),
            CRMHelper::opportunity(['stage_id' => $secondStage->id]),
        ];

        $result = LeadsAnalysisReport::aggregate(reportEngineLeadQuery($leads), 'stage', 'type', 'count');

        expect($result['labels'])->toBe(['Report Split Alpha', 'Report Split Beta'])
            ->and($result['series'])->toHaveCount(2)
            ->and(reportEngineTable($result))->toEqual([
                __('crm::models/lead.title') => [
                    'Report Split Alpha' => 1.0,
                    'Report Split Beta'  => 0.0,
                ],
                __('crm::models/opportunity.title') => [
                    'Report Split Alpha' => 1.0,
                    'Report Split Beta'  => 2.0,
                ],
            ]);
    });

    it('ignores a split by the same dimension as the group', function () {
        CRMHelper::actingAs([]);

        $leads = [CRMHelper::opportunity(), CRMHelper::opportunity()];

        $result = PipelineReport::aggregate(reportEngineLeadQuery($leads), 'stage', 'stage', 'count');

        expect($result['series'])->toHaveCount(1)
            ->and($result['series'][0]['label'])->toBe('Count');
    });

    it('falls back to the default group for an unknown group key', function () {
        CRMHelper::actingAs([]);

        $stage = Stage::factory()->create(['name' => 'Report Fallback Stage']);
        $leads = [CRMHelper::opportunity(['stage_id' => $stage->id])];

        $result = PipelineReport::aggregate(reportEngineLeadQuery($leads), 'not_a_dimension', null, 'count');

        expect($result['labels'])->toBe(['Report Fallback Stage']);
    });

    it('returns no labels for an empty query', function () {
        $result = PipelineReport::aggregate(Lead::query()->whereRaw('1 = 0'), 'stage', null, 'count');

        expect($result['labels'])->toBe([])
            ->and($result['series'][0]['data'])->toBe([]);
    });

    it('computes each measure', function (string $measure, float $expected) {
        CRMHelper::actingAs([]);

        $stage = Stage::factory()->create(['name' => 'Report Measure Stage']);

        $leads = [
            CRMHelper::opportunity([
                'stage_id'         => $stage->id,
                'expected_revenue' => 1000,
                'probability'      => 50,
                'created_at'       => Carbon::parse('2026-01-01 00:00:00'),
                'assigned_at'      => Carbon::parse('2026-01-03 00:00:00'),
                'closed_at'        => Carbon::parse('2026-01-11 00:00:00'),
            ]),
            CRMHelper::opportunity([
                'stage_id'         => $stage->id,
                'expected_revenue' => 3000,
                'probability'      => 20,
                'created_at'       => Carbon::parse('2026-01-01 00:00:00'),
                'assigned_at'      => Carbon::parse('2026-01-05 00:00:00'),
                'closed_at'        => Carbon::parse('2026-01-21 00:00:00'),
            ]),
        ];

        $result = LeadsAnalysisReport::aggregate(reportEngineLeadQuery($leads), 'stage', null, $measure);

        expect($result['labels'])->toBe(['Report Measure Stage'])
            ->and($result['series'][0]['data'][0])->toEqual($expected);
    })->with([
        'count'            => ['count', 2.0],
        'expected revenue' => ['expected_revenue', 4000.0],
        'prorated revenue' => ['prorated_revenue', 1100.0],
        'probability'      => ['probability', 35.0],
        'days to assign'   => ['days_to_assign', 3.0],
        'days to close'    => ['days_to_close', 15.0],
    ]);

    it('aggregates every dimension with every measure, also as a split, without SQL errors', function (string $report) {
        CRMHelper::actingAs([]);

        $lead = CRMHelper::opportunity(['closed_at' => now(), 'converted_at' => now()]);

        $query = $report === ActivityReport::class
            ? ActivityLog::query()->whereIn('crm_activity_logs.lead_id', [$lead->id])
            : reportEngineLeadQuery([$lead]);

        if ($report === ActivityReport::class) {
            ActivityLog::query()->create([
                'lead_id'    => $lead->id,
                'lead_type'  => $lead->type,
                'user_id'    => $lead->user_id,
                'stage_id'   => $lead->stage_id,
                'company_id' => $lead->company_id,
                'done_at'    => now(),
            ]);
        }

        $combinations = 0;

        foreach (array_keys($report::dimensions()) as $dimension) {
            foreach (array_keys($report::measures()) as $measure) {
                $result = $report::aggregate(clone $query, $dimension, $report::getDefaultGroupBy() === $dimension ? null : $report::getDefaultGroupBy(), $measure);

                expect($result['labels'])->not->toBeEmpty()
                    ->and(array_sum(array_map(fn (array $series): float => array_sum($series['data']), $result['series'])))->toBeNumeric();

                $splitResult = $report::aggregate(clone $query, $report::getDefaultGroupBy(), $dimension, $measure);

                expect($splitResult['labels'])->not->toBeEmpty();

                $combinations++;
            }
        }

        expect($combinations)->toBe(count($report::dimensions()) * count($report::measures()));
    })->with([ForecastReport::class, PipelineReport::class, LeadsAnalysisReport::class, ActivityReport::class]);
});

describe('measures', function () {
    it('rounds integers to whole numbers and other formats to two decimals', function () {
        expect((new Measure('Count', 'count(*)'))->round('2.6'))->toBe(3.0)
            ->and((new Measure('Revenue', 'sum(1)', MeasureFormat::CURRENCY))->round('2.666'))->toBe(2.67)
            ->and((new Measure('Days', 'avg(1)', MeasureFormat::DAYS))->round(null))->toBe(0.0);
    });

    it('uses the expected aggregates on crm_leads', function () {
        $measures = LeadReport::measures();

        expect($measures['count']->aggregate)->toBe('count(*)')
            ->and($measures['expected_revenue']->aggregate)->toBe('sum(crm_leads.expected_revenue)')
            ->and($measures['prorated_revenue']->aggregate)->toBe('sum(crm_leads.expected_revenue * crm_leads.probability / 100)')
            ->and($measures['probability']->aggregate)->toBe('avg(crm_leads.probability)')
            ->and($measures['expected_revenue']->format)->toBe(MeasureFormat::CURRENCY)
            ->and($measures['probability']->format)->toBe(MeasureFormat::PERCENT)
            ->and($measures['days_to_close']->format)->toBe(MeasureFormat::DAYS);
    });
});

describe('sql', function () {
    it('builds the month expression for the current driver', function () {
        $expected = match (DB::connection()->getDriverName()) {
            'pgsql'  => "to_char(crm_leads.created_at, 'YYYY-MM')",
            'sqlite' => "strftime('%Y-%m', crm_leads.created_at)",
            'sqlsrv' => "format(crm_leads.created_at, 'yyyy-MM')",
            default  => "date_format(crm_leads.created_at, '%Y-%m')",
        };

        expect(Sql::month('crm_leads.created_at'))->toBe($expected);
    });

    it('extracts the YYYY-MM month of a date column', function () {
        CRMHelper::actingAs([]);

        $lead = CRMHelper::opportunity(['expected_closing_date' => '2026-07-19']);

        $month = Lead::query()
            ->whereKey($lead->id)
            ->selectRaw(Sql::month('crm_leads.expected_closing_date').' as closing_month')
            ->value('closing_month');

        expect($month)->toBe('2026-07');
    });

    it('computes the days between two datetime columns', function () {
        CRMHelper::actingAs([]);

        $lead = CRMHelper::opportunity([
            'created_at' => Carbon::parse('2026-01-01 00:00:00'),
            'closed_at'  => Carbon::parse('2026-01-04 12:00:00'),
        ]);

        $days = Lead::query()
            ->whereKey($lead->id)
            ->selectRaw(Sql::daysBetween('crm_leads.created_at', 'crm_leads.closed_at').' as days')
            ->value('days');

        expect(round((float) $days, 2))->toBe(3.5);
    });
});

describe('table groups', function () {
    it('turns a dimension into a summary table group with the right id and column', function (string $report, string $key, string $column, string $label) {
        $group = $report::dimensions()[$key]->toTableGroup($key);

        expect($group)->toBeInstanceOf(Group::class)
            ->and($group->getId())->toBe($key)
            ->and($group->getColumn())->toBe($column)
            ->and($group->getLabel())->toBe($label);
    })->with([
        'relation'           => [PipelineReport::class, 'stage', 'stage.name', 'Stage'],
        'attribute'          => [PipelineReport::class, 'city', 'city', 'City'],
        'options'            => [LeadsAnalysisReport::class, 'type', 'type', 'Type'],
        'month'              => [ForecastReport::class, 'expected_closing', 'expected_closing_date', 'Expected Closing'],
        'activity relation'  => [ActivityReport::class, 'activity_type', 'activityType.name', 'Activity Type'],
        'activity subquery'  => [ActivityReport::class, 'expected_closing', 'lead.expected_closing_date', 'Expected Closing'],
    ]);

    it('builds one table group per dimension', function (string $report) {
        $groups = $report::getTableGroups();

        expect(collect($groups)->map(fn (Group $group): string => $group->getId())->all())
            ->toBe(array_keys($report::dimensions()));
    })->with([ForecastReport::class, PipelineReport::class, LeadsAnalysisReport::class, ActivityReport::class]);
});
