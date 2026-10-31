<?php

use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions\ConfirmAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\CreateQuotation;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\EditQuotation;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ListQuotations;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\ViewQuotation;
use Webkul\Crm\Policies\QuotationPolicy;
use Webkul\Product\Models\Product;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions\ConfirmAction as SalesConfirmAction;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\Quotation;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';
require_once __DIR__.'/../../../../../sales/tests/Helpers/SaleHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function crmQuotation(array $attributes = []): Order
{
    $order = SaleHelper::order([
        'state'   => OrderState::DRAFT,
        'user_id' => auth()->id(),
        ...$attributes,
    ]);

    SaleHelper::line($order, crmQuotationProduct(), qty: 2, priceUnit: 100);

    return $order->refresh();
}

/**
 * A product for quotation lines. `SaleHelper::product()` always sets `is_storable`, a column
 * the Inventory plugin adds, which the CRM test setup doesn't install.
 */
function crmQuotationProduct(): Product
{
    $uom = SaleHelper::unitsUom();

    return Product::factory()->create([
        'uom_id'     => $uom->id,
        'uom_po_id'  => $uom->id,
        'company_id' => SaleHelper::company()->id,
        ...(Schema::hasColumn('products_products', 'is_storable') ? ['is_storable' => true] : []),
    ]);
}

/**
 * The header actions a quotation page builds, read without rendering them.
 *
 * @return array<int, mixed>
 */
function crmQuotationHeaderActions(object $page): array
{
    return (fn (): array => $this->getHeaderActions())->call($page);
}

it('is labelled My Quotations', function () {
    CRMHelper::actingAsCRMUser(['quotation']);

    expect(QuotationResource::getNavigationLabel())->toBe(__('crm::filament/clusters/sales/resources/quotation.navigation.title'))
        ->and(QuotationResource::getNavigationLabel())->toBe('My Quotations');

    Livewire::test(ListQuotations::class)
        ->assertOk()
        ->assertSee('My Quotations');

    expect(Livewire::test(ListQuotations::class)->instance()->getTitle())->toBe('My Quotations');
});

it('lists quotations with the crm quotation permissions', function () {
    CRMHelper::actingAsCRMUser(['quotation']);

    $quotation = crmQuotation();

    Livewire::test(ListQuotations::class)
        ->assertOk()
        ->searchTable($quotation->name)
        ->assertCanSeeTableRecords([$quotation->id]);
});

it('loads the create page with the crm quotation permissions', function () {
    CRMHelper::actingAs(['view_any_crm_quotation', 'create_crm_quotation']);

    Livewire::test(CreateQuotation::class)->assertOk();
});

it('forbids the crm quotation pages without the crm permissions', function (string $page, array $permissions) {
    CRMHelper::actingAs($permissions);

    Livewire::test($page)->assertForbidden();
})->with([
    'list, no permissions'           => [ListQuotations::class, []],
    'list, sales permissions only'   => [ListQuotations::class, ['view_any_sale_quotation', 'view_sale_quotation']],
    'create, no create permission'   => [CreateQuotation::class, ['view_any_crm_quotation']],
    'create, sales permissions only' => [CreateQuotation::class, ['view_any_sale_quotation', 'create_sale_quotation']],
]);

it('forbids editing a quotation with only the sales permissions', function () {
    CRMHelper::actingAs(['view_any_sale_quotation', 'view_sale_quotation', 'update_sale_quotation']);

    $quotation = crmQuotation();

    Livewire::test(EditQuotation::class, ['record' => $quotation->id])->assertForbidden();
});

it('allows viewing a quotation that the order scopes return', function () {
    $user = CRMHelper::actingAs(['view_crm_quotation']);

    $quotation = Quotation::query()->findOrFail(crmQuotation()->id);

    expect((new QuotationPolicy)->view($user, $quotation))->toBeTrue();
});

it('refuses to view a quotation that the order scopes hide', function () {
    $user = CRMHelper::actingAs(['view_crm_quotation']);

    $foreign = crmQuotation(['company_id' => Company::factory()->create()->id]);

    $quotation = Quotation::query()->withoutGlobalScopes()->findOrFail($foreign->id);

    expect(Quotation::query()->whereKey($foreign->id)->exists())->toBeFalse()
        ->and((new QuotationPolicy)->view($user, $quotation))->toBeFalse();
});

it('refuses to view a quotation without the view permission', function () {
    $user = CRMHelper::actingAs(['view_any_crm_quotation']);

    $quotation = Quotation::query()->findOrFail(crmQuotation()->id);

    expect((new QuotationPolicy)->view($user, $quotation))->toBeFalse();
});

it('swaps the sales confirm action for the crm one on the edit and view pages', function (string $page) {
    CRMHelper::actingAsCRMUser(['quotation']);

    $quotation = crmQuotation();

    $component = Livewire::test($page, ['record' => $quotation->id])->assertOk();

    $confirmActions = collect(crmQuotationHeaderActions($component->instance()))
        ->filter(fn ($action): bool => $action instanceof SalesConfirmAction)
        ->values();

    expect($confirmActions)->toHaveCount(1)
        ->and($confirmActions->first())->toBeInstanceOf(ConfirmAction::class);

    $component->assertActionVisible(ConfirmAction::class);
})->with([
    'edit' => [EditQuotation::class],
    'view' => [ViewQuotation::class],
]);

it('confirms a quotation and stays in crm', function () {
    CRMHelper::actingAsCRMUser(['quotation']);

    $quotation = crmQuotation();

    $crmUrl = QuotationResource::getUrl('edit', ['record' => $quotation]);

    Livewire::test(EditQuotation::class, ['record' => $quotation->id])
        ->callAction(ConfirmAction::class)
        ->assertRedirect($crmUrl);

    expect($quotation->refresh()->state)->toBe(OrderState::SALE)
        ->and($crmUrl)->toContain('/crm/sales/quotations/'.$quotation->id.'/edit')
        ->and($crmUrl)->not->toBe(OrderResource::getUrl('edit', ['record' => $quotation]));
});

it('hides confirm once the quotation is a sales order', function () {
    CRMHelper::actingAsCRMUser(['quotation']);

    $quotation = crmQuotation(['state' => OrderState::SALE]);

    Livewire::test(EditQuotation::class, ['record' => $quotation->id])
        ->assertActionHidden(ConfirmAction::class);
});
