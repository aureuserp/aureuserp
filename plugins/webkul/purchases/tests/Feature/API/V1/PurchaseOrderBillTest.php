<?php

use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Purchase\Models\Order;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsPurchaseOrderBillApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function purchaseOrderBillRoute(mixed $order): string
{
    return route('admin.api.v1.purchases.purchase-orders.bills.index', ['purchase_order' => $order]);
}

it('requires authentication to list purchase order bills', function () {
    $order = Order::factory()->create();

    $this->getJson(purchaseOrderBillRoute($order->id))
        ->assertUnauthorized();
});

it('forbids listing purchase order bills without permission', function () {
    actingAsPurchaseOrderBillApiUser();

    $order = Order::factory()->create();

    $this->getJson(purchaseOrderBillRoute($order->id))
        ->assertForbidden();
});

it('lists purchase order bills for authorized users', function () {
    actingAsPurchaseOrderBillApiUser(['view_purchase_purchase::order']);

    $order = Order::factory()->create();
    $bill = Move::factory()->vendorBill()->create(['company_id' => $order->company_id]);
    $order->accountMoves()->attach($bill->id);

    $this->getJson(purchaseOrderBillRoute($order->id))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $bill->id)
        ->assertJsonPath('data.0.move_type', MoveType::IN_INVOICE->value)
        ->assertJsonStructure(['data']);
});

it('returns 404 for a non-existent purchase order when listing bills', function () {
    actingAsPurchaseOrderBillApiUser(['view_purchase_purchase::order']);

    $this->getJson(purchaseOrderBillRoute(999999))
        ->assertNotFound();
});

it('does not return bills from other purchase orders', function () {
    actingAsPurchaseOrderBillApiUser(['view_purchase_purchase::order']);

    $order = Order::factory()->create();
    $orderBill = Move::factory()->vendorBill()->create(['company_id' => $order->company_id]);
    $order->accountMoves()->attach($orderBill->id);

    $otherOrder = Order::factory()->create();
    $otherOrderBill = Move::factory()->vendorBill()->create(['company_id' => $otherOrder->company_id]);
    $otherOrder->accountMoves()->attach($otherOrderBill->id);

    $response = $this->getJson(purchaseOrderBillRoute($order->id))
        ->assertOk();

    $returnedIds = collect($response->json('data'))->pluck('id');

    expect($returnedIds)
        ->toContain($orderBill->id)
        ->not->toContain($otherOrderBill->id);
});

it('filters purchase order bills by state', function () {
    actingAsPurchaseOrderBillApiUser(['view_purchase_purchase::order']);

    $order = Order::factory()->create();

    $draftBill = Move::factory()->vendorBill()->create([
        'company_id' => $order->company_id,
        'state'      => 'draft',
    ]);
    $postedBill = Move::factory()->vendorBill()->create([
        'company_id' => $order->company_id,
        'state'      => 'posted',
    ]);

    $order->accountMoves()->attach([$draftBill->id, $postedBill->id]);

    $response = $this->getJson(purchaseOrderBillRoute($order->id).'?filter[state]=draft')
        ->assertOk();

    $states = collect($response->json('data'))->pluck('state')->unique()->values()->all();

    expect($states)->toBe(['draft']);
});

it('includes company relationship for purchase order bills', function () {
    actingAsPurchaseOrderBillApiUser(['view_purchase_purchase::order']);

    $order = Order::factory()->create();
    $bill = Move::factory()->vendorBill()->create(['company_id' => $order->company_id]);
    $order->accountMoves()->attach($bill->id);

    $this->getJson(purchaseOrderBillRoute($order->id).'?include=company')
        ->assertOk()
        ->assertJsonPath('data.0.company.id', fn ($id) => is_int($id));
});
