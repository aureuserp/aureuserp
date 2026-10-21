<?php

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Leads;
use Webkul\Crm\Filament\Clusters\Reporting;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByStatusChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByTypeChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByUserChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivityStatsOverview;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource as SalesTeamResource;
use Webkul\Crm\Filament\Support\Reports\Widgets\ReportChart;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(function () {
    CrmTestHelper::bootstrap();

    $this->shield = require __DIR__.'/../../../../config/filament-shield.php';
});

/**
 * The policy that authorizes a CRM resource: its `getCrmPolicy()` for resources that reuse
 * another plugin's model, otherwise the policy Laravel resolves for the resource model.
 */
function crmShieldPolicyFor(string $resource): object
{
    if (method_exists($resource, 'getCrmPolicy')) {
        return app($resource::getCrmPolicy());
    }

    $policy = Gate::getPolicyFor($resource::getModel());

    expect($policy)->not->toBeNull("No policy resolved for {$resource}");

    return $policy;
}

it('has a policy method for every ability of every managed crm resource', function () {
    foreach ($this->shield['resources']['manage'] as $resource => $abilities) {
        $policy = crmShieldPolicyFor($resource);

        foreach ($abilities as $ability) {
            $method = Str::camel($ability);

            expect(method_exists($policy, $method))
                ->toBeTrue(sprintf('%s is missing %s() for the "%s" ability of %s', $policy::class, $method, $ability, $resource));
        }
    }
});

it('only lists abilities known to the permission affixes', function () {
    foreach ($this->shield['resources']['manage'] as $resource => $abilities) {
        expect($abilities)->each->toBeIn(CrmTestHelper::ALL_AFFIXES)
            ->and(array_unique($abilities))->toHaveCount(count($abilities));
    }
});

it('grants the crm permission named after each ability', function () {
    foreach ($this->shield['resources']['manage'] as $resource => $abilities) {
        $policy = crmShieldPolicyFor($resource);

        $source = file_get_contents((new ReflectionClass($policy))->getFileName());

        foreach ($abilities as $ability) {
            expect($source)->toMatch(
                "/'{$ability}_crm_[a-z:]+'/",
                sprintf('%s does not check a "%s_crm_*" permission', $policy::class, $ability),
            );
        }
    }
});

it('registers every crm resource under the clusters in the shield config', function () {
    $pluginRoot = dirname(__DIR__, 4);

    $resources = collect(glob($pluginRoot.'/src/Filament/Clusters/*/Resources/*Resource.php'))
        ->map(fn (string $path): string => 'Webkul\\Crm\\Filament\\Clusters\\'.str_replace(
            ['/', '.php'],
            ['\\', ''],
            Str::after($path, $pluginRoot.'/src/Filament/Clusters/'),
        ))
        ->sort()
        ->values();

    expect($resources)->not->toBeEmpty();

    $configured = collect(array_keys($this->shield['resources']['manage']))
        ->merge($this->shield['resources']['exclude']);

    foreach ($resources as $resource) {
        expect(class_exists($resource))->toBeTrue("{$resource} does not exist")
            ->and($configured->contains($resource))->toBeTrue("{$resource} is not listed in config/filament-shield.php");
    }
});

it('keeps the report resources view_any only', function (string $resource) {
    expect($this->shield['resources']['manage'][$resource] ?? null)->toBe(['view_any']);
})->with([
    'forecast'          => [ForecastResource::class],
    'pipeline analysis' => [PipelineAnalysisResource::class],
    'lead analysis'     => [LeadAnalysisResource::class],
    'activity analysis' => [ActivityAnalysisResource::class],
]);

it('excludes the sales teams overview, which shares the crm team permissions', function () {
    expect($this->shield['resources']['exclude'])->toContain(SalesTeamResource::class)
        ->and($this->shield['resources']['manage'])->not->toHaveKey(SalesTeamResource::class);
});

it('excludes the cluster pages from shield', function () {
    expect($this->shield['pages']['exclude'])->toEqualCanonicalizing([
        Sales::class,
        Leads::class,
        Reporting::class,
        Configurations::class,
    ]);
});

it('excludes the activity and report widgets from shield', function () {
    expect($this->shield['widgets']['exclude'])->toEqualCanonicalizing([
        ActivityStatsOverview::class,
        ActivitiesByStatusChart::class,
        ActivitiesByTypeChart::class,
        ActivitiesByUserChart::class,
        ReportChart::class,
    ]);
});
