<?php

use Filament\Tables\Columns\Layout\Stack;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ListLeads;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

it('opens the leads list as the index page without a kanban board', function () {
    expect(LeadResource::getPages()['index']->getPage())->toBe(ListLeads::class)
        ->and(LeadResource::getPages())->not->toHaveKey('list');
});

it('forbids the leads list without the lead permission', function () {
    CRMHelper::actingAs([]);

    Livewire::test(ListLeads::class)->assertForbidden();
});

it('shows lead, email, city, country, salesperson and sales team in the list', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $lead = CRMHelper::lead(['name' => 'List layout lead', 'city' => 'Pune']);

    Livewire::test(ListLeads::class)
        ->assertSet('viewLayout', 'list')
        ->assertCanSeeTableRecords([$lead])
        ->assertTableColumnVisible('name')
        ->assertTableColumnVisible('email')
        ->assertTableColumnVisible('city')
        ->assertTableColumnVisible('country.name')
        ->assertTableColumnVisible('user.name')
        ->assertTableColumnVisible('team.name')
        ->assertTableColumnHidden('contact_name');
});

it('shows the same leads as cards in the grid layout', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $lead = CRMHelper::lead(['name' => 'Grid layout lead']);

    $component = Livewire::withQueryParams(['layout' => 'grid'])
        ->test(ListLeads::class)
        ->assertSet('viewLayout', 'grid')
        ->assertCanSeeTableRecords([$lead])
        ->assertSee('Grid layout lead');

    $table = $component->instance()->getTable();

    expect($table->getContentGrid())->not->toBeNull()
        ->and($table->getColumnsLayout()[0])->toBeInstanceOf(Stack::class);
});

it('falls back to the list for an unknown layout', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    Livewire::withQueryParams(['layout' => 'kanban'])
        ->test(ListLeads::class)
        ->assertSet('viewLayout', 'list');
});

it('shows list and grid tabs that highlight the current layout', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $tabs = collect(Livewire::withQueryParams(['layout' => 'grid'])->test(ListLeads::class)->instance()->getRecordNavigationTabs());

    expect($tabs->map(fn ($tab): string => $tab->getUrl())->all())->toBe([
        LeadResource::getUrl('index'),
        LeadResource::getUrl('index', ['layout' => 'grid']),
        LeadResource::getUrl('index', ['layout' => 'graph']),
    ])
        ->and($tabs->map(fn ($tab): bool => $tab->isActive())->all())->toBe([false, true, false]);
});

it('counts the leads per group in the graph layout', function () {
    $user = CRMHelper::actingAsCRMUser(['lead']);

    CRMHelper::lead(['name' => 'Graph lead mine', 'user_id' => $user->id]);
    $unassigned = CRMHelper::lead(['name' => 'Graph lead unassigned']);
    $unassigned->forceFill(['user_id' => null])->saveQuietly();

    $component = Livewire::withQueryParams(['layout' => 'graph'])
        ->test(ListLeads::class)
        ->set('tableGrouping', 'user.name');

    $chart = $component->instance()->getRecordsGraph()['data'];
    $counts = array_combine($chart['labels'], $chart['datasets'][0]['data']);

    expect($counts[$user->name] ?? 0)->toBeGreaterThanOrEqual(1)
        ->and($counts[__('crm::filament/clusters/reporting/report.none')] ?? 0)->toBeGreaterThanOrEqual(1)
        ->and($component->instance()->getKanbanColumns())->toBe([]);
});

it('shows the leads as a board with one column per group when grouped', function () {
    $user = CRMHelper::actingAsCRMUser(['lead']);

    $mine = CRMHelper::lead(['name' => 'Grouped lead mine', 'user_id' => $user->id]);
    $unassigned = CRMHelper::lead(['name' => 'Grouped lead unassigned']);
    $unassigned->forceFill(['user_id' => null])->saveQuietly();

    $component = Livewire::test(ListLeads::class)->set('tableGrouping', 'user.name');

    $columns = collect($component->instance()->getKanbanColumns())->keyBy('value');

    expect($columns->get('none')?->records->modelKeys())->toContain($unassigned->id)
        ->and($columns->get((string) $user->id)?->records->modelKeys())->toContain($mine->id);

    $component->call('moveRecord', (string) $unassigned->id, (string) $user->id, []);

    expect($unassigned->fresh()->user_id)->toBe($user->id);
});

it('keeps the list when no group is chosen', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $lead = CRMHelper::lead(['name' => 'Plain list lead']);

    Livewire::test(ListLeads::class)->assertCanSeeTableRecords([$lead]);
});

it('shows unassigned leads in the default view', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $unassigned = CRMHelper::lead(['name' => 'Unassigned website lead']);
    $unassigned->forceFill(['user_id' => null])->saveQuietly();

    Livewire::test(ListLeads::class)->assertCanSeeTableRecords([$unassigned]);
});
