<?php

use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PURCHASE_ORDER_JSON_STRUCTURE = [
    'id',
    'partner_id',
    'company_id',
    'currency_id',
    'ordered_at',
    'state',
];

const PURCHASE_ORDER_REQUIRED_FIELDS = [
    'partner_id',
    'currency_id',
    'ordered_at',
    'company_id',
    'lines',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsPurchaseOrderApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function purchaseOrderRoute(string $action, mixed $order = null): string
{
    $name = "admin.api.v1.purchases.purchase-orders.{$action}";

    return $order ? route($name, $order) : route($name);
}

function makePurchaseLinePayload(array $overrides = []): array
{
    $product = Product::factory()->create(['is_configurable' => false]);

    return array_merge([
        'product_id'  => $product->id,
        'planned_at'  => now()->addDays(7)->format('Y-m-d'),
        'product_qty' => 10,
        'price_unit'  => 50.00,
    ], $overrides);
}

function purchaseOrderPayload(int $lineCount = 2, array $overrides = []): array
{
    $currency = Currency::first() ?? Currency::factory()->create();
    $company = Company::factory()->create(['currency_id' => $currency->id]);
    $partner = Partner::factory()->create();

    $payload = [
        'partner_id'  => $partner->id,
        'currency_id' => $currency->id,
        'ordered_at'  => now()->format('Y-m-d'),
        'company_id'  => $company->id,
        'lines'       => collect(range(1, $lineCount))
            ->map(fn () => makePurchaseLinePayload())
            ->all(),
    ];

    return array_replace_recursive($payload, $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list purchase orders', function () {
    $this->getJson(purchaseOrderRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a purchase order', function () {
    $this->postJson(purchaseOrderRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing purchase orders without permission', function () {
    actingAsPurchaseOrderApiUser();

    $this->getJson(purchaseOrderRoute('index'))
        ->assertForbidden();
});

it('forbids creating a purchase order without permission', function () {
    actingAsPurchaseOrderApiUser();

    $this->postJson(purchaseOrderRoute('store'), purchaseOrderPayload())
        ->assertForbidden();
});

it('forbids updating a purchase order without permission', function () {
    actingAsPurchaseOrderApiUser();

    $order = Order::factory()->create();

    $this->patchJson(purchaseOrderRoute('update', $order), [])
        ->assertForbidden();
});

it('forbids deleting a purchase order without permission', function () {
    actingAsPurchaseOrderApiUser();

    $order = Order::factory()->create();

    $this->deleteJson(purchaseOrderRoute('destroy', $order))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists purchase orders for authorized users', function () {
    actingAsPurchaseOrderApiUser(['view_any_purchase_purchase::order']);

    Order::factory()->count(3)->create();

    $this->getJson(purchaseOrderRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a purchase order with lines in a single API call', function () {
    actingAsPurchaseOrderApiUser(['create_purchase_purchase::order']);

    $payload = purchaseOrderPayload(lineCount: 2);
    $response = $this->postJson(purchaseOrderRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Purchase order created successfully.')
        ->assertJsonPath('data.partner_id', $payload['partner_id'])
        ->assertJsonCount(2, 'data.lines')
        ->assertJsonStructure(['data' => PURCHASE_ORDER_JSON_STRUCTURE]);

    $orderId = $response->json('data.id');

    $this->assertDatabaseHas('purchases_orders', [
        'id'         => $orderId,
        'partner_id' => $payload['partner_id'],
        'company_id' => $payload['company_id'],
    ]);

    $this->assertDatabaseCount('purchases_order_lines', 2);
});

it('validates required fields when creating a purchase order', function (string $field) {
    actingAsPurchaseOrderApiUser(['create_purchase_purchase::order']);

    $payload = purchaseOrderPayload();
    unset($payload[$field]);

    $this->postJson(purchaseOrderRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(PURCHASE_ORDER_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a purchase order for authorized users', function () {
    actingAsPurchaseOrderApiUser(['view_purchase_purchase::order']);

    $order = Order::factory()->create();

    $this->getJson(purchaseOrderRoute('show', $order).'?include=lines')
        ->assertOk()
        ->assertJsonPath('data.id', $order->id)
        ->assertJsonStructure(['data' => PURCHASE_ORDER_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent purchase order', function () {
    actingAsPurchaseOrderApiUser(['view_purchase_purchase::order']);

    $this->getJson(purchaseOrderRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a purchase order and syncs its lines', function () {
    actingAsPurchaseOrderApiUser(['create_purchase_purchase::order', 'update_purchase_purchase::order']);

    $order = Order::factory()->create();

    [$lineToKeepId, $lineToDeleteId] = OrderLine::factory()->count(2)->create([
        'order_id'    => $order->id,
        'company_id'  => $order->company_id,
        'currency_id' => $order->currency_id,
        'partner_id'  => $order->partner_id,
        'state'       => $order->state,
    ])->pluck('id')->values()->all();

    $updatePayload = [
        'lines' => [
            [
                'id'          => $lineToKeepId,
                'product_id'  => OrderLine::query()->find($lineToKeepId)->product_id,
                'planned_at'  => now()->addDays(7)->format('Y-m-d'),
                'product_qty' => 5,
                'price_unit'  => 200,
            ],
        ],
    ];

    $this->patchJson(purchaseOrderRoute('update', $order), $updatePayload)
        ->assertOk()
        ->assertJsonPath('message', 'Purchase order updated successfully.')
        ->assertJsonCount(1, 'data.lines');

    $this->assertDatabaseHas('purchases_order_lines', [
        'id'          => $lineToKeepId,
        'product_qty' => 5,
        'price_unit'  => 200,
    ]);

    $this->assertDatabaseMissing('purchases_order_lines', ['id' => $lineToDeleteId]);

    expect(OrderLine::query()->where('order_id', $order->id)->count())->toBe(1);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes a purchase order for authorized users', function () {
    actingAsPurchaseOrderApiUser(['delete_purchase_purchase::order']);

    $order = Order::factory()->create();

    $this->deleteJson(purchaseOrderRoute('destroy', $order))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase order deleted successfully.');

    $this->assertDatabaseMissing('purchases_orders', ['id' => $order->id]);
});

// ── Confirm ────────────────────────────────────────────────────────────────────

it('confirms a draft purchase order', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->draft()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.confirm', ['id' => $order->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase order confirmed successfully.')
        ->assertJsonPath('data.state', OrderState::PURCHASE->value);
});

it('confirms a sent purchase order', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->sent()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.confirm', ['id' => $order->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase order confirmed successfully.');
});

it('rejects confirming a purchase order that is already confirmed', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->purchase()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.confirm', ['id' => $order->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft or sent purchase orders can be confirmed.');
});

it('rejects confirming a canceled purchase order', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->canceled()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.confirm', ['id' => $order->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft or sent purchase orders can be confirmed.');
});

// ── Cancel ─────────────────────────────────────────────────────────────────────

it('cancels a draft purchase order', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->draft()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.cancel', ['id' => $order->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase order canceled successfully.')
        ->assertJsonPath('data.state', OrderState::CANCELED->value);
});

it('rejects canceling a locked (done) purchase order', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->done()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.cancel', ['id' => $order->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-locked and non-canceled purchase orders can be canceled.');
});

it('rejects canceling an already canceled purchase order', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->canceled()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.cancel', ['id' => $order->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-locked and non-canceled purchase orders can be canceled.');
});

// ── Draft ──────────────────────────────────────────────────────────────────────

it('sets a canceled purchase order back to draft', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->canceled()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.draft', ['id' => $order->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase order set to draft successfully.')
        ->assertJsonPath('data.state', OrderState::DRAFT->value);
});

it('rejects setting a non-canceled purchase order to draft', function (OrderState $state) {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->create(['state' => $state]);

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.draft', ['id' => $order->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only canceled purchase orders can be set to draft.');
})->with([
    'draft order'    => [OrderState::DRAFT],
    'purchase order' => [OrderState::PURCHASE],
]);

// ── Toggle Lock ────────────────────────────────────────────────────────────────

it('locks a confirmed (purchase) order via toggle-lock', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->purchase()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.toggle-lock', ['id' => $order->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase order lock state updated successfully.')
        ->assertJsonPath('data.state', OrderState::DONE->value);
});

it('unlocks a done order via toggle-lock', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->done()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.toggle-lock', ['id' => $order->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase order lock state updated successfully.')
        ->assertJsonPath('data.state', OrderState::PURCHASE->value);
});

it('rejects toggle-lock on a draft order', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->draft()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.toggle-lock', ['id' => $order->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only purchase or done orders can toggle lock state.');
});

// ── Confirm Receipt Date ───────────────────────────────────────────────────────

it('confirms receipt date for a purchase order', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->purchase()->create([
        'mail_reminder_confirmed' => false,
    ]);

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.confirm-receipt-date', ['id' => $order->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase order receipt date confirmed successfully.');

    $this->assertDatabaseHas('purchases_orders', [
        'id'                      => $order->id,
        'mail_reminder_confirmed' => true,
    ]);
});

it('rejects confirming receipt date when already confirmed', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->purchase()->create([
        'mail_reminder_confirmed' => true,
    ]);

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.confirm-receipt-date', ['id' => $order->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only unconfirmed purchase or done orders can confirm receipt date.');
});

it('rejects confirming receipt date for a draft order', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->draft()->create([
        'mail_reminder_confirmed' => false,
    ]);

    $this->postJson(route('admin.api.v1.purchases.purchase-orders.confirm-receipt-date', ['id' => $order->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only unconfirmed purchase or done orders can confirm receipt date.');
});
