<?php

use Filament\Tables\Columns\Layout\Stack;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ListLeads;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

it('opens the leads list as the index page without a kanban board', function () {
    expect(LeadResource::getPages()['index']->getPage())->toBe(ListLeads::class)
        ->and(LeadResource::getPages())->not->toHaveKey('list');
});

it('forbids the leads list without the lead permission', function () {
    CrmTestHelper::actingAs([]);

    Livewire::test(ListLeads::class)->assertForbidden();
});

it('shows lead, email, city, country, salesperson and sales team in the list', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $lead = CrmTestHelper::lead(['name' => 'List layout lead', 'city' => 'Pune']);

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
    CrmTestHelper::actingAsCrmUser(['lead']);

    $lead = CrmTestHelper::lead(['name' => 'Grid layout lead']);

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
    CrmTestHelper::actingAsCrmUser(['lead']);

    Livewire::withQueryParams(['layout' => 'kanban'])
        ->test(ListLeads::class)
        ->assertSet('viewLayout', 'list');
});

it('shows list and grid switches that highlight the current layout', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    Livewire::test(ListLeads::class)
        ->assertActionExists('listLayout')
        ->assertActionExists('gridLayout')
        ->assertActionHasUrl('listLayout', LeadResource::getUrl('index'))
        ->assertActionHasUrl('gridLayout', LeadResource::getUrl('index', ['layout' => 'grid']))
        ->assertActionDoesNotExist('kanbanView');

    expect(collect(LeadResource::getViewSwitcherActions('grid'))->mapWithKeys(fn ($action): array => [$action->getName() => $action->getColor()])->all())
        ->toBe(['listLayout' => 'gray', 'gridLayout' => 'primary']);
});

it('shows unassigned leads in the default view', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $unassigned = CrmTestHelper::lead(['name' => 'Unassigned website lead']);
    $unassigned->forceFill(['user_id' => null])->saveQuietly();

    Livewire::test(ListLeads::class)->assertCanSeeTableRecords([$unassigned]);
});
