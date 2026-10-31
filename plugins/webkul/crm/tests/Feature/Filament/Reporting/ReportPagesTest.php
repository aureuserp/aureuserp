<?php

use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\Select;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource\Pages\ListActivityAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource\Pages\ListForecast;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource\Pages\ListLeadAnalysis;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource\Pages\ListPipelineAnalysis;
use Webkul\Crm\Filament\Exports\ForecastExporter;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\Lead;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function reportPagesDataset(): array
{
    return [
        'forecast'   => [ForecastResource::class, ListForecast::class.'@graph', ListForecast::class.'@list', 'view_any_crm_forecast'],
        'pipeline'   => [PipelineAnalysisResource::class, ListPipelineAnalysis::class.'@graph', ListPipelineAnalysis::class.'@list', 'view_any_crm_pipeline::analysis'],
        'leads'      => [LeadAnalysisResource::class, ListLeadAnalysis::class.'@graph', ListLeadAnalysis::class.'@list', 'view_any_crm_lead::analysis'],
        'activities' => [ActivityAnalysisResource::class, ListActivityAnalysis::class.'@graph', ListActivityAnalysis::class.'@list', 'view_any_crm_activity::analysis'],
    ];
}

function reportPage(string $page): Testable
{
    [$class, $layout] = explode('@', $page) + [1 => null];

    return Livewire::withQueryParams(array_filter(['layout' => $layout]))->test($class);
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

it('renders the graph and list pages with the report permission', function (string $resource, string $graph, string $list, string $permission) {
    CRMHelper::actingAs([$permission]);

    reportPage($graph)->assertOk()->assertSeeHtml('crm-report-graph');
    reportPage($list)->assertOk();
})->with('report pages');

it('forbids the graph and list pages without the report permission', function (string $resource, string $graph, string $list, string $permission) {
    $otherReportPermissions = collect(reportPagesDataset())
        ->pluck(3)
        ->reject(fn (string $other): bool => $other === $permission)
        ->values()
        ->all();

    CRMHelper::actingAs([...$otherReportPermissions, ...CRMHelper::permissionsFor(['lead', 'opportunity'])]);

    reportPage($graph)->assertForbidden();
    reportPage($list)->assertForbidden();
})->with('report pages');

it('authorizes the report resource with its own permission only', function (string $resource, string $graph, string $list, string $permission) {
    CRMHelper::actingAs([]);

    expect($resource::canViewAny())->toBeFalse();

    CRMHelper::actingAs([$permission]);

    expect($resource::canViewAny())->toBeTrue();
})->with('report pages');

it('shows the left menu of the reporting section', function (string $resource, string $graph, string $list, string $permission) {
    CRMHelper::actingAs(['view_any_crm_forecast', 'view_any_crm_pipeline::analysis', 'view_any_crm_lead::analysis', 'view_any_crm_activity::analysis']);

    expect(reportPage($graph)->instance()->getSubNavigation())->not->toBeEmpty();
})->with('report pages');

describe('view switcher', function () {
    it('shows every view in the toolbar switcher with the current one active', function (string $resource, string $graph, string $list, string $permission) {
        CRMHelper::actingAs([$permission]);

        foreach ([$graph, $list] as $page) {
            $component = reportPage($page)->assertSeeHtml('crm-layout-switcher');

            expect($component->instance()->getViewLayouts())->toBe($resource::getReportLayouts())
                ->and($component->instance()->getViewLayout()->value)->toBe(str($page)->after('@')->toString())
                ->and(method_exists($component->instance(), 'getRecordNavigationTabs'))->toBeFalse();
        }
    })->with('report pages');

    it('offers the kanban view only on the forecast', function () {
        expect(ForecastResource::getReportLayouts())->toContain(ViewLayout::KANBAN)
            ->and(PipelineAnalysisResource::getReportLayouts())->not->toContain(ViewLayout::KANBAN);
    });

    it('switches between graph and list without leaving the page', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        reportPage(ListPipelineAnalysis::class.'@list')
            ->call('switchViewLayout', 'graph')
            ->assertSet('viewLayout', 'graph')
            ->assertSeeHtml('crm-report-graph');
    });

    it('shows the saved view tabs on the report lists and opens on the first one', function (string $list, string $permission, array $views) {
        CRMHelper::actingAs([$permission]);

        $component = reportPage($list)->assertSet('activeTableView', $views[0]);

        expect(array_keys($component->instance()->getCachedFavoriteTableViews()))->toContain('default', ...$views);
    })->with([
        'forecast'   => [ListForecast::class, 'view_any_crm_forecast', ['my_pipeline', 'open', 'won', 'lost']],
        'pipeline'   => [ListPipelineAnalysis::class, 'view_any_crm_pipeline::analysis', ['my_pipeline', 'open', 'won', 'lost']],
        'leads'      => [ListLeadAnalysis::class, 'view_any_crm_lead::analysis', ['leads', 'opportunities', 'mine', 'won', 'lost']],
        'activities' => [ListActivityAnalysis::class, 'view_any_crm_activity::analysis', ['mine', 'leads', 'opportunities', 'this_month']],
    ]);

    it('opens the report on the Default tab when it comes filtered to a team', function () {
        CRMHelper::actingAs(['view_any_crm_activity::analysis']);

        $team = Team::factory()->create(['company_id' => CRMHelper::companyId(), 'is_active' => true]);

        Livewire::withQueryParams(['filters' => ['team_ids' => ['values' => [(string) $team->id]]]])
            ->test(ListActivityAnalysis::class)
            ->assertSet('activeTableView', 'default');
    });

    it('offers the bulk menu on the report lists', function () {
        CRMHelper::actingAs(['view_any_crm_lead::analysis', 'view_any_crm_activity::analysis']);

        $leads = reportPage(ListLeadAnalysis::class.'@list');

        foreach (['export', 'delete', 'restore', 'forceDelete', 'markLeadsAsLost', 'mergeLeads', 'sendLeadsEmail', 'sendLeadsSMS'] as $name) {
            $leads->assertTableBulkActionExists($name);
        }

        $leads->assertTableBulkActionDoesNotExist('archiveLeads')
            ->assertTableBulkActionDoesNotExist('duplicateLeads');

        reportPage(ListActivityAnalysis::class.'@list')->assertTableBulkActionExists('export');
    });
});

describe('graph', function () {
    it('starts with the report defaults and the core table filters', function (string $resource, string $graph, string $list, string $permission) {
        CRMHelper::actingAs([$permission]);

        /** @var class-string<Report> $report */
        $report = $resource::getReport();
        $defaults = $report::getDefaultFilters();

        $component = reportPage($graph)
            ->assertSet('graphMeasure', $defaults['measure']);

        $filters = $component->instance()->getGraphFilters();

        expect($filters['group_by'])->toBe($defaults['group_by'])
            ->and($filters['split_by'])->toBe($defaults['split_by'])
            ->and(array_diff($report::getDefaultPresets(), array_keys($component->instance()->getTable()->getFilters())))->toBe([]);
    })->with('report pages');

    it('offers the report measures and dimensions in the graph controls', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $report = PipelineAnalysisResource::getReport();

        $component = Livewire::test(ListPipelineAnalysis::class)
            ->assertFormFieldExists('graphMeasure', 'graph', fn (Select $field): bool => $field->getOptions() === $report::getMeasureOptions() && ! $field->canSelectPlaceholder())
            ->assertFormFieldExists('graphSplitBy', 'graph', fn (Select $field): bool => $field->getOptions() === $report::getDimensionOptions() && $field->canSelectPlaceholder());

        expect($component->instance()->getGraph()['chart']['type'])->toBe('bar');
    });

    it('switches the measure and split and always draws a bar chart', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $component = Livewire::test(ListPipelineAnalysis::class)
            ->set('graphMeasure', 'count')
            ->set('graphSplitBy', '');

        $graph = $component->instance()->getGraph();

        expect($graph['filters'])->toMatchArray(['measure' => 'count', 'split_by' => null])
            ->and($graph['filters'])->not->toHaveKey('chart_type')
            ->and($graph['chart']['type'])->toBe('bar');
    });

    it('draws the chart from the filtered table records', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        CRMHelper::opportunity(['user_id' => auth()->id()]);

        $component = Livewire::test(ListPipelineAnalysis::class)->set('graphMeasure', 'count')->set('graphSplitBy', '');

        $total = array_sum($component->instance()->getGraph()['chart']['data']['datasets'][0]['data']);

        $component->call('loadView', 'lost');

        $lost = array_sum($component->instance()->getGraph()['chart']['data']['datasets'][0]['data'] ?? []);

        expect($total)->toBeGreaterThan(0)
            ->and($lost)->toBeLessThan($total);
    });

    it('keeps the chosen measure in the session', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        Livewire::test(ListPipelineAnalysis::class)->set('graphMeasure', 'count');

        Livewire::test(ListPipelineAnalysis::class)->assertSet('graphMeasure', 'count');
    });
});

describe('list', function () {
    it('turns on the default quick filters', function (string $resource, string $graph, string $list, string $permission) {
        CRMHelper::actingAs([$permission]);

        /** @var class-string<Report> $report */
        $report = $resource::getReport();

        $component = reportPage($list);

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

        reportPage(ListForecast::class.'@list')
            ->assertCanSeeTableRecords([$upcoming])
            ->assertCanNotSeeTableRecords([$past, $theirs, $lead]);
    });

    it('lists every salesperson once My Pipeline is turned off', function () {
        CRMHelper::actingAs(['view_any_crm_forecast']);

        $theirs = CRMHelper::opportunity(['expected_closing_date' => today()->addMonth(), 'user_id' => reportPagesUser()->id]);

        Livewire::withQueryParams(['layout' => 'list', 'activeTableView' => 'default'])->test(ListForecast::class)
            ->filterTable('my_pipeline', false)
            ->assertCanSeeTableRecords([$theirs]);
    });

    it('lists active opportunities on the pipeline analysis', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $active = CRMHelper::opportunity();
        $lost = CRMHelper::opportunity();
        $lost->markAsLost(null);
        $lead = CRMHelper::lead();

        reportPage(ListPipelineAnalysis::class.'@list')
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

        reportPage(ListLeadAnalysis::class.'@list')
            ->assertCanSeeTableRecords([$lead, $lost])
            ->assertCanNotSeeTableRecords([$opportunity, $old]);

        Livewire::withQueryParams(['layout' => 'list', 'activeTableView' => 'default'])->test(ListLeadAnalysis::class)
            ->assertCanSeeTableRecords([$lead, $opportunity, $lost])
            ->assertCanNotSeeTableRecords([$old]);
    });

    it('lists the activities completed in the last 12 months on the activities analysis', function () {
        CRMHelper::actingAs(['view_any_crm_activity::analysis']);

        $opportunity = CRMHelper::opportunity();

        $recent = reportPagesActivityLog($opportunity, ['done_at' => now()->subMonth()]);
        $old = reportPagesActivityLog($opportunity, ['done_at' => now()->subMonths(13)]);

        reportPage(ListActivityAnalysis::class.'@list')
            ->assertCanSeeTableRecords([$recent])
            ->assertCanNotSeeTableRecords([$old]);
    });
});

describe('forecast export', function () {
    it('has no export button in the header of any report view', function (string $resource, string $graph, string $list, string $permission) {
        CRMHelper::actingAs([$permission]);

        foreach ([$graph, $list] as $page) {
            reportPage($page)->assertActionDoesNotExist('export');
        }
    })->with('report pages');

    it('has no export button on the forecast kanban and exports from the list bulk menu', function () {
        CRMHelper::actingAs(['view_any_crm_forecast']);

        Livewire::test(ListForecast::class)->assertActionDoesNotExist('export');
        reportPage(ListForecast::class.'@list')->assertTableBulkActionExists('export');
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
    'pipeline'   => [ListPipelineAnalysis::class, ['view_any_crm_pipeline::analysis']],
    'leads'      => [ListLeadAnalysis::class, ['view_any_crm_lead::analysis']],
    'activities' => [ListActivityAnalysis::class, ['view_any_crm_activity::analysis']],
]);
