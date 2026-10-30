<?php

use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Events\MoveConfirmed;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Events\OperationBackOrdered;
use Webkul\Inventory\Events\OperationDone;
use Webkul\Inventory\Events\OperationReturned;
use Webkul\Inventory\Facades\Inventory;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Purchase\Enums\OrderReceiptStatus;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Events\OrderConfirmed;
use Webkul\Purchase\Facades\PurchaseOrder as PurchaseOrderFacade;
use Webkul\Purchase\Settings\OrderSettings;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';
require_once __DIR__.'/../../Helpers/PurchaseHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('purchases');

    foreach (['inventories', 'purchases'] as $plugin) {
        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    $routes = new RouteCollection;

    foreach (Route::getRoutes() as $route) {
        if (! str_starts_with((string) $route->getName(), 'filament.')) {
            $routes->add($route);
        }
    }

    Route::setRoutes($routes);

    PurchaseHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->product = PurchaseHelper::product();
});

function recordEvents(array $events): ArrayObject
{
    $dispatched = new ArrayObject;

    foreach ($events as $event) {
        Event::listen($event, fn ($payload) => $dispatched[] = $payload);
    }

    return $dispatched;
}

function dispatchedOf(ArrayObject $dispatched, string $event): array
{
    return array_values(array_filter($dispatched->getArrayCopy(), fn ($payload) => $payload instanceof $event));
}

function messageBodies($record): string
{
    return $record->messages()->pluck('body')->implode(' ');
}

it('confirms a purchase order and plans its receipt without panel routes', function () {
    $events = recordEvents([OrderConfirmed::class]);

    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    $receipt = $order->operations->first();

    expect($order->state)->toBe(OrderState::PURCHASE)
        ->and($receipt)->not->toBeNull()
        ->and((float) $receipt->moves->sum('product_uom_qty'))->toBe(10.0)
        ->and(messageBodies($receipt))->toContain($order->name);

    expect(dispatchedOf($events, OrderConfirmed::class))->toHaveCount(1)
        ->and(dispatchedOf($events, OrderConfirmed::class)[0]->order->is($order))->toBeTrue();
});

it('receives part of a purchase order and creates the backorder without panel routes', function () {
    $events = recordEvents([OperationDone::class, OperationBackOrdered::class]);

    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    $receipt = PurchaseHelper::partialReceive($order, 6);

    $backorder = InventoryHelper::backorderOf($receipt);

    expect($receipt->state)->toBe(OperationState::DONE)
        ->and($order->refresh()->receipt_status)->toBe(OrderReceiptStatus::PARTIAL)
        ->and($backorder)->not->toBeNull()
        ->and(messageBodies($backorder))->toContain($receipt->name)
        ->and(messageBodies($receipt))->toContain($backorder->name);

    expect(dispatchedOf($events, OperationDone::class))->not->toBeEmpty()
        ->and(dispatchedOf($events, OperationBackOrdered::class))->toHaveCount(1);
});

it('returns a received purchase without panel routes', function () {
    $events = recordEvents([OperationReturned::class]);

    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    $receipt = $order->operations->first();

    Inventory::completeTransfer($receipt->refresh());

    $return = Inventory::createReturn($receipt->refresh(), [$receipt->moves->first()->id => 4]);

    expect($return)->not->toBeNull()
        ->and((float) $return->moves->sum('product_uom_qty'))->toBe(4.0)
        ->and(messageBodies($return))->toContain($receipt->name)
        ->and(messageBodies($receipt->refresh()))->toContain($return->name);

    expect(dispatchedOf($events, OperationReturned::class))->toHaveCount(1);
});

it('bills a received purchase order without panel routes', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    Inventory::completeTransfer($order->operations->first()->refresh());

    $bill = PurchaseHelper::createBill($order->refresh());

    expect($bill->partner_id)->toBe($order->partner_id)
        ->and((float) $bill->invoiceLines->sum('quantity'))->toBe(10.0);
});

it('confirms, receives, bills and posts a taxed purchase order with no authenticated user', function () {
    $tax = AccountHelper::taxWithAccounts(15, type: TypeTaxUse::PURCHASE);

    $order = PurchaseHelper::order(['operation_type_id' => $this->warehouse->in_type_id]);

    PurchaseHelper::line($order, $this->product, qty: 3, priceUnit: 100, taxes: [$tax]);

    Auth::logout();

    app()->forgetInstance(CompanyContext::class);

    $events = recordEvents([OrderConfirmed::class, OperationDone::class, MoveConfirmed::class]);

    $order = PurchaseOrderFacade::confirmPurchaseOrder($order->refresh());

    Inventory::completeTransfer($order->operations()->first()->refresh());

    $bill = PurchaseHelper::postBill(PurchaseHelper::createBill($order->refresh()));

    expect(Auth::check())->toBeFalse()
        ->and($order->refresh()->state)->toBe(OrderState::PURCHASE)
        ->and($order->receipt_status)->toBe(OrderReceiptStatus::FULL)
        ->and($bill->state)->toBe(MoveState::POSTED)
        ->and((float) $bill->amount_total)->toBe(345.0)
        ->and(dispatchedOf($events, OrderConfirmed::class))->toHaveCount(1)
        ->and(dispatchedOf($events, OperationDone::class))->not->toBeEmpty()
        ->and(dispatchedOf($events, MoveConfirmed::class))->toHaveCount(1);
});

it('leaves an order above the approval amount waiting for approval when no user is acting', function () {
    $settings = app(OrderSettings::class);
    $settings->enable_order_approval = true;
    $settings->order_validation_amount = 100;
    $settings->save();

    $order = PurchaseHelper::order(['operation_type_id' => $this->warehouse->in_type_id]);

    PurchaseHelper::line($order, $this->product, qty: 3, priceUnit: 100);

    PurchaseHelper::compute($order->refresh());

    Auth::logout();

    $order = PurchaseOrderFacade::confirmPurchaseOrder($order->refresh());

    expect($order->state)->toBe(OrderState::TO_APPROVE)
        ->and($order->operations()->exists())->toBeFalse();
});
