<?php

use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
use Webkul\Purchase\Exceptions\OrderApprovalException;
use Webkul\Purchase\Facades\PurchaseOrder as PurchaseOrderFacade;
use Webkul\Security\Enums\PermissionType;
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

    $this->routesWithPanel = Route::getRoutes();

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

it('confirms a purchase order and plans its receipt without panel routes', function () {
    $events = PurchaseHelper::recordEvents([OrderConfirmed::class]);

    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    $receipt = $order->operations->first();

    expect($order->state)->toBe(OrderState::PURCHASE)
        ->and($receipt)->not->toBeNull()
        ->and((float) $receipt->moves->sum('product_uom_qty'))->toBe(10.0)
        ->and(PurchaseHelper::messageBodies($receipt))->toContain($order->name)
        ->and(PurchaseHelper::messageBodies($receipt))->not->toContain('<a href');

    expect(PurchaseHelper::dispatchedOf($events, OrderConfirmed::class))->toHaveCount(1)
        ->and(PurchaseHelper::dispatchedOf($events, OrderConfirmed::class)[0]->order->is($order))->toBeTrue();
});

it('receives part of a purchase order and creates the backorder without panel routes', function () {
    $events = PurchaseHelper::recordEvents([OperationDone::class, OperationBackOrdered::class]);

    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    $receipt = PurchaseHelper::partialReceive($order, 6);

    $backorder = InventoryHelper::backorderOf($receipt);

    expect($receipt->state)->toBe(OperationState::DONE)
        ->and($order->refresh()->receipt_status)->toBe(OrderReceiptStatus::PARTIAL)
        ->and($backorder)->not->toBeNull()
        ->and(PurchaseHelper::messageBodies($backorder))->toContain($receipt->name)
        ->and(PurchaseHelper::messageBodies($backorder))->not->toContain('<a href')
        ->and(PurchaseHelper::messageBodies($receipt))->toContain($backorder->name)
        ->and(PurchaseHelper::messageBodies($receipt))->not->toContain('<a href');

    expect(PurchaseHelper::dispatchedOf($events, OperationDone::class))->not->toBeEmpty()
        ->and(PurchaseHelper::dispatchedOf($events, OperationBackOrdered::class))->toHaveCount(1);
});

it('returns a received purchase without panel routes', function () {
    $events = PurchaseHelper::recordEvents([OperationReturned::class]);

    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    $receipt = $order->operations->first();

    Inventory::completeTransfer($receipt->refresh());

    $return = Inventory::createReturn($receipt->refresh(), [$receipt->moves->first()->id => 4]);

    expect($return)->not->toBeNull()
        ->and((float) $return->moves->sum('product_uom_qty'))->toBe(4.0)
        ->and(PurchaseHelper::messageBodies($return))->toContain($receipt->name)
        ->and(PurchaseHelper::messageBodies($return))->not->toContain('<a href')
        ->and(PurchaseHelper::messageBodies($receipt->refresh()))->toContain($return->name)
        ->and(PurchaseHelper::messageBodies($receipt))->not->toContain('<a href');

    expect(PurchaseHelper::dispatchedOf($events, OperationReturned::class))->toHaveCount(1);
});

it('links the purchase order, backorder and return in the chatter messages when panel routes exist', function () {
    Route::setRoutes($this->routesWithPanel);

    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    $receipt = PurchaseHelper::partialReceive($order, 6);

    $backorder = InventoryHelper::backorderOf($receipt);

    $return = Inventory::createReturn($receipt->refresh(), [$receipt->moves->first()->id => 2]);

    $orderUrl = $order->refresh()->getChatterResourceUrl();
    $receiptUrl = $receipt->getChatterResourceUrl();
    $backorderUrl = $backorder->getChatterResourceUrl();
    $returnUrl = $return->getChatterResourceUrl();

    expect($orderUrl)->not->toBe('')
        ->and($receiptUrl)->not->toBe('')
        ->and($backorderUrl)->not->toBe('')
        ->and($returnUrl)->not->toBe('')
        ->and(PurchaseHelper::messageBodies($receipt))->toContain('<a href="'.e($orderUrl).'"')
        ->and(PurchaseHelper::messageBodies($receipt))->toContain('<a href="'.e($backorderUrl).'"')
        ->and(PurchaseHelper::messageBodies($receipt))->toContain('<a href="'.e($returnUrl).'"')
        ->and(PurchaseHelper::messageBodies($backorder))->toContain('<a href="'.e($receiptUrl).'"')
        ->and(PurchaseHelper::messageBodies($return))->toContain('<a href="'.e($receiptUrl).'"');
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

    $events = PurchaseHelper::recordEvents([OrderConfirmed::class, OperationDone::class, MoveConfirmed::class]);

    $order = PurchaseOrderFacade::confirmPurchaseOrder($order->refresh());

    Inventory::completeTransfer($order->operations()->first()->refresh());

    $bill = PurchaseHelper::postBill(PurchaseHelper::createBill($order->refresh()));

    expect(Auth::check())->toBeFalse()
        ->and($order->refresh()->state)->toBe(OrderState::PURCHASE)
        ->and($order->receipt_status)->toBe(OrderReceiptStatus::FULL)
        ->and($bill->state)->toBe(MoveState::POSTED)
        ->and((float) $bill->amount_total)->toBe(345.0)
        ->and(PurchaseHelper::dispatchedOf($events, OrderConfirmed::class))->toHaveCount(1)
        ->and(PurchaseHelper::dispatchedOf($events, OperationDone::class))->not->toBeEmpty()
        ->and(PurchaseHelper::dispatchedOf($events, MoveConfirmed::class))->toHaveCount(1);
});

it('leaves an order above the approval amount waiting for approval when no user is acting', function () {
    $order = PurchaseOrderFacade::confirmPurchaseOrder(PurchaseHelper::orderAboveApprovalAmount($this->warehouse, $this->product));

    expect($order->state)->toBe(OrderState::TO_APPROVE)
        ->and($order->operations()->exists())->toBeFalse();
});

it('confirms an order above the approval amount on behalf of an approver with no authenticated user', function () {
    $approver = PurchaseHelper::userWithResourcePermission(PermissionType::GLOBAL);

    $order = PurchaseHelper::orderAboveApprovalAmount($this->warehouse, $this->product);

    $events = PurchaseHelper::recordEvents([OrderConfirmed::class]);

    $order = PurchaseOrderFacade::confirmPurchaseOrder($order, $approver);

    $receipt = $order->operations()->first();

    expect(Auth::check())->toBeFalse()
        ->and($order->state)->toBe(OrderState::PURCHASE)
        ->and($order->approved_at)->not->toBeNull()
        ->and($receipt)->not->toBeNull()
        ->and((float) $receipt->moves->sum('product_uom_qty'))->toBe(3.0);

    expect(PurchaseHelper::dispatchedOf($events, OrderConfirmed::class))->toHaveCount(1)
        ->and(PurchaseHelper::dispatchedOf($events, OrderConfirmed::class)[0]->order->is($order))->toBeTrue();
});

it('leaves an order above the approval amount waiting for approval when confirmed on behalf of a non-approver', function () {
    $clerk = PurchaseHelper::userWithResourcePermission(PermissionType::INDIVIDUAL);

    $order = PurchaseHelper::orderAboveApprovalAmount($this->warehouse, $this->product);

    $events = PurchaseHelper::recordEvents([OrderConfirmed::class]);

    $order = PurchaseOrderFacade::confirmPurchaseOrder($order, $clerk);

    expect($order->state)->toBe(OrderState::TO_APPROVE)
        ->and($order->approved_at)->toBeNull()
        ->and($order->operations()->exists())->toBeFalse()
        ->and(PurchaseHelper::dispatchedOf($events, OrderConfirmed::class))->toBeEmpty();
});

it('approves an order waiting for approval on behalf of an approver with no authenticated user', function () {
    $approver = PurchaseHelper::userWithResourcePermission(PermissionType::GROUP);

    $order = PurchaseOrderFacade::confirmPurchaseOrder(PurchaseHelper::orderAboveApprovalAmount($this->warehouse, $this->product));

    expect($order->state)->toBe(OrderState::TO_APPROVE);

    $events = PurchaseHelper::recordEvents([OrderConfirmed::class]);

    $order = PurchaseOrderFacade::approvePurchaseOrder($order, $approver);

    $receipt = $order->operations()->first();

    expect(Auth::check())->toBeFalse()
        ->and($order->state)->toBe(OrderState::PURCHASE)
        ->and($order->approved_at)->not->toBeNull()
        ->and($receipt)->not->toBeNull()
        ->and((float) $receipt->moves->sum('product_uom_qty'))->toBe(3.0)
        ->and(PurchaseHelper::messageBodies($receipt))->toContain($order->name);

    expect(PurchaseHelper::dispatchedOf($events, OrderConfirmed::class))->toHaveCount(1);
});

it('refuses to approve an order on behalf of a user who may not approve', function () {
    $clerk = PurchaseHelper::userWithResourcePermission(PermissionType::INDIVIDUAL);

    $order = PurchaseOrderFacade::confirmPurchaseOrder(PurchaseHelper::orderAboveApprovalAmount($this->warehouse, $this->product));

    expect(fn () => PurchaseOrderFacade::approvePurchaseOrder($order, $clerk))
        ->toThrow(OrderApprovalException::class, __('purchases::system.order-workflow.approve.not-allowed'));

    expect($order->refresh()->state)->toBe(OrderState::TO_APPROVE)
        ->and($order->operations()->exists())->toBeFalse();
});

it('refuses to approve an order that is not waiting for approval', function () {
    $approver = PurchaseHelper::userWithResourcePermission(PermissionType::GLOBAL);

    $order = PurchaseHelper::orderAboveApprovalAmount($this->warehouse, $this->product);

    expect(fn () => PurchaseOrderFacade::approvePurchaseOrder($order, $approver))
        ->toThrow(OrderApprovalException::class, __('purchases::system.order-workflow.approve.not-waiting-approval'));

    expect($order->refresh()->state)->toBe(OrderState::DRAFT)
        ->and($order->operations()->exists())->toBeFalse();
});
