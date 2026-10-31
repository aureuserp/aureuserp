<?php

use Livewire\Livewire;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Models\Stage;

it('switches the pipeline between kanban, list and graph without leaving the page', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $opportunity = CRMHelper::opportunity();

    $component = Livewire::test(ListOpportunities::class)
        ->assertSet('viewLayout', null)
        ->assertSeeHtml('crm-layout-switcher');

    expect($component->instance()->getViewLayouts())->toBe([ViewLayout::KANBAN, ViewLayout::LIST, ViewLayout::GRAPH])
        ->and($component->instance()->getViewLayout())->toBe(ViewLayout::KANBAN)
        ->and(method_exists($component->instance(), 'getRecordNavigationTabs'))->toBeFalse();

    $component->call('switchViewLayout', 'list')
        ->assertSet('viewLayout', 'list')
        ->assertCanSeeTableRecords([$opportunity]);

    $component->call('switchViewLayout', 'graph')
        ->assertSet('viewLayout', 'graph');

    expect($component->instance()->getTableRecords())->toBeEmpty();
});

it('counts the opportunities per stage in the graph, in stage order', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $stage = Stage::query()->orderBy('sort')->firstOrFail();

    CRMHelper::opportunity(['stage_id' => $stage->id]);

    $chart = Livewire::withQueryParams(['layout' => 'graph'])->test(ListOpportunities::class)->instance()->getRecordsGraph()['data'];

    $expected = Stage::query()->orderBy('sort')->pluck('name')->intersect($chart['labels'])->values()->all();

    expect($chart['labels'])->toBe($expected)
        ->and(array_combine($chart['labels'], $chart['datasets'][0]['data'])[$stage->name])->toBeGreaterThanOrEqual(1);
});

it('follows the chosen group in the graph', function () {
    $user = CRMHelper::actingAsCRMUser(['opportunity']);

    CRMHelper::opportunity(['user_id' => $user->id]);

    $chart = Livewire::withQueryParams(['layout' => 'graph'])->test(ListOpportunities::class)
        ->set('tableGrouping', 'user.name')
        ->instance()
        ->getRecordsGraph()['data'];

    expect($chart['labels'])->toContain($user->name);
});
