<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\GraphOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\KanbanOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Models\Stage;

it('shows kanban, list and graph tabs on every pipeline view', function (string $page, array $active) {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $tabs = collect(Livewire::test($page)->instance()->getRecordNavigationTabs());

    expect($tabs->map(fn ($tab): bool => $tab->isActive())->all())->toBe($active);
})->with([
    'kanban' => [KanbanOpportunities::class, [true, false, false]],
    'list'   => [ListOpportunities::class, [false, true, false]],
    'graph'  => [GraphOpportunities::class, [false, false, true]],
]);

it('counts the opportunities per stage in the graph, in stage order', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $stage = Stage::query()->orderBy('sort')->firstOrFail();

    CRMHelper::opportunity(['stage_id' => $stage->id]);

    $chart = Livewire::test(GraphOpportunities::class)->instance()->getRecordsGraph()['data'];

    $expected = Stage::query()->orderBy('sort')->pluck('name')->intersect($chart['labels'])->values()->all();

    expect($chart['labels'])->toBe($expected)
        ->and(array_combine($chart['labels'], $chart['datasets'][0]['data'])[$stage->name])->toBeGreaterThanOrEqual(1);
});

it('follows the chosen group in the graph', function () {
    $user = CRMHelper::actingAsCRMUser(['opportunity']);

    CRMHelper::opportunity(['user_id' => $user->id]);

    $chart = Livewire::test(GraphOpportunities::class)
        ->set('tableGrouping', 'user.name')
        ->instance()
        ->getRecordsGraph()['data'];

    expect($chart['labels'])->toContain($user->name);
});
