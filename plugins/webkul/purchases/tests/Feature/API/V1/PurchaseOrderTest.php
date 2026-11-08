<?php

use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Tax;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Facades\PurchaseOrder as PurchaseOrderFacade;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\CurrencyRate;

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
    $companyId = current_company_id() ?? Company::factory()->create(['currency_id' => $currency->id])->id;
    $partner = Partner::factory()->create();

    $payload = [
        'partner_id'  => $partner->id,
        'currency_id' => $currency->id,
        'ordered_at'  => now()->format('Y-m-d'),
        'company_id'  => $companyId,
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

function expectApiOrderTotalsFromStoredLines(int $orderId, array $responseData): Order
{
    $order = Order::query()->with(['lines', 'currency', 'company.currency'])->findOrFail($orderId);

    $currency = $order->currency;

    $companyCurrency = $order->company->currency;

    $rate = (float) $order->currency_rate;

    foreach ($order->lines as $line) {
        foreach (['price_subtotal', 'price_tax', 'price_total'] as $column) {
            expect((float) $line->{$column})->toEqualWithDelta($currency->round((float) $line->{$column}), 0.000001);
        }

        expect((float) $line->price_total_cc)->toEqualWithDelta($companyCurrency->round((float) $line->price_total / $rate), 0.000001);
    }

    expect((float) $order->untaxed_amount)->toEqualWithDelta($order->lines->sum(fn ($line) => (float) $line->price_subtotal), 0.000001)
        ->and((float) $order->tax_amount)->toEqualWithDelta($order->lines->sum(fn ($line) => (float) $line->price_tax), 0.000001)
        ->and((float) $order->total_amount)->toEqualWithDelta($order->lines->sum(fn ($line) => (float) $line->price_total), 0.000001)
        ->and((float) $order->total_cc_amount)->toEqualWithDelta($order->lines->sum(fn ($line) => (float) $line->price_total_cc), 0.000001)
        ->and((float) $responseData['untaxed_amount'])->toEqualWithDelta((float) $order->untaxed_amount, 0.000001)
        ->and((float) $responseData['tax_amount'])->toEqualWithDelta((float) $order->tax_amount, 0.000001)
        ->and((float) $responseData['total_amount'])->toEqualWithDelta((float) $order->total_amount, 0.000001)
        ->and((float) $responseData['total_cc_amount'])->toEqualWithDelta((float) $order->total_cc_amount, 0.000001);

    $responseLines = collect($responseData['lines'])->keyBy('id');

    foreach ($order->lines as $line) {
        expect((float) $responseLines[$line->id]['price_subtotal'])->toEqualWithDelta((float) $line->price_subtotal, 0.000001)
            ->and((float) $responseLines[$line->id]['price_total'])->toEqualWithDelta((float) $line->price_total, 0.000001)
            ->and((float) $responseLines[$line->id]['price_total_cc'])->toEqualWithDelta((float) $line->price_total_cc, 0.000001);
    }

    return $order;
}

it('builds api order totals from stored line amounts at the order currency precision', function (int $decimalPlaces, float $rounding) {
    actingAsPurchaseOrderApiUser(['create_purchase_purchase::order', 'update_purchase_purchase::order']);

    $payload = purchaseOrderPayload();

    $currency = Currency::factory()->create([
        'decimal_places' => $decimalPlaces,
        'rounding'       => $rounding,
    ]);

    CurrencyRate::factory()->create([
        'currency_id' => $currency->id,
        'company_id'  => null,
        'rate'        => 1.3579,
        'name'        => now()->subDay()->toDateString(),
    ]);

    $tax = Tax::factory()->create([
        'amount'                 => 15,
        'amount_type'            => AmountType::PERCENT,
        'price_include_override' => TaxIncludeOverride::TAX_EXCLUDED,
        'type_tax_use'           => TypeTaxUse::PURCHASE,
        'company_id'             => $payload['company_id'],
    ]);

    $payload['currency_id'] = $currency->id;

    $payload['lines'] = [
        makePurchaseLinePayload(['product_qty' => 3, 'price_unit' => 33.335, 'discount' => 7.5, 'taxes' => [$tax->id]]),
        makePurchaseLinePayload(['product_qty' => 7, 'price_unit' => 12.3456, 'taxes' => [$tax->id]]),
        makePurchaseLinePayload(['product_qty' => 1, 'price_unit' => 0.3333, 'discount' => 12.5, 'planned_at' => now()->addDays(12)->format('Y-m-d')]),
    ];

    $created = $this->postJson(purchaseOrderRoute('store'), $payload)
        ->assertCreated();

    $order = expectApiOrderTotalsFromStoredLines($created->json('data.id'), $created->json('data'));

    expect((float) $order->currency_rate)->not->toEqualWithDelta(1.0, 0.000001)
        ->and($order->planned_at?->toDateString())->toBe(now()->addDays(12)->toDateString());

    $firstLine = $order->lines->sortBy('id')->first();

    $updated = $this->patchJson(purchaseOrderRoute('update', $order), [
        'lines' => [
            [
                'id'          => $firstLine->id,
                'product_id'  => $firstLine->product_id,
                'planned_at'  => now()->addDays(7)->format('Y-m-d'),
                'product_qty' => 4.5,
                'price_unit'  => 19.9999,
                'discount'    => 2.5,
                'taxes'       => [$tax->id],
            ],
            makePurchaseLinePayload(['product_qty' => 2, 'price_unit' => 1234.5678, 'discount' => 3, 'taxes' => [$tax->id]]),
        ],
    ])->assertOk();

    $order = expectApiOrderTotalsFromStoredLines($order->id, $updated->json('data'));

    $stored = fn (Order $order): array => [
        'header' => array_map('floatval', $order->only(['untaxed_amount', 'tax_amount', 'total_amount', 'total_cc_amount'])),
        'lines'  => $order->lines()->orderBy('id')->get()
            ->map(fn (OrderLine $line): array => array_map('floatval', $line->only(['price_unit', 'price_subtotal', 'price_tax', 'price_total', 'price_total_cc'])))
            ->all(),
    ];

    $beforeRecompute = $stored($order->refresh());

    PurchaseOrderFacade::computePurchaseOrder($order->refresh());

    expect($stored($order->refresh()))->toEqualWithDelta($beforeRecompute, 0.000001);
})->with([
    'two decimals'   => [2, 0.01],
    'no decimals'    => [0, 1],
    'three decimals' => [3, 0.001],
]);

it('rejects changing the currency or order deadline of a confirmed purchase order', function (OrderState $state, string $field, string $messageKey) {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->create(['state' => $state])->refresh();

    $changes = [
        'currency_id' => Currency::factory()->create()->id,
        'ordered_at'  => $order->ordered_at->copy()->subDays(10)->toDateTimeString(),
    ];

    $this->patchJson(purchaseOrderRoute('update', $order), [$field => $changes[$field]])
        ->assertUnprocessable()
        ->assertJsonPath('message', __($messageKey));

    $stored = $order->fresh();

    expect($stored->currency_id)->toBe($order->currency_id)
        ->and($stored->ordered_at->equalTo($order->ordered_at))->toBeTrue()
        ->and((float) $stored->currency_rate)->toEqualWithDelta((float) $order->currency_rate, 0.000001);
})->with([
    'purchase currency' => [OrderState::PURCHASE, 'currency_id', 'purchases::app.api.purchase-order.currency-locked'],
    'purchase deadline' => [OrderState::PURCHASE, 'ordered_at', 'purchases::app.api.purchase-order.order-date-locked'],
    'done currency'     => [OrderState::DONE, 'currency_id', 'purchases::app.api.purchase-order.currency-locked'],
    'done deadline'     => [OrderState::DONE, 'ordered_at', 'purchases::app.api.purchase-order.order-date-locked'],
]);

it('accepts the unchanged currency and order deadline on a confirmed purchase order', function () {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->purchase()->create()->refresh();

    $this->patchJson(purchaseOrderRoute('update', $order), [
        'currency_id'       => $order->currency_id,
        'ordered_at'        => $order->ordered_at->toDateTimeString(),
        'partner_reference' => 'VEN-REF-LOCKED',
    ])->assertOk();

    expect($order->fresh()->partner_reference)->toBe('VEN-REF-LOCKED');
});

it('lets a request for quotation change its currency and order deadline', function (OrderState $state) {
    actingAsPurchaseOrderApiUser(['update_purchase_purchase::order']);

    $order = Order::factory()->create(['state' => $state])->refresh();

    $currency = Currency::factory()->create();

    $orderedAt = $order->ordered_at->copy()->subDays(10)->startOfSecond();

    $this->patchJson(purchaseOrderRoute('update', $order), [
        'currency_id' => $currency->id,
        'ordered_at'  => $orderedAt->toDateTimeString(),
    ])->assertOk();

    $stored = $order->fresh();

    expect($stored->currency_id)->toBe($currency->id)
        ->and($stored->ordered_at->equalTo($orderedAt))->toBeTrue();
})->with([
    'draft' => [OrderState::DRAFT],
    'sent'  => [OrderState::SENT],
]);

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
