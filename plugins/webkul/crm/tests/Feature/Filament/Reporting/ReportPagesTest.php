<?php

use Filament\Actions\Exports\Models\Export;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource\Pages\GraphActivityAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource\Pages\ListActivityAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource\Pages\SummaryActivityAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages\GraphForecast;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages\KanbanForecast;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages\ListForecast;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages\SummaryForecast;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource\Pages\GraphLeadAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource\Pages\ListLeadAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource\Pages\SummaryLeadAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource\Pages\GraphPipelineAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource\Pages\ListPipelineAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource\Pages\SummaryPipelineAnalysis;
use Webkul\Crm\Filament\Exports\ForecastExporter;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Filament\Support\Reports\Widgets\ReportChart;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

function reportPagesDataset(): array
{
    return [
        'forecast'   => [ForecastResource::class, GraphForecast::class, SummaryForecast::class, ListForecast::class, 'view_any_crm_forecast', 'graph'],
        'pipeline'   => [PipelineAnalysisResource::class, GraphPipelineAnalysis::class, SummaryPipelineAnalysis::class, ListPipelineAnalysis::class, 'view_any_crm_pipeline::analysis', 'index'],
        'leads'      => [LeadAnalysisResource::class, GraphLeadAnalysis::class, SummaryLeadAnalysis::class, ListLeadAnalysis::class, 'view_any_crm_lead::analysis', 'index'],
        'activities' => [ActivityAnalysisResource::class, GraphActivityAnalysis::class, SummaryActivityAnalysis::class, ListActivityAnalysis::class, 'view_any_crm_activity::analysis', 'index'],
    ];
}

function reportPagesUser(): User
{
    return User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => CrmTestHelper::companyId(),
        'is_active'          => true,
    ]));
}

function reportPagesActivityLog(Lead $lead, array $attributes = []): ActivityLog
{
    return ActivityLog::query()->create([
        'lead_id'     => $lead->id,
        'lead_type'   => $lead->type,
        'user_id'     => $lead->user_id,
        'stage_id'    => $lead->stage_id,
        'company_id'  => $lead->company_id,
        'assigned_to' => $lead->user_id,
        'done_by'     => $lead->user_id,
        'summary'     => 'Report pages activity',
        'done_at'     => now(),
        ...$attributes,
    ]);
}

dataset('report pages', reportPagesDataset());

it('renders the graph, summary and list pages with the report permission', function (string $resource, string $graph, string $summary, string $list, string $permission) {
    CrmTestHelper::actingAs([$permission]);

    Livewire::test($graph)->assertOk()->assertSeeLivewire(ReportChart::class);
    Livewire::test($summary)->assertOk();
    Livewire::test($list)->assertOk();
})->with('report pages');

it('forbids the graph, summary and list pages without the report permission', function (string $resource, string $graph, string $summary, string $list, string $permission) {
    $otherReportPermissions = collect(reportPagesDataset())
        ->pluck(4)
        ->reject(fn (string $other): bool => $other === $permission)
        ->values()
        ->all();

    CrmTestHelper::actingAs([...$otherReportPermissions, ...CrmTestHelper::permissionsFor(['lead', 'opportunity'])]);

    Livewire::test($graph)->assertForbidden();
    Livewire::test($summary)->assertForbidden();
    Livewire::test($list)->assertForbidden();
})->with('report pages');

it('authorizes the report resource with its own permission only', function (string $resource, string $graph, string $summary, string $list, string $permission) {
    CrmTestHelper::actingAs([]);

    expect($resource::canViewAny())->toBeFalse();

    CrmTestHelper::actingAs([$permission]);

    expect($resource::canViewAny())->toBeTrue();
})->with('report pages');

it('shows the left menu of the reporting section', function (string $resource, string $graph, string $summary, string $list, string $permission) {
    CrmTestHelper::actingAs(['view_any_crm_forecast', 'view_any_crm_pipeline::analysis', 'view_any_crm_lead::analysis', 'view_any_crm_activity::analysis']);

    expect(Livewire::test($graph)->instance()->getSubNavigation())->not->toBeEmpty();
})->with('report pages');

describe('view switcher', function () {
    it('shows a toggle button for each other view, not for the current one', function (string $resource, string $graph, string $summary, string $list, string $permission, string $graphPage) {
        CrmTestHelper::actingAs([$permission]);

        $pages = [$graphPage => $graph, 'summary' => $summary, 'list' => $list];

        foreach ($pages as $pageName => $page) {
            $component = Livewire::test($page);

            foreach ($resource::getReportViews() as $viewPage => [$icon, $view]) {
                if ($viewPage === $pageName) {
                    $component->assertActionDoesNotExist('view_'.$view);

                    continue;
                }

                $component
                    ->assertActionExists('view_'.$view)
                    ->assertActionHasUrl('view_'.$view, $resource::getUrl($viewPage))
                    ->assertActionHasColor('view_'.$view, 'gray');
            }

            expect($resource::getViewSwitcherActions($pageName))->toHaveCount(($resource === ForecastResource::class ? 4 : 3) - 1);
        }
    })->with('report pages');

    it('offers the kanban button only on the forecast', function () {
        CrmTestHelper::actingAs(['view_any_crm_forecast', 'view_any_crm_pipeline::analysis']);

        Livewire::test(GraphForecast::class)->assertActionExists('view_kanban');
        Livewire::test(GraphPipelineAnalysis::class)->assertActionDoesNotExist('view_kanban');
    });
});

describe('graph', function () {
    it('starts the filter form with the report defaults', function (string $resource, string $graph, string $summary, string $list, string $permission) {
        CrmTestHelper::actingAs([$permission]);

        /** @var class-string<Report> $report */
        $report = $resource::getReport();
        $defaults = $report::getDefaultFilters();

        $component = Livewire::test($graph)
            ->assertSet('filters.measure', $defaults['measure'])
            ->assertSet('filters.group_by', $defaults['group_by'])
            ->assertSet('filters.split_by', $defaults['split_by'])
            ->assertSet('filters.chart_type', $defaults['chart_type']);

        $filters = $component->get('filters');

        expect($filters)->toHaveKeys(['measure', 'group_by', 'split_by', 'chart_type', 'presets', 'user_ids', 'team_ids'])
            ->and($filters['presets'])->toEqualCanonicalizing($defaults['presets'])
            ->and($filters['user_ids'] ?? [])->toBe([])
            ->and($filters['team_ids'] ?? [])->toBe([]);
    })->with('report pages');

    it('offers the report measures, dimensions and quick filters as options', function () {
        CrmTestHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $form = Livewire::test(GraphPipelineAnalysis::class)->instance()->getFiltersForm();

        $components = $form->getFlatComponents(withHidden: true);

        expect($components)->toHaveKeys(['measure', 'group_by', 'split_by', 'chart_type', 'presets', 'user_ids', 'team_ids'])
            ->and(array_keys($components['measure']->getOptions()))->toBe(array_keys(PipelineAnalysisResource::getReport()::getMeasureOptions()))
            ->and(array_keys($components['group_by']->getOptions()))->toBe(array_keys(PipelineAnalysisResource::getReport()::getDimensionOptions()))
            ->and(array_keys($components['presets']->getOptions()))->toBe(array_keys(PipelineAnalysisResource::getReport()::getPresetOptions()))
            ->and(array_keys($components['chart_type']->getOptions()))->toBe([Report::CHART_BAR, Report::CHART_LINE, Report::CHART_PIE]);
    });

    it('re-keys the chart component when the filters change', function (string $resource, string $graph, string $summary, string $list, string $permission) {
        CrmTestHelper::actingAs([$permission]);

        $component = Livewire::test($graph);

        $hashBefore = md5(json_encode($component->get('filters')));

        expect(collect(array_keys($component->snapshot['memo']['children'] ?? []))->contains(fn (string $key): bool => str_contains($key, 'crm-report-chart-'.$hashBefore)))
            ->toBeTrue();

        $component->set('filters.chart_type', Report::CHART_PIE);

        $hashAfter = md5(json_encode($component->get('filters')));

        expect($hashAfter)->not->toBe($hashBefore)
            ->and(collect(array_keys($component->snapshot['memo']['children'] ?? []))->contains(fn (string $key): bool => str_contains($key, 'crm-report-chart-'.$hashAfter)))
            ->toBeTrue();
    })->with('report pages');

    it('keeps the chosen filters in the session', function () {
        CrmTestHelper::actingAs(['view_any_crm_pipeline::analysis']);

        Livewire::test(GraphPipelineAnalysis::class)->set('filters.measure', 'count');

        Livewire::test(GraphPipelineAnalysis::class)->assertSet('filters.measure', 'count');
    });
});

describe('summary', function () {
    it('is a groups-only table grouped by the report default, with one column per measure', function (string $resource, string $graph, string $summary, string $list, string $permission) {
        CrmTestHelper::actingAs([$permission]);

        /** @var class-string<Report> $report */
        $report = $resource::getReport();

        $table = Livewire::test($summary)->instance()->getTable();

        $defaultDimension = $report::getDimension($report::getDefaultGroupBy());

        expect($table->isGroupsOnly())->toBeTrue()
            ->and($table->getDefaultGroup()?->getId())->toBe($report::getDefaultGroupBy())
            ->and(array_keys($table->getGroups()))->toBe(array_keys($report::dimensions()))
            ->and(array_keys($table->getColumns()))->toBe([
                $defaultDimension->getGroupColumn(),
                ...array_map(fn (string $measure): string => 'measure_'.$measure, array_keys($report::measures())),
            ]);
    })->with('report pages');

    it('renames the first column after the chosen group', function (string $resource, string $graph, string $summary, string $list, string $permission) {
        CrmTestHelper::actingAs([$permission]);

        $component = Livewire::test($summary)->set('tableGrouping', 'salesperson');

        expect(array_key_first($component->instance()->getTable()->getColumns()))->toBe('user.name');

        $component->set('tableGrouping', 'stage');

        expect(array_key_first($component->instance()->getTable()->getColumns()))->toBe('stage.name');
    })->with('report pages');

    it('shows a row per group', function () {
        CrmTestHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $stage = Stage::factory()->create(['name' => 'Summary Row Stage']);
        CrmTestHelper::opportunity(['stage_id' => $stage->id, 'expected_revenue' => 1234]);

        Livewire::test(SummaryPipelineAnalysis::class)
            ->assertOk()
            ->assertSee('Summary Row Stage');
    });
});

describe('list', function () {
    it('turns on the default quick filters', function (string $resource, string $graph, string $summary, string $list, string $permission) {
        CrmTestHelper::actingAs([$permission]);

        /** @var class-string<Report> $report */
        $report = $resource::getReport();

        $component = Livewire::test($list);

        foreach (array_keys($report::presets()) as $preset) {
            $component->assertTableFilterExists($preset);

            expect((bool) ($component->get('tableFilters')[$preset]['isActive'] ?? false))
                ->toBe(in_array($preset, $report::getDefaultPresets(), true));
        }

        $component
            ->assertTableFilterExists('user_ids')
            ->assertTableFilterExists('team_ids');
    })->with('report pages');

    it('lists the upcoming opportunities of the current user on the forecast', function () {
        CrmTestHelper::actingAs(['view_any_crm_forecast']);

        $upcoming = CrmTestHelper::opportunity(['expected_closing_date' => today()->addMonth()]);
        $past = CrmTestHelper::opportunity(['expected_closing_date' => today()->subMonths(2)->startOfMonth()]);
        $theirs = CrmTestHelper::opportunity(['expected_closing_date' => today()->addMonth(), 'user_id' => reportPagesUser()->id]);
        $lead = CrmTestHelper::lead(['expected_closing_date' => today()->addMonth()]);

        Livewire::test(ListForecast::class)
            ->assertCanSeeTableRecords([$upcoming])
            ->assertCanNotSeeTableRecords([$past, $theirs, $lead]);
    });

    it('lists every salesperson once My Pipeline is turned off', function () {
        CrmTestHelper::actingAs(['view_any_crm_forecast']);

        $theirs = CrmTestHelper::opportunity(['expected_closing_date' => today()->addMonth(), 'user_id' => reportPagesUser()->id]);

        Livewire::test(ListForecast::class)
            ->filterTable('my_pipeline', false)
            ->assertCanSeeTableRecords([$theirs]);
    });

    it('lists active opportunities on the pipeline analysis', function () {
        CrmTestHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $active = CrmTestHelper::opportunity();
        $lost = CrmTestHelper::opportunity();
        $lost->markAsLost(null);
        $lead = CrmTestHelper::lead();

        Livewire::test(ListPipelineAnalysis::class)
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$lost, $lead]);
    });

    it('lists leads and opportunities created this year, lost ones included, on the leads analysis', function () {
        CrmTestHelper::actingAs(['view_any_crm_lead::analysis']);

        $lead = CrmTestHelper::lead();
        $opportunity = CrmTestHelper::opportunity();
        $lost = CrmTestHelper::lead();
        $lost->markAsLost(null);
        $old = CrmTestHelper::opportunity(['created_at' => now()->subYear()->startOfYear()]);

        Livewire::test(ListLeadAnalysis::class)
            ->assertCanSeeTableRecords([$lead, $opportunity, $lost])
            ->assertCanNotSeeTableRecords([$old]);
    });

    it('lists the activities completed in the last 12 months on the activities analysis', function () {
        CrmTestHelper::actingAs(['view_any_crm_activity::analysis']);

        $opportunity = CrmTestHelper::opportunity();

        $recent = reportPagesActivityLog($opportunity, ['done_at' => now()->subMonth()]);
        $old = reportPagesActivityLog($opportunity, ['done_at' => now()->subMonths(13)]);

        Livewire::test(ListActivityAnalysis::class)
            ->assertCanSeeTableRecords([$recent])
            ->assertCanNotSeeTableRecords([$old]);
    });
});

describe('forecast export', function () {
    it('shows export on every forecast view', function (string $page) {
        CrmTestHelper::actingAs(['view_any_crm_forecast']);

        Livewire::test($page)->assertActionVisible('export');
    })->with([
        'kanban'  => KanbanForecast::class,
        'graph'   => GraphForecast::class,
        'summary' => SummaryForecast::class,
        'list'    => ListForecast::class,
    ]);

    it('has no export on the other reports', function () {
        CrmTestHelper::actingAs(['view_any_crm_pipeline::analysis']);

        Livewire::test(GraphPipelineAnalysis::class)->assertActionDoesNotExist('export');

        expect(PipelineAnalysisResource::getExporter())->toBeNull();
    });

    it('exports the prorated revenue and status of an opportunity', function () {
        CrmTestHelper::actingAsCrmUser(['opportunity']);

        $opportunity = CrmTestHelper::opportunity(['expected_revenue' => 1000, 'probability' => 25]);

        $export = Export::query()->create([
            'exporter'   => ForecastExporter::class,
            'total_rows' => 1,
            'file_disk'  => 'local',
            'user_id'    => auth()->id(),
        ]);

        $columns = collect(ForecastExporter::getColumns())
            ->mapWithKeys(fn ($column): array => [$column->getName() => $column->getLabel()])
            ->all();

        $row = array_combine(array_keys($columns), (new ForecastExporter($export, $columns, []))($opportunity->fresh()));

        expect((float) $row['prorated_revenue'])->toBe(250.0)
            ->and($row['forecast_status'])->toBe(__('crm::filament/clusters/reporting/report.presets.ongoing'));
    });
});

it('clears every optional graph filter, the split included, and keeps it cleared', function (string $page, array $permissions) {
    CrmTestHelper::actingAs($permissions);

    $component = Livewire::test($page)
        ->set('filters.split_by', null)
        ->set('filters.presets', [])
        ->set('filters.user_ids', [])
        ->set('filters.team_ids', []);

    expect($component->instance()->hasActiveFilters())->toBeFalse()
        ->and($component->get('filters.split_by'))->toBeNull()
        ->and($component->instance()->hasChangedFilters())->toBeTrue();
})->with([
    'pipeline'   => [GraphPipelineAnalysis::class, ['view_any_crm_pipeline::analysis']],
    'leads'      => [GraphLeadAnalysis::class, ['view_any_crm_lead::analysis']],
    'activities' => [GraphActivityAnalysis::class, ['view_any_crm_activity::analysis']],
]);
