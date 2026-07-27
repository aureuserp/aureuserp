<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Purchase\Enums\OrderInvoiceStatus;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Actions\ConfirmAction;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Actions\CreateBillAction;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages\CreatePurchaseOrder;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages\ViewPurchaseOrder;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource\Pages\EditQuotation;
use Webkul\Purchase\Models\Order;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/PurchaseHelper.php';

beforeEach(function () {
    foreach (['inventories', 'purchases', 'accounts'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);

        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

function purchaseOrderRecord(?int $userId = null): Order
{
    $order = PurchaseHelper::order([
        'state'          => OrderState::PURCHASE,
        'invoice_status' => OrderInvoiceStatus::TO_INVOICED,
        'user_id'        => $userId,
    ]);

    return $order->refresh();
}

it('forbids listing purchase orders without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListPurchaseOrders::class)->assertForbidden();
});

it('lists purchase orders with their key columns for authorized users', function () {
    $user = FilamentHelper::actingAs(['view_any_purchase_purchase::order']);

    $order = purchaseOrderRecord($user->id);

    Livewire::test(ListPurchaseOrders::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$order])
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('partner.name')
        ->assertCanRenderTableColumn('state');
});

it('finds a purchase order by searching its number', function () {
    $user = FilamentHelper::actingAs(['view_any_purchase_purchase::order']);

    $order = purchaseOrderRecord($user->id);

    Livewire::test(ListPurchaseOrders::class)
        ->searchTable($order->name)
        ->assertCanSeeTableRecords([$order]);
});

it('confirms a draft RFQ into a purchase order through the action', function () {
    $user = FilamentHelper::actingAs(['view_any_purchase_quotation', 'update_purchase_quotation']);

    $warehouse = InventoryHelper::warehouse();
    $product = PurchaseHelper::product();

    $order = PurchaseHelper::order([
        'state'             => OrderState::DRAFT,
        'operation_type_id' => $warehouse->in_type_id,
        'user_id'           => $user->id,
    ]);
    PurchaseHelper::line($order, $product, qty: 2, priceUnit: 100);

    Livewire::test(EditQuotation::class, ['record' => $order->id])
        ->assertOk()
        ->callAction(ConfirmAction::class);

    expect($order->refresh()->state)->toBe(OrderState::PURCHASE);
});

it('renders the purchase order create page', function () {
    FilamentHelper::actingAs(['view_any_purchase_purchase::order', 'create_purchase_purchase::order']);

    Livewire::test(CreatePurchaseOrder::class)->assertOk();
});

it('creates a purchase order through the create form', function () {
    FilamentHelper::actingAs(['view_any_purchase_purchase::order', 'create_purchase_purchase::order']);

    $warehouse = InventoryHelper::warehouse();
    $partner = PurchaseHelper::partner();

    Livewire::test(CreatePurchaseOrder::class)
        ->fillForm([
            'partner_id'        => $partner->id,
            'ordered_at'        => now(),
            'operation_type_id' => $warehouse->in_type_id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Order::query()->where('partner_id', $partner->id)->exists())->toBeTrue();
});

it('exposes the create-bill action on a confirmed purchase order', function () {
    $user = FilamentHelper::actingAs(['view_any_purchase_purchase::order', 'view_purchase_purchase::order']);

    $order = purchaseOrderRecord($user->id);

    Livewire::test(ViewPurchaseOrder::class, ['record' => $order->id])
        ->assertOk()
        ->assertActionExists(CreateBillAction::class);
});
