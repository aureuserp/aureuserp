<?php

use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PURCHASE_ORDER_LINE_JSON_STRUCTURE = [
    'id',
    'order_id',
    'product_id',
    'product_qty',
    'price_unit',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsPurchaseOrderLineApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function purchaseOrderLineRoute(string $action, mixed $order, mixed $line = null): string
{
    $name = "admin.api.v1.purchases.purchase-orders.lines.{$action}";

    $parameters = ['purchase_order' => $order];

    if ($line !== null) {
        $parameters['line'] = $line;
    }

    return route($name, $parameters);
}

function createPurchaseOrderWithLines(int $lineCount = 2): Order
{
    $order = Order::factory()->create();

    OrderLine::factory()->count($lineCount)->create([
        'order_id'    => $order->id,
        'company_id'  => $order->company_id,
        'currency_id' => $order->currency_id,
        'partner_id'  => $order->partner_id,
        'state'       => $order->state,
    ]);

    return $order->refresh();
}

it('requires authentication to list purchase order lines', function () {
    $order = createPurchaseOrderWithLines();

    $this->getJson(purchaseOrderLineRoute('index', $order->id))
        ->assertUnauthorized();
});

it('forbids listing purchase order lines without permission', function () {
    actingAsPurchaseOrderLineApiUser();

    $order = createPurchaseOrderWithLines();

    $this->getJson(purchaseOrderLineRoute('index', $order->id))
        ->assertForbidden();
});

it('lists purchase order lines for authorized users', function () {
    actingAsPurchaseOrderLineApiUser(['view_purchase_purchase::order']);

    $order = createPurchaseOrderWithLines(2);

    $this->getJson(purchaseOrderLineRoute('index', $order->id))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => ['*' => PURCHASE_ORDER_LINE_JSON_STRUCTURE]]);
});

it('shows a purchase order line for authorized users', function () {
    actingAsPurchaseOrderLineApiUser(['view_purchase_purchase::order']);

    $order = createPurchaseOrderWithLines(1);
    $line = $order->lines()->firstOrFail();

    $this->getJson(purchaseOrderLineRoute('show', $order->id, $line->id))
        ->assertOk()
        ->assertJsonPath('data.id', $line->id)
        ->assertJsonPath('data.order_id', $order->id)
        ->assertJsonStructure(['data' => PURCHASE_ORDER_LINE_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent purchase order when listing lines', function () {
    actingAsPurchaseOrderLineApiUser(['view_purchase_purchase::order']);

    $this->getJson(purchaseOrderLineRoute('index', 999999))
        ->assertNotFound();
});

it('returns 404 for a non-existent purchase order line', function () {
    actingAsPurchaseOrderLineApiUser(['view_purchase_purchase::order']);

    $order = createPurchaseOrderWithLines(1);

    $this->getJson(purchaseOrderLineRoute('show', $order->id, 999999))
        ->assertNotFound();
});

it('filters purchase order lines by product id', function () {
    actingAsPurchaseOrderLineApiUser(['view_purchase_purchase::order']);

    $order = createPurchaseOrderWithLines(2);
    $matchingLine = $order->lines()->firstOrFail();

    $response = $this->getJson(
        purchaseOrderLineRoute('index', $order->id).'?filter[product_id]='.$matchingLine->product_id
    )->assertOk();

    $productIds = collect($response->json('data'))->pluck('product_id')->unique()->values()->all();

    expect($productIds)->toBe([$matchingLine->product_id]);
});

it('sorts purchase order lines by id descending', function () {
    actingAsPurchaseOrderLineApiUser(['view_purchase_purchase::order']);

    $order = createPurchaseOrderWithLines(2);

    $response = $this->getJson(purchaseOrderLineRoute('index', $order->id).'?sort=-id')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->values()->all();
    $sortedIds = collect($ids)->sortDesc()->values()->all();

    expect($ids)->toBe($sortedIds);
});

it('includes related product for purchase order lines', function () {
    actingAsPurchaseOrderLineApiUser(['view_purchase_purchase::order']);

    $order = createPurchaseOrderWithLines(1);

    $this->getJson(purchaseOrderLineRoute('index', $order->id).'?include=product')
        ->assertOk()
        ->assertJsonPath('data.0.product.id', fn ($id) => is_int($id));
});
