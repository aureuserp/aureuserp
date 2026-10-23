<?php

use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Filament\Support\Reports\Widgets\ReportChart;
use Webkul\Crm\Models\Stage;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

/**
 * Two of the current user's opportunities in one stage and one in another, filtered to that user only.
 *
 * @return array{0: Stage, 1: Stage, 2: array<string, mixed>}
 */
function reportChartFixture(): array
{
    $firstStage = Stage::factory()->create(['name' => 'Chart Stage Alpha']);
    $secondStage = Stage::factory()->create(['name' => 'Chart Stage Beta']);

    CrmTestHelper::opportunity(['stage_id' => $firstStage->id]);
    CrmTestHelper::opportunity(['stage_id' => $firstStage->id]);
    CrmTestHelper::lead(['stage_id' => $secondStage->id]);
    CrmTestHelper::opportunity(['stage_id' => $secondStage->id]);

    return [$firstStage, $secondStage, [
        'measure'  => 'count',
        'group_by' => 'stage',
        'presets'  => [],
        'user_ids' => [auth()->id()],
        'team_ids' => [],
    ]];
}

it('renders for each report resource with the page filters', function (string $resource, string $permission) {
    CrmTestHelper::actingAs([$permission]);

    /** @var class-string<Report> $report */
    $report = $resource::getReport();

    Livewire::test(ReportChart::class, [
        'resource'    => $resource,
        'pageFilters' => $report::getDefaultFilters(),
    ])->assertOk();
})->with([
    'forecast'   => [ForecastResource::class, 'view_any_crm_forecast'],
    'pipeline'   => [PipelineAnalysisResource::class, 'view_any_crm_pipeline::analysis'],
    'leads'      => [LeadAnalysisResource::class, 'view_any_crm_lead::analysis'],
    'activities' => [ActivityAnalysisResource::class, 'view_any_crm_activity::analysis'],
]);

it('only accepts the four report resources', function () {
    expect(ReportChart::RESOURCES)->toBe([
        ForecastResource::class,
        PipelineAnalysisResource::class,
        LeadAnalysisResource::class,
        ActivityAnalysisResource::class,
    ]);
});

it('aborts when mounted with a class that is not a report resource', function (string $resource) {
    CrmTestHelper::actingAs([
        'view_any_crm_forecast',
        'view_any_crm_pipeline::analysis',
        'view_any_crm_lead::analysis',
        'view_any_crm_activity::analysis',
        ...CrmTestHelper::permissionsFor(['lead', 'opportunity']),
    ]);

    Livewire::test(ReportChart::class, ['resource' => $resource, 'pageFilters' => []])->assertForbidden();
})->with([
    'leads resource'       => [LeadResource::class],
    'opportunity resource' => [OpportunityResource::class],
    'arbitrary class'      => [stdClass::class],
]);

it('aborts without the report permission', function (string $resource, string $otherPermission) {
    CrmTestHelper::actingAs([$otherPermission]);

    Livewire::test(ReportChart::class, ['resource' => $resource, 'pageFilters' => []])->assertForbidden();
})->with([
    'forecast'   => [ForecastResource::class, 'view_any_crm_pipeline::analysis'],
    'pipeline'   => [PipelineAnalysisResource::class, 'view_any_crm_forecast'],
    'leads'      => [LeadAnalysisResource::class, 'view_any_crm_activity::analysis'],
    'activities' => [ActivityAnalysisResource::class, 'view_any_crm_lead::analysis'],
]);

it('locks the resource property against changes from the browser', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast', 'view_any_crm_pipeline::analysis']);

    $component = Livewire::test(ReportChart::class, ['resource' => ForecastResource::class, 'pageFilters' => []]);

    expect(fn () => $component->set('resource', PipelineAnalysisResource::class))
        ->toThrow(CannotUpdateLockedPropertyException::class);

    expect($component->get('resource'))->toBe(ForecastResource::class);
});

it('re-checks the permission when the data is loaded', function () {
    CrmTestHelper::actingAs(['view_any_crm_pipeline::analysis']);

    $component = Livewire::test(ReportChart::class, ['resource' => PipelineAnalysisResource::class, 'pageFilters' => []]);

    CrmTestHelper::actingAs([]);

    expect(fn () => $component->invade()->getData())
        ->toThrow(HttpException::class);
});

it('draws a bar chart with one dataset per split, stacked', function () {
    CrmTestHelper::actingAs(['view_any_crm_lead::analysis']);

    [$firstStage, $secondStage, $filters] = reportChartFixture();

    $component = Livewire::test(ReportChart::class, [
        'resource'    => LeadAnalysisResource::class,
        'pageFilters' => [...$filters, 'split_by' => 'type', 'chart_type' => Report::CHART_BAR],
    ]);

    $data = $component->invade()->getData();
    $options = $component->invade()->getOptions();

    expect($component->invade()->getType())->toBe('bar')
        ->and($data['labels'])->toBe(['Chart Stage Alpha', 'Chart Stage Beta'])
        ->and($data['datasets'])->toHaveCount(2)
        ->and(collect($data['datasets'])->pluck('label')->all())->toEqualCanonicalizing([
            __('crm::models/lead.title'),
            __('crm::models/opportunity.title'),
        ])
        ->and(collect($data['datasets'])->every(fn (array $dataset): bool => is_string($dataset['backgroundColor']) && count($dataset['data']) === 2))->toBeTrue()
        ->and($options['scales']['x']['stacked'])->toBeTrue()
        ->and($options['scales']['y']['stacked'])->toBeTrue()
        ->and($options['scales']['y']['beginAtZero'])->toBeTrue();

    $byLabel = collect($data['datasets'])->mapWithKeys(fn (array $dataset): array => [$dataset['label'] => $dataset['data']]);

    expect($byLabel[__('crm::models/opportunity.title')])->toBe([2.0, 1.0])
        ->and($byLabel[__('crm::models/lead.title')])->toBe([0.0, 1.0]);
});

it('does not stack a bar chart without a split', function () {
    CrmTestHelper::actingAs(['view_any_crm_lead::analysis']);

    [, , $filters] = reportChartFixture();

    $component = Livewire::test(ReportChart::class, [
        'resource'    => LeadAnalysisResource::class,
        'pageFilters' => [...$filters, 'split_by' => '', 'chart_type' => Report::CHART_BAR],
    ]);

    $data = $component->invade()->getData();
    $options = $component->invade()->getOptions();

    expect($data['datasets'])->toHaveCount(1)
        ->and($data['datasets'][0]['label'])->toBe('Count')
        ->and($data['datasets'][0]['data'])->toBe([2.0, 2.0])
        ->and($options['scales']['x']['stacked'])->toBeFalse()
        ->and($options['scales']['y']['stacked'])->toBeFalse();
});

it('does not stack a line chart even when split', function () {
    CrmTestHelper::actingAs(['view_any_crm_lead::analysis']);

    [, , $filters] = reportChartFixture();

    $component = Livewire::test(ReportChart::class, [
        'resource'    => LeadAnalysisResource::class,
        'pageFilters' => [...$filters, 'split_by' => 'type', 'chart_type' => Report::CHART_LINE],
    ]);

    $options = $component->invade()->getOptions();

    expect($component->invade()->getType())->toBe('line')
        ->and($component->invade()->getData()['datasets'])->toHaveCount(2)
        ->and($options['scales']['x']['stacked'])->toBeFalse();
});

it('draws a pie chart as a single dataset with a color per slice, ignoring the split', function () {
    CrmTestHelper::actingAs(['view_any_crm_lead::analysis']);

    [, , $filters] = reportChartFixture();

    $component = Livewire::test(ReportChart::class, [
        'resource'    => LeadAnalysisResource::class,
        'pageFilters' => [...$filters, 'split_by' => 'type', 'chart_type' => Report::CHART_PIE],
    ]);

    $data = $component->invade()->getData();
    $options = $component->invade()->getOptions();

    expect($component->invade()->getType())->toBe('pie')
        ->and($data['labels'])->toBe(['Chart Stage Alpha', 'Chart Stage Beta'])
        ->and($data['datasets'])->toHaveCount(1)
        ->and($data['datasets'][0]['data'])->toBe([2.0, 2.0])
        ->and($data['datasets'][0]['backgroundColor'])->toBeArray()->toHaveCount(2)
        ->and($options['scales']['x']['display'])->toBeFalse()
        ->and($options['scales']['y']['display'])->toBeFalse();
});

it('falls back to a bar chart for an unknown chart type', function () {
    CrmTestHelper::actingAs(['view_any_crm_forecast']);

    $component = Livewire::test(ReportChart::class, [
        'resource'    => ForecastResource::class,
        'pageFilters' => ['chart_type' => 'radar'],
    ]);

    expect($component->invade()->getType())->toBe('bar');
});

it('uses the report defaults for filters the page does not set', function () {
    CrmTestHelper::actingAs(['view_any_crm_pipeline::analysis']);

    $component = Livewire::test(ReportChart::class, [
        'resource'    => PipelineAnalysisResource::class,
        'pageFilters' => ['measure' => null, 'group_by' => null],
    ]);

    expect($component->instance()->getHeading())->toBe('Expected Revenue by Stage');
});

it('names the chart after the measure and group', function () {
    CrmTestHelper::actingAs(['view_any_crm_activity::analysis']);

    $component = Livewire::test(ReportChart::class, [
        'resource'    => ActivityAnalysisResource::class,
        'pageFilters' => ['group_by' => 'activity_type'],
    ]);

    expect($component->instance()->getHeading())->toBe('Count by Activity Type');
});
