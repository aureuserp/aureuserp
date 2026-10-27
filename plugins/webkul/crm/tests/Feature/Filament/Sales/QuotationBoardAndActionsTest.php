<?php

use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Webkul\Crm\Enums\QuotationBoardGroup;
use Webkul\Crm\Enums\TeamOrderScope;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\GraphQuotations;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\KanbanQuotations;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ListQuotations;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ViewQuotation;
use Webkul\Product\Models\Product;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';
require_once __DIR__.'/../../../../../sales/tests/Helpers/SaleHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function boardQuotation(array $attributes = []): Order
{
    $uom = SaleHelper::unitsUom();

    $product = Product::factory()->create([
        'uom_id'     => $uom->id,
        'uom_po_id'  => $uom->id,
        'company_id' => SaleHelper::company()->id,
        ...(Schema::hasColumn('products_products', 'is_storable') ? ['is_storable' => true] : []),
    ]);

    $order = SaleHelper::order([
        'state'   => OrderState::DRAFT,
        'user_id' => auth()->id(),
        ...$attributes,
    ]);

    SaleHelper::line($order, $product, qty: 2, priceUnit: 100);

    return $order->refresh();
}

it('shows list, kanban and graph tabs on every quotation view', function (string $page, array $active) {
    CRMHelper::actingAsCRMUser(['quotation']);

    $tabs = collect(Livewire::test($page)->assertOk()->instance()->getRecordNavigationTabs());

    expect($tabs->map(fn ($tab): bool => $tab->isActive())->all())->toBe($active);
})->with([
    'list'   => [ListQuotations::class, [true, false, false]],
    'kanban' => [KanbanQuotations::class, [false, true, false]],
    'graph'  => [GraphQuotations::class, [false, false, true]],
]);

it('shows the board with one column per status on the kanban page', function () {
    CRMHelper::actingAsCRMUser(['quotation']);

    boardQuotation();
    boardQuotation(['state' => OrderState::SENT]);

    $component = Livewire::test(KanbanQuotations::class)->set('tableGrouping', null);

    $columns = collect($component->instance()->getKanbanColumns());

    expect($component->instance()->getBoardGroup())->toBe(QuotationBoardGroup::STATUS)
        ->and($columns->pluck('value')->all())->toContain(OrderState::DRAFT->value, OrderState::SENT->value)
        ->and($component->instance()->getTableRecords())->toHaveCount(0);
});

it('turns a list grouped by salesperson into a board and moves cards between salespeople', function () {
    $user = CRMHelper::actingAsCRMUser(['quotation']);

    $other = User::factory()->create();
    $quotation = boardQuotation(['user_id' => $other->id]);
    boardQuotation(['user_id' => $user->id]);

    $component = Livewire::test(ListQuotations::class)
        ->set('tableGrouping', 'user.name');

    expect($component->instance()->getBoardGroup())->toBe(QuotationBoardGroup::SALESPERSON);

    $component->call('moveRecord', (string) $quotation->id, (string) $user->id, []);

    expect($quotation->refresh()->user_id)->toBe($user->id);
});

it('does not move cards on a status board', function () {
    CRMHelper::actingAsCRMUser(['quotation']);

    $quotation = boardQuotation();

    Livewire::test(KanbanQuotations::class)
        ->call('moveRecord', (string) $quotation->id, OrderState::SALE->value, []);

    expect($quotation->refresh()->state)->toBe(OrderState::DRAFT);
});

it('counts quotations per status in the graph when grouped by status', function () {
    CRMHelper::actingAsCRMUser(['quotation']);

    boardQuotation();

    $chart = Livewire::test(GraphQuotations::class)
        ->set('tableGrouping', QuotationBoardGroup::STATUS->value)
        ->instance()
        ->getRecordsGraph()['data'];

    expect($chart['labels'])->toContain(OrderState::DRAFT->getLabel());
});

it('offers the quotation bulk actions on the list', function () {
    CRMHelper::actingAsCRMUser(['quotation']);

    $component = Livewire::test(ListQuotations::class);

    foreach (['export', 'duplicateQuotations', 'delete', 'sendQuotationsEmail', 'markQuotationsAsSent', 'confirmQuotations'] as $name) {
        $component->assertTableBulkActionExists($name);
    }
});

it('marks only draft quotations as sent in bulk', function () {
    CRMHelper::actingAsCRMUser(['quotation']);

    $draft = boardQuotation();
    $order = boardQuotation(['state' => OrderState::SALE]);

    Livewire::test(ListQuotations::class)
        ->callTableBulkAction('markQuotationsAsSent', [$draft, $order]);

    expect($draft->refresh()->state)->toBe(OrderState::SENT)
        ->and($order->refresh()->state)->toBe(OrderState::SALE);
});

it('duplicates quotations with their lines as new drafts', function () {
    CRMHelper::actingAsCRMUser(['quotation']);

    $quotation = boardQuotation(['state' => OrderState::SENT]);

    Livewire::test(ListQuotations::class)
        ->callTableBulkAction('duplicateQuotations', [$quotation]);

    $copy = Order::query()->whereKeyNot($quotation->id)->latest('id')->firstOrFail();

    expect($copy->state)->toBe(OrderState::DRAFT)
        ->and($copy->name)->not->toBe($quotation->name)
        ->and($copy->lines()->count())->toBe($quotation->lines()->count());
});

it('groups duplicate, mark as sent and delete in the quotation page actions menu', function () {
    CRMHelper::actingAsCRMUser(['quotation']);

    $quotation = boardQuotation();

    Livewire::test(ViewQuotation::class, ['record' => $quotation->id])
        ->assertActionVisible('duplicateQuotation')
        ->assertActionVisible('markQuotationAsSent')
        ->assertActionVisible('delete')
        ->callAction('markQuotationAsSent');

    expect($quotation->refresh()->state)->toBe(OrderState::SENT);
});

it('opens the copy after duplicating from the quotation page', function () {
    CRMHelper::actingAsCRMUser(['quotation']);

    $quotation = boardQuotation();

    $component = Livewire::test(ViewQuotation::class, ['record' => $quotation->id])
        ->callAction('duplicateQuotation');

    $copy = Order::query()->whereKeyNot($quotation->id)->latest('id')->firstOrFail();

    $component->assertRedirect(QuotationResource::getUrl('edit', ['record' => $copy]));
});

it('shows the opportunity as a record tab instead of a header button', function () {
    CRMHelper::actingAsCRMUser(['quotation', 'opportunity']);

    $opportunity = CRMHelper::opportunity();
    $quotation = boardQuotation(['opportunity_id' => $opportunity->id]);

    $component = Livewire::test(ViewQuotation::class, ['record' => $quotation->id])
        ->assertActionDoesNotExist('openOpportunity');

    $tab = collect($component->instance()->getRecordNavigationTabs())
        ->first(fn ($item): bool => $item->getLabel() === __('crm::filament/clusters/sales/resources/quotation.tabs.opportunity'));

    expect($tab?->getUrl())->toBe(OpportunityResource::getUrl('view', ['record' => $opportunity]));
});

it('hides the opportunity tab when the quotation has no opportunity', function () {
    CRMHelper::actingAsCRMUser(['quotation', 'opportunity']);

    $quotation = boardQuotation();

    $labels = collect(Livewire::test(ViewQuotation::class, ['record' => $quotation->id])->instance()->getRecordNavigationTabs())
        ->map(fn ($item): string => $item->getLabel());

    expect($labels)->not->toContain(__('crm::filament/clusters/sales/resources/quotation.tabs.opportunity'));
});

it('limits the quotations to one sales team from the team card links', function () {
    CRMHelper::actingAsCRMUser(['quotation']);

    $team = Team::factory()->create(['company_id' => CRMHelper::companyId()]);
    $otherTeam = Team::factory()->create(['company_id' => CRMHelper::companyId()]);

    $teamQuotation = boardQuotation(['team_id' => $team->id]);
    $teamOrder = boardQuotation(['team_id' => $team->id, 'state' => OrderState::SALE]);
    $otherQuotation = boardQuotation(['team_id' => $otherTeam->id]);

    Livewire::withQueryParams(['activeTableView' => 'default', 'team' => $team->id, 'scope' => TeamOrderScope::QUOTATIONS->value])
        ->test(ListQuotations::class)
        ->assertCanSeeTableRecords([$teamQuotation])
        ->assertCanNotSeeTableRecords([$teamOrder, $otherQuotation]);

    Livewire::withQueryParams(['activeTableView' => 'default', 'team' => $team->id])
        ->test(ListQuotations::class)
        ->assertCanSeeTableRecords([$teamQuotation, $teamOrder])
        ->assertCanNotSeeTableRecords([$otherQuotation]);
});
