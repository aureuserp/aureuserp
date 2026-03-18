<?php

use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Models\ProductSupplier;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const VENDOR_PRICE_LIST_JSON_STRUCTURE = [
    'id',
    'partner_id',
    'product_id',
    'currency_id',
    'price',
    'min_qty',
];

const VENDOR_PRICE_LIST_REQUIRED_FIELDS = [
    'partner_id',
    'product_id',
    'currency_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsVendorPriceListApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function vendorPriceListRoute(string $action, mixed $priceList = null): string
{
    $name = "admin.api.v1.purchases.vendor-price-lists.{$action}";

    return $priceList ? route($name, $priceList) : route($name);
}

function vendorPriceListPayload(array $overrides = []): array
{
    $currency = Currency::first() ?? Currency::factory()->create();
    $partner = Partner::factory()->create();
    $product = Product::factory()->create(['is_configurable' => false]);

    return array_merge([
        'partner_id'  => $partner->id,
        'product_id'  => $product->id,
        'currency_id' => $currency->id,
        'price'       => 100.00,
        'min_qty'     => 1,
        'delay'       => 5,
    ], $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list vendor price lists', function () {
    $this->getJson(vendorPriceListRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a vendor price list', function () {
    $this->postJson(vendorPriceListRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing vendor price lists without permission', function () {
    actingAsVendorPriceListApiUser();

    $this->getJson(vendorPriceListRoute('index'))
        ->assertForbidden();
});

it('forbids creating a vendor price list without permission', function () {
    actingAsVendorPriceListApiUser();

    $this->postJson(vendorPriceListRoute('store'), vendorPriceListPayload())
        ->assertForbidden();
});

it('forbids updating a vendor price list without permission', function () {
    actingAsVendorPriceListApiUser();

    $priceList = ProductSupplier::factory()->create();

    $this->patchJson(vendorPriceListRoute('update', $priceList), [])
        ->assertForbidden();
});

it('forbids deleting a vendor price list without permission', function () {
    actingAsVendorPriceListApiUser();

    $priceList = ProductSupplier::factory()->create();

    $this->deleteJson(vendorPriceListRoute('destroy', $priceList))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists vendor price lists for authorized users', function () {
    actingAsVendorPriceListApiUser(['view_any_purchase_vendor::price']);

    ProductSupplier::factory()->count(3)->create();

    $this->getJson(vendorPriceListRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a vendor price list', function () {
    actingAsVendorPriceListApiUser(['create_purchase_vendor::price']);

    $payload = vendorPriceListPayload();
    $response = $this->postJson(vendorPriceListRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Vendor price list created successfully.')
        ->assertJsonPath('data.partner_id', $payload['partner_id'])
        ->assertJsonPath('data.product_id', $payload['product_id'])
        ->assertJsonStructure(['data' => VENDOR_PRICE_LIST_JSON_STRUCTURE]);

    $this->assertDatabaseHas('products_product_suppliers', [
        'id'         => $response->json('data.id'),
        'partner_id' => $payload['partner_id'],
        'product_id' => $payload['product_id'],
    ]);
});

it('validates required fields when creating a vendor price list', function (string $field) {
    actingAsVendorPriceListApiUser(['create_purchase_vendor::price']);

    $payload = vendorPriceListPayload();
    unset($payload[$field]);

    $this->postJson(vendorPriceListRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(VENDOR_PRICE_LIST_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a vendor price list for authorized users', function () {
    actingAsVendorPriceListApiUser(['view_purchase_vendor::price']);

    $priceList = ProductSupplier::factory()->create();

    $this->getJson(vendorPriceListRoute('show', $priceList))
        ->assertOk()
        ->assertJsonPath('data.id', $priceList->id)
        ->assertJsonStructure(['data' => VENDOR_PRICE_LIST_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent vendor price list', function () {
    actingAsVendorPriceListApiUser(['view_purchase_vendor::price']);

    $this->getJson(vendorPriceListRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a vendor price list', function () {
    actingAsVendorPriceListApiUser(['update_purchase_vendor::price']);

    $priceList = ProductSupplier::factory()->create();

    $this->patchJson(vendorPriceListRoute('update', $priceList), ['price' => 250.00, 'delay' => 10])
        ->assertOk()
        ->assertJsonPath('message', 'Vendor price list updated successfully.')
        ->assertJsonPath('data.delay', 10);

    $this->assertDatabaseHas('products_product_suppliers', [
        'id'    => $priceList->id,
        'price' => 250.00,
        'delay' => 10,
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes a vendor price list for authorized users', function () {
    actingAsVendorPriceListApiUser(['delete_purchase_vendor::price']);

    $priceList = ProductSupplier::factory()->create();

    $this->deleteJson(vendorPriceListRoute('destroy', $priceList))
        ->assertOk()
        ->assertJsonPath('message', 'Vendor price list deleted successfully.');

    $this->assertDatabaseMissing('products_product_suppliers', ['id' => $priceList->id]);
});
