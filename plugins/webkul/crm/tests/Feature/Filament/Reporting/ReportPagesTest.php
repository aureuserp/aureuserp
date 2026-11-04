<?php

use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\Select;
use Filament\Tables\Grouping\Group;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Webkul\Crm\Enums\LeadType;
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
use Webkul\Crm\Filament\Support\LeadTableFilters;
use Webkul\Crm\Filament\Support\Reports\ForecastReport;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\Stage;
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

function reportPagesRuleTypes(Testable $component): array
{
    return collect($component->get('tableFilters')['queryBuilder']['rules'] ?? [])->pluck('type')->values()->all();
}

function reportPagesRule(string $type, string $operator, array $settings = []): array
{
    return ['type' => $type, 'data' => ['operator' => $operator, 'settings' => $settings]];
}

function reportPagesWithRules(string $page, array $rules): Testable
{
    return Livewire::withQueryParams(['layout' => 'list', 'activeTableView' => 'default'])->test($page)
        ->filterTable('queryBuilder', ['rules' => collect(array_values($rules))->mapWithKeys(fn (array $rule, int $index): array => ["rule-{$index}" => $rule])->all()]);
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

    it('opens the analysis reports on the list with the graph last, and the forecast on the kanban with the graph last', function () {
        CRMHelper::actingAs(['view_any_crm_forecast', 'view_any_crm_pipeline::analysis', 'view_any_crm_lead::analysis', 'view_any_crm_activity::analysis']);

        foreach ([ListPipelineAnalysis::class, ListLeadAnalysis::class, ListActivityAnalysis::class] as $page) {
            $component = Livewire::test($page);

            expect($component->instance()->getViewLayouts())->toBe([ViewLayout::LIST, ViewLayout::GRAPH])
                ->and($component->instance()->getViewLayout())->toBe(ViewLayout::LIST);
        }

        $forecast = Livewire::test(ListForecast::class);

        expect($forecast->instance()->getViewLayouts())->toBe([ViewLayout::KANBAN, ViewLayout::LIST, ViewLayout::GRAPH])
            ->and($forecast->instance()->getViewLayout())->toBe(ViewLayout::KANBAN);
    });

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
            ->and(reportPagesRuleTypes($component))->toBe(array_column($report::getDefaultRules(), 'type'));
    })->with('report pages');

    it('offers measure and split by as prefixed toolbar selects after the group by on the graph', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $report = PipelineAnalysisResource::getReport();

        $component = reportPage(ListPipelineAnalysis::class.'@graph')
            ->assertSeeHtml('crm-report-measures')
            ->assertFormFieldExists('graphMeasure', 'reportMeasures', fn (Select $field): bool => $field->getOptions() === $report::getMeasureOptions()
                && $field->getPrefixLabel() === __('crm::filament/clusters/reporting/report.filters.measure')
                && ! $field->canSelectPlaceholder())
            ->assertFormFieldExists('graphSplitBy', 'reportMeasures', fn (Select $field): bool => $field->getOptions() === $report::getDimensionOptions()
                && $field->getPrefixLabel() === __('crm::filament/clusters/reporting/report.filters.split-by'))
            ->assertFormFieldVisible('graphSplitBy', 'reportMeasures');

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
    it('offers the measure, not the split, on the list and adds the measure as a column with totals', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $opportunity = CRMHelper::opportunity(['expected_revenue' => 1000, 'probability' => 40, 'is_automated_probability' => false]);

        $component = reportPage(ListPipelineAnalysis::class.'@list')
            ->set('graphMeasure', 'probability')
            ->assertFormFieldVisible('graphMeasure', 'reportMeasures')
            ->assertFormFieldHidden('graphSplitBy', 'reportMeasures')
            ->assertTableColumnExists('report_measure')
            ->assertTableColumnFormattedStateSet('report_measure', '40%', $opportunity);

        $column = $component->instance()->getTable()->getColumn('report_measure');

        expect($column->getLabel())->toBe(PipelineAnalysisResource::getReport()::measures()['probability']->label)
            ->and($column->getSummarizers())->not->toBeEmpty();

        $component->set('graphMeasure', 'count');

        expect($component->instance()->getTable()->getColumn('report_measure')->getLabel())->toBe(PipelineAnalysisResource::getReport()::measures()['count']->label);
    });

    it('offers every report dimension as a collapsible group by, with no group by default', function (string $resource, string $graph, string $list, string $permission) {
        CRMHelper::actingAs([$permission]);

        /** @var class-string<Report> $report */
        $report = $resource::getReport();

        $component = reportPage($list);
        $table = $component->instance()->getTable();

        expect(array_keys($table->getGroups()))->toBe(array_keys($report::dimensions()))
            ->and($table->getDefaultGroup())->toBeNull()
            ->and(collect($table->getGroups())->every(fn (Group $group): bool => $group->isCollapsible() && $group->isTitlePrefixedWithLabel()))->toBeTrue();

        foreach (array_keys($report::dimensions()) as $dimension) {
            $component->set('tableGrouping', $dimension)->assertOk();
        }
    })->with('report pages');

    it('groups the pipeline list by stage with the core group headings', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $opportunity = CRMHelper::opportunity();

        reportPage(ListPipelineAnalysis::class.'@list')
            ->set('tableGrouping', 'stage')
            ->assertCanSeeTableRecords([$opportunity])
            ->assertSee(__('crm::filament/clusters/reporting/report.dimensions.stage').': '.$opportunity->stage->name);
    });

    it('has no quick filter toggles and pre-adds the default rules of the report', function (string $resource, string $graph, string $list, string $permission) {
        CRMHelper::actingAs([$permission]);

        /** @var class-string<Report> $report */
        $report = $resource::getReport();

        $component = reportPage($list);

        expect(array_keys($component->instance()->getTable()->getFilters()))->toBe(['user_ids', 'team_ids', 'queryBuilder'])
            ->and(reportPagesRuleTypes($component))->toBe(array_column($report::getDefaultRules(), 'type'));
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

    it('lists every salesperson once the salesperson rule is removed', function () {
        CRMHelper::actingAs(['view_any_crm_forecast']);

        $theirs = CRMHelper::opportunity(['expected_closing_date' => today()->addMonth(), 'user_id' => reportPagesUser()->id]);

        reportPagesWithRules(ListForecast::class, collect(ForecastReport::getDefaultRules())->reject(fn (array $rule): bool => $rule['type'] === 'user')->all())
            ->assertCanSeeTableRecords([$theirs]);
    });

    it('rebuilds the former quick filters of the pipeline analysis as rules', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $open = Stage::factory()->create(['is_won' => false]);

        $mine = CRMHelper::opportunity(['stage_id' => $open->id, 'expected_closing_date' => today()->addMonth()]);
        $theirs = CRMHelper::opportunity(['stage_id' => $open->id, 'expected_closing_date' => today()->subMonths(2), 'user_id' => reportPagesUser()->id]);
        $unassigned = CRMHelper::opportunity(['stage_id' => $open->id]);
        Lead::query()->whereKey($unassigned->id)->update(['user_id' => null]);
        $won = CRMHelper::opportunity(['stage_id' => CRMHelper::wonStage()->id]);
        $lost = CRMHelper::opportunity(['stage_id' => $open->id]);
        $lost->markAsLost(null);
        $old = CRMHelper::opportunity(['stage_id' => $open->id, 'created_at' => now()->subYear()->startOfYear()]);
        $closed = CRMHelper::opportunity(['stage_id' => $open->id]);
        Lead::query()->whereKey($closed->id)->update(['closed_at' => now()]);

        $active = reportPagesRule('active', 'isActive');

        reportPagesWithRules(ListPipelineAnalysis::class, [LeadTableFilters::salespersonRule(auth()->id())])
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$theirs, $unassigned]);

        reportPagesWithRules(ListPipelineAnalysis::class, [reportPagesRule('user', 'isEmpty')])
            ->assertCanSeeTableRecords([$unassigned])
            ->assertCanNotSeeTableRecords([$mine, $theirs]);

        reportPagesWithRules(ListPipelineAnalysis::class, [LeadTableFilters::relativeDateRule('expected_closing_date', 'this_month')])
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$theirs]);

        reportPagesWithRules(ListPipelineAnalysis::class, [$active, reportPagesRule('won', 'isWon')])
            ->assertCanSeeTableRecords([$won])
            ->assertCanNotSeeTableRecords([$mine, $lost]);

        reportPagesWithRules(ListPipelineAnalysis::class, [$active, reportPagesRule('won', 'isWon.inverse')])
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$won, $lost]);

        reportPagesWithRules(ListPipelineAnalysis::class, [reportPagesRule('active', 'isActive.inverse')])
            ->assertCanSeeTableRecords([$lost])
            ->assertCanNotSeeTableRecords([$mine]);

        reportPagesWithRules(ListPipelineAnalysis::class, [LeadTableFilters::relativeDateRule('created_at', 'this_year')])
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$old]);

        reportPagesWithRules(ListPipelineAnalysis::class, [LeadTableFilters::relativeDateRule('closed_at', 'this_year')])
            ->assertCanSeeTableRecords([$closed])
            ->assertCanNotSeeTableRecords([$mine]);
    });

    it('rebuilds the leads and opportunities quick filters of the leads analysis as a type rule', function () {
        CRMHelper::actingAs(['view_any_crm_lead::analysis']);

        $lead = CRMHelper::lead();
        $opportunity = CRMHelper::opportunity();

        reportPagesWithRules(ListLeadAnalysis::class, [reportPagesRule('type', 'is', ['values' => [LeadType::LEAD->value]])])
            ->assertCanSeeTableRecords([$lead])
            ->assertCanNotSeeTableRecords([$opportunity]);

        reportPagesWithRules(ListLeadAnalysis::class, [reportPagesRule('type', 'is', ['values' => [LeadType::OPPORTUNITY->value]])])
            ->assertCanSeeTableRecords([$opportunity])
            ->assertCanNotSeeTableRecords([$lead]);
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

    it('inverts or removes the Active rule on the pipeline analysis, and reset puts it back', function () {
        CRMHelper::actingAs(['view_any_crm_pipeline::analysis']);

        $active = CRMHelper::opportunity();
        $lost = CRMHelper::opportunity();
        $lost->markAsLost(null);

        Livewire::withQueryParams(['layout' => 'list', 'activeTableView' => 'default'])->test(ListPipelineAnalysis::class)
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$lost])
            ->filterTable('queryBuilder', ['rules' => ['active' =>['type' => 'active', 'data' => ['operator' => 'isActive.inverse', 'settings' => []]]]])
            ->assertCanSeeTableRecords([$lost])
            ->assertCanNotSeeTableRecords([$active])
            ->filterTable('queryBuilder', ['rules' => []])
            ->assertCanSeeTableRecords([$active, $lost])
            ->resetTableFilters()
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$lost]);
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
        ->and($component->get('tableFilters')['queryBuilder']['rules'] ?? [])->toBeEmpty();
})->with([
    'pipeline'   => [ListPipelineAnalysis::class, ['view_any_crm_pipeline::analysis']],
    'leads'      => [ListLeadAnalysis::class, ['view_any_crm_lead::analysis']],
    'activities' => [ListActivityAnalysis::class, ['view_any_crm_activity::analysis']],
]);
