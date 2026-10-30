<?php

use Livewire\Livewire;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ListLeads;
use Webkul\Sale\Models\Team;
use Webkul\Support\Models\UTMMedium;

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

it('offers the medium in the column manager, hidden until toggled on', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $lead = CRMHelper::lead(['utm_medium_id' => UTMMedium::query()->firstOrCreate(['name' => 'Website'])->getKey()]);

    Livewire::test(ListLeads::class)
        ->assertTableColumnHidden('medium.name')
        ->toggleAllTableColumns()
        ->assertTableColumnVisible('medium.name')
        ->assertTableColumnStateSet('medium.name', 'Website', $lead);
});

it('asks for a group by on the kanban until one is chosen', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $component = Livewire::withQueryParams(['layout' => 'kanban'])
        ->test(ListLeads::class)
        ->assertSee(__('crm::filament/kanban.choose-group.heading'));

    expect($component->instance()->needsBoardGroup())->toBeTrue()
        ->and($component->instance()->getTableRecords())->toBeEmpty();
});

it('falls back to the list for an unknown or removed layout', function (string $layout) {
    CRMHelper::actingAsCRMUser(['lead']);

    expect(Livewire::withQueryParams(['layout' => $layout])->test(ListLeads::class)->instance()->getViewLayout())->toBe(ViewLayout::LIST);
})->with(['grid', 'nope']);

it('switches list, kanban and graph from the toolbar icons without reloading the page', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $lead = CRMHelper::lead(['name' => 'Switcher lead']);

    $component = Livewire::test(ListLeads::class)
        ->assertSeeHtml('crm-layout-switcher')
        ->assertSeeHtml('aria-label="'.ViewLayout::KANBAN->getLabel().'"')
        ->call('switchViewLayout', 'kanban')
        ->assertSet('viewLayout', 'kanban');

    expect($component->instance()->getViewLayouts())->toBe([ViewLayout::LIST, ViewLayout::KANBAN, ViewLayout::GRAPH])
        ->and($component->instance()->getTableRecords())->toBeEmpty()
        ->and(method_exists($component->instance(), 'getRecordNavigationTabs'))->toBeFalse();

    $component->call('switchViewLayout', 'graph')
        ->assertSet('viewLayout', 'graph')
        ->assertSee(__('crm::filament/clusters/leads/resources/lead/pages/list-leads.graph.heading.month'));

    $component->call('switchViewLayout', 'list')
        ->assertSet('viewLayout', 'list')
        ->assertCanSeeTableRecords([$lead]);

    $component->call('switchViewLayout', 'grid')
        ->assertSet('viewLayout', 'list');
});

it('heads the graph by its group like the dashboard charts', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $component = Livewire::withQueryParams(['layout' => 'graph'])->test(ListLeads::class);

    expect($component->instance()->getRecordsGraphHeading())->toBe('Leads by Month');

    $component->assertSee('Leads by Month')
        ->set('tableGrouping', 'user.name');

    expect($component->instance()->getRecordsGraphHeading())->toBe('Leads by Salesperson');
});

it('shows every month between the first and last lead with short month names', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    CRMHelper::lead(['created_at' => '2019-01-15 10:00:00']);
    CRMHelper::lead(['created_at' => '2019-03-02 10:00:00']);

    $chart = Livewire::withQueryParams(['layout' => 'graph'])->test(ListLeads::class)->instance()->getRecordsGraph()['data'];
    $counts = array_combine($chart['labels'], $chart['datasets'][0]['data']);

    expect($chart['labels'])->toContain('Jan 2019', 'Feb 2019', 'Mar 2019')
        ->and($counts['Feb 2019'])->toBe(0)
        ->and($counts['Jan 2019'])->toBeGreaterThanOrEqual(1);
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

it('shows the leads as a board with one column per group on the kanban', function () {
    $user = CRMHelper::actingAsCRMUser(['lead']);

    $mine = CRMHelper::lead(['name' => 'Grouped lead mine', 'user_id' => $user->id]);
    $unassigned = CRMHelper::lead(['name' => 'Grouped lead unassigned']);
    $unassigned->forceFill(['user_id' => null])->saveQuietly();

    expect(Livewire::test(ListLeads::class)->set('tableGrouping', 'user.name')->instance()->getKanbanColumns())->toBe([]);

    $component = Livewire::withQueryParams(['layout' => 'kanban'])->test(ListLeads::class)->set('tableGrouping', 'user.name');

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

it('opens on My Leads and shows unassigned leads on the Unassigned and All views', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $unassigned = CRMHelper::lead(['name' => 'Unassigned website lead']);
    $unassigned->forceFill(['user_id' => null])->saveQuietly();

    Livewire::test(ListLeads::class)
        ->assertSet('activeTableView', 'my_leads')
        ->assertCanNotSeeTableRecords([$unassigned])
        ->call('loadView', 'unassigned')
        ->assertCanSeeTableRecords([$unassigned])
        ->call('loadView', 'all')
        ->assertCanSeeTableRecords([$unassigned]);
});

it('opens the All Leads view for a sales team link', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    Livewire::withQueryParams(['team' => Team::factory()->create(['company_id' => CRMHelper::companyId()])->id])
        ->test(ListLeads::class)
        ->assertSet('activeTableView', 'all');
});
