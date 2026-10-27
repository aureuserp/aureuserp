<?php

use Filament\Actions\Exports\Models\Export;
use Livewire\Livewire;
use Webkul\Crm\Enums\ReportChartType;
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
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

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
        'default_company_id' => CRMHelper::companyId(),
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
    CRMHelper::actingAs([$permission]);

    Livewire::test($graph)->assertOk()->assertSeeHtml('crm-report-graph');
    Livewire::test($summary)->assertOk();
    Livewire::test($list)->assertOk();
})->with('report pages');

it('forbids the graph, summary and list pages without the report permission', function (string $resource, string $graph, string $summary, string $list, string $permission) {
    $otherReportPermissions = collect(reportPagesDataset())
        ->pluck(4)
        ->reject(fn (string $other): bool => $other === $permission)
        ->values()
        ->all();

    CRMHelper::actingAs([...$otherReportPermissions, ...CRMHelper::permissionsFor(['lead', 'opportunity'])]);

    Livewire::test($graph)->assertForbidden();
    Livewire::test($summary)->assertForbidden();
    Livewire::test($list)->assertForbidden();
})->with('report pages');

it('authorizes the report resource with its own permission only', function (string $resource, string $graph, string $summary, string $list, string $permission) {
    CRMHelper::actingAs([]);

    expect($resource::canViewAny())->toBeFalse();

    CRMHelper::actingAs([$permission]);

    expect($resource::canViewAny())->toBeTrue();
})->with('report pages');

it('shows the left menu of the reporting section', function (string $resource, string $graph, string $summary, string $list, string $permission) {
    CRMHelper::actingAs(['view_any_crm_forecast', 'view_any_crm_pipeline::analysis', 'view_any_crm_lead::analysis', 'view_any_crm_activity::analysis']);

    expect(Livewire::test($graph)->instance()->getSubNavigation())->not->toBeEmpty();
})->with('report pages');

describe('view switcher', function () {
    it('shows every view as a tab with the current one active', function (string $resource, string $graph, string $summary, string $list, string $permission, string $graphPage) {
        CRMHelper::actingAs([$permission]);

        $pages = [$graphPage => $graph, 'summary' => $summary, 'list' => $list];

        foreach ($pages as $pageName => $page) {
            $tabs = collect(Livewire::test($page)->instance()->getRecordNavigationTabs());

            expect($tabs)->toHaveCount(count($resource::getReportViews()))
                ->and($tabs->filter(fn ($tab): bool => $tab->isActive())->map(fn ($tab): ?string => $tab->getUrl())->values()->all())
                ->toBe([$resource::getUrl($pageName)]);
        }
    })->with('report pages');

    it('offers the kanban tab only on the forecast', function () {
        CRMHelper::actingAs(['view_any_crm_forecast', 'view_any_crm_pipeline::analysis']);

        expect(collect(Livewire::test(GraphForecast::class)->instance()->getRecordNavigationTabs())->map(fn ($tab): string => $tab->getLabel()))
            ->toContain(__('crm::filament/clusters/reporting/report.views.kanban'))
            ->and(collect(Livewire::test(GraphPipelineAnalysis::class)->instance()->getRecordNavigationTabs())->map(fn ($tab): string => $tab->getLabel()))
            ->not->toContain(__('crm::filament/clusters/reporting/report.views.kanban'));
    });

    it('shows the saved view tabs on the report lists', function (string $list, string $permission, array $views) {
        CRMHelper::actingAs([$permission]);

        expect(array_keys(Livewire::test($list)->instance()->getCachedFavoriteTableViews()))->toContain('default', ...$views);
    })->with([
        'forecast'   => [ListForecast::class, 'view_any_crm_forecast', ['my_pipeline', 'open', 'won', 'lost']],
        'pipeline'   => [ListPipelineAnalysis::class, 'view_any_crm_pipeline::analysis', ['my_pipeline', 'open', 'won', 'lost']],
        'leads'      => [ListLeadAnalysis::class, 'view_any_crm_lead::analysis', ['leads', 'opportunities', 'mine', 'won', 'lost']],
        'activities' => [ListActivityAnalysis::class, 'view_any_crm_activity::analysis', ['mine', 'leads', 'opportunities', 'this_month']],
    ]);

    it('offers the bulk menu on the report lists', function () {
        CRMHelper::actingAs(['view_any_crm_lead::analysis', 'view_any_crm_activity::analysis']);

        $leads = Livewire::test(ListLeadAnalysis::class);

        foreach (['export', 'archiveLeads', 'markLeadsAsLost', 'mergeLeads', 'sendLeadsEmail', 'sendLeadsSMS'] as $name) {
            $leads->assertTableBulkActionExists($name);
        }

        Livewire::test(ListActivityAnalysis::class)->assertTableBulkActionExists('export');
    });
});

describe('graph', function () {
    it('starts with the report defaults and the core table filters', function (string $resource, string $graph, string $summary, string $list, string $permission) {
        CRMHelper::actingAs([$permission]);

        /** @var class-string<Report> $report */
        $report = $resource::getReport();
        $defaults = $report::getDefaultFilters();

        $component = Livewire::test($graph)
            ->assertSet('graphMeasure', $defaults['measure'])
            ->assertSet('graphChartType', $defaults['chart_type']);

        $filters = $component->instance()->getGraphFilters();

        expect($filters['group_by'])->toBe($defaults['group_by'])
            ->and($filters['split_by'])->toBe($defaults['split_by'])
            ->and(array_diff($report::getDefaultPresets(), array_keys($component->instance()->getTable()->getFilters())))->toBe([]);
    })->with('report pages');

    it('offers the report measures and dimensions in the graph controls', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $graph = Livewire::test(GraphPipelineAnalysis::class)->instance()->getGraph();

        expect(array_keys($graph['measures']))->toBe(array_keys(PipelineAnalysisResource::getReport()::getMeasureOptions()))
            ->and(array_keys($graph['dimensions']))->toBe(array_keys(PipelineAnalysisResource::getReport()::getDimensionOptions()))
            ->and($graph['chart']['type'])->toBe(ReportChartType::BAR->value);
    });

    it('switches the measure, split and chart type', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $component = Livewire::test(GraphPipelineAnalysis::class)
            ->set('graphMeasure', 'count')
            ->set('graphSplitBy', '')
            ->set('graphChartType', ReportChartType::PIE->value);

        $graph = $component->instance()->getGraph();

        expect($graph['filters'])->toMatchArray(['measure' => 'count', 'split_by' => null, 'chart_type' => ReportChartType::PIE->value])
            ->and($graph['chart']['type'])->toBe(ReportChartType::PIE->value);
    });

    it('draws the chart from the filtered table records', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        CRMHelper::opportunity(['user_id' => auth()->id()]);

        $component = Livewire::test(GraphPipelineAnalysis::class)->set('graphMeasure', 'count')->set('graphSplitBy', '');

        $total = array_sum($component->instance()->getGraph()['chart']['data']['datasets'][0]['data']);

        $component->call('loadView', 'lost');

        $lost = array_sum($component->instance()->getGraph()['chart']['data']['datasets'][0]['data'] ?? []);

        expect($total)->toBeGreaterThan(0)
            ->and($lost)->toBeLessThan($total);
    });

    it('keeps the chosen measure in the session', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        Livewire::test(GraphPipelineAnalysis::class)->set('graphMeasure', 'count');

        Livewire::test(GraphPipelineAnalysis::class)->assertSet('graphMeasure', 'count');
    });
});

describe('summary', function () {
    it('is a groups-only table grouped by the report default, with one column per measure', function (string $resource, string $graph, string $summary, string $list, string $permission) {
        CRMHelper::actingAs([$permission]);

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
        CRMHelper::actingAs([$permission]);

        $component = Livewire::test($summary)->set('tableGrouping', 'salesperson');

        expect(array_key_first($component->instance()->getTable()->getColumns()))->toBe('user.name');

        $component->set('tableGrouping', 'stage');

        expect(array_key_first($component->instance()->getTable()->getColumns()))->toBe('stage.name');
    })->with('report pages');

    it('shows a row per group', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $stage = Stage::factory()->create(['name' => 'Summary Row Stage']);
        CRMHelper::opportunity(['stage_id' => $stage->id, 'expected_revenue' => 1234]);

        Livewire::test(SummaryPipelineAnalysis::class)
            ->assertOk()
            ->assertSee('Summary Row Stage');
    });
});

describe('list', function () {
    it('turns on the default quick filters', function (string $resource, string $graph, string $summary, string $list, string $permission) {
        CRMHelper::actingAs([$permission]);

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
        CRMHelper::actingAs(['view_any_crm_forecast']);

        $upcoming = CRMHelper::opportunity(['expected_closing_date' => today()->addMonth()]);
        $past = CRMHelper::opportunity(['expected_closing_date' => today()->subMonths(2)->startOfMonth()]);
        $theirs = CRMHelper::opportunity(['expected_closing_date' => today()->addMonth(), 'user_id' => reportPagesUser()->id]);
        $lead = CRMHelper::lead(['expected_closing_date' => today()->addMonth()]);

        Livewire::test(ListForecast::class)
            ->assertCanSeeTableRecords([$upcoming])
            ->assertCanNotSeeTableRecords([$past, $theirs, $lead]);
    });

    it('lists every salesperson once My Pipeline is turned off', function () {
        CRMHelper::actingAs(['view_any_crm_forecast']);

        $theirs = CRMHelper::opportunity(['expected_closing_date' => today()->addMonth(), 'user_id' => reportPagesUser()->id]);

        Livewire::test(ListForecast::class)
            ->filterTable('my_pipeline', false)
            ->assertCanSeeTableRecords([$theirs]);
    });

    it('lists active opportunities on the pipeline analysis', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $active = CRMHelper::opportunity();
        $lost = CRMHelper::opportunity();
        $lost->markAsLost(null);
        $lead = CRMHelper::lead();

        Livewire::test(ListPipelineAnalysis::class)
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$lost, $lead]);
    });

    it('lists leads and opportunities created this year, lost ones included, on the leads analysis', function () {
        CRMHelper::actingAs(['view_any_crm_lead::analysis']);

        $lead = CRMHelper::lead();
        $opportunity = CRMHelper::opportunity();
        $lost = CRMHelper::lead();
        $lost->markAsLost(null);
        $old = CRMHelper::opportunity(['created_at' => now()->subYear()->startOfYear()]);

        Livewire::test(ListLeadAnalysis::class)
            ->assertCanSeeTableRecords([$lead, $opportunity, $lost])
            ->assertCanNotSeeTableRecords([$old]);
    });

    it('lists the activities completed in the last 12 months on the activities analysis', function () {
        CRMHelper::actingAs(['view_any_crm_activity::analysis']);

        $opportunity = CRMHelper::opportunity();

        $recent = reportPagesActivityLog($opportunity, ['done_at' => now()->subMonth()]);
        $old = reportPagesActivityLog($opportunity, ['done_at' => now()->subMonths(13)]);

        Livewire::test(ListActivityAnalysis::class)
            ->assertCanSeeTableRecords([$recent])
            ->assertCanNotSeeTableRecords([$old]);
    });
});

describe('forecast export', function () {
    it('has no export button in the header of any report view', function (string $resource, string $graph, string $summary, string $list, string $permission) {
        CRMHelper::actingAs([$permission]);

        foreach ([$graph, $summary, $list] as $page) {
            Livewire::test($page)->assertActionDoesNotExist('export');
        }
    })->with('report pages');

    it('has no export button on the forecast kanban and exports from the list bulk menu', function () {
        CRMHelper::actingAs(['view_any_crm_forecast']);

        Livewire::test(KanbanForecast::class)->assertActionDoesNotExist('export');
        Livewire::test(ListForecast::class)->assertTableBulkActionExists('export');
    });

    it('exports the prorated revenue and status of an opportunity', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $opportunity = CRMHelper::opportunity(['expected_revenue' => 1000, 'probability' => 25]);

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

it('clears the split and the table filters on the graph', function (string $page, array $permissions) {
    CRMHelper::actingAs($permissions);

    $component = Livewire::test($page)
        ->set('graphSplitBy', '')
        ->call('removeTableFilters');

    expect($component->instance()->getGraphFilters()['split_by'])->toBeNull()
        ->and(collect($component->get('tableFilters'))->filter(fn ($state): bool => (bool) ($state['isActive'] ?? false)))->toBeEmpty();
})->with([
    'pipeline'   => [GraphPipelineAnalysis::class, ['view_any_crm_pipeline::analysis']],
    'leads'      => [GraphLeadAnalysis::class, ['view_any_crm_lead::analysis']],
    'activities' => [GraphActivityAnalysis::class, ['view_any_crm_activity::analysis']],
]);
