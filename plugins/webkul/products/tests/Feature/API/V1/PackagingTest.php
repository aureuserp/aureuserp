<?php

use Webkul\Product\Models\Packaging;
use Webkul\Product\Models\Product;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PRODUCTS_PACKAGING_JSON_STRUCTURE = [
    'id',
    'name',
    'qty',
    'product_id',
];

const PRODUCTS_PACKAGING_REQUIRED_FIELDS = [
    'name',
    'qty',
    'product_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsProductsPackagingApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function productsPackagingRoute(string $action, mixed $packaging = null): string
{
    $name = "admin.api.v1.products.packagings.{$action}";

    return $packaging ? route($name, $packaging) : route($name);
}

function productsPackagingPayload(array $overrides = []): array
{
    $product = Product::factory()->create();

    return array_replace_recursive([
        'name'       => 'Box of 12',
        'qty'        => 12,
        'product_id' => $product->id,
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list packagings', function () {
    $this->getJson(productsPackagingRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a packaging', function () {
    $this->postJson(productsPackagingRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a packaging', function () {
    $packaging = Packaging::factory()->create();

    $this->getJson(productsPackagingRoute('show', $packaging))
        ->assertUnauthorized();
});

it('requires authentication to update a packaging', function () {
    $packaging = Packaging::factory()->create();

    $this->patchJson(productsPackagingRoute('update', $packaging), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a packaging', function () {
    $packaging = Packaging::factory()->create();

    $this->deleteJson(productsPackagingRoute('destroy', $packaging))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing packagings without permission', function () {
    actingAsProductsPackagingApiUser();

    $this->getJson(productsPackagingRoute('index'))
        ->assertForbidden();
});

it('forbids creating a packaging without permission', function () {
    actingAsProductsPackagingApiUser();

    $this->postJson(productsPackagingRoute('store'), productsPackagingPayload())
        ->assertForbidden();
});

it('forbids showing a packaging without permission', function () {
    actingAsProductsPackagingApiUser();

    $packaging = Packaging::factory()->create();

    $this->getJson(productsPackagingRoute('show', $packaging))
        ->assertForbidden();
});

it('forbids updating a packaging without permission', function () {
    actingAsProductsPackagingApiUser();

    $packaging = Packaging::factory()->create();

    $this->patchJson(productsPackagingRoute('update', $packaging), [])
        ->assertForbidden();
});

it('forbids deleting a packaging without permission', function () {
    actingAsProductsPackagingApiUser();

    $packaging = Packaging::factory()->create();

    $this->deleteJson(productsPackagingRoute('destroy', $packaging))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists packagings for authorized users', function () {
    actingAsProductsPackagingApiUser(['view_any_product_packaging']);

    Packaging::factory()->count(3)->create();

    $this->getJson(productsPackagingRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters packagings by name', function () {
    actingAsProductsPackagingApiUser(['view_any_product_packaging']);

    $packaging = Packaging::factory()->create(['name' => 'UniquePackagingXYZ']);
    Packaging::factory()->count(2)->create();

    $response = $this->getJson(productsPackagingRoute('index').'?filter[name]=UniquePackagingXYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($packaging->id);
});

it('filters packagings by product_id', function () {
    actingAsProductsPackagingApiUser(['view_any_product_packaging']);

    $product = Product::factory()->create();
    $packaging = Packaging::factory()->create(['product_id' => $product->id]);
    Packaging::factory()->create();

    $response = $this->getJson(productsPackagingRoute('index')."?filter[product_id]={$product->id}")
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($packaging->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates a packaging', function () {
    actingAsProductsPackagingApiUser(['create_product_packaging']);

    $payload = productsPackagingPayload();

    $this->postJson(productsPackagingRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Packaging created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonPath('data.qty', $payload['qty'])
        ->assertJsonPath('data.product_id', $payload['product_id'])
        ->assertJsonStructure(['data' => PRODUCTS_PACKAGING_JSON_STRUCTURE]);

    $this->assertDatabaseHas('products_packagings', [
        'name'       => $payload['name'],
        'product_id' => $payload['product_id'],
    ]);
});

it('creates a packaging with optional fields', function () {
    actingAsProductsPackagingApiUser(['create_product_packaging']);

    $this->postJson(productsPackagingRoute('store'), productsPackagingPayload([
        'barcode' => '5901234123457',
        'sort'    => 1,
    ]))
        ->assertCreated()
        ->assertJsonPath('data.barcode', '5901234123457');
});

it('validates required fields when creating a packaging', function (string $field) {
    actingAsProductsPackagingApiUser(['create_product_packaging']);

    $payload = productsPackagingPayload();
    unset($payload[$field]);

    $this->postJson(productsPackagingRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(PRODUCTS_PACKAGING_REQUIRED_FIELDS);

it('rejects a non-existent product_id when creating a packaging', function () {
    actingAsProductsPackagingApiUser(['create_product_packaging']);

    $this->postJson(productsPackagingRoute('store'), productsPackagingPayload(['product_id' => 999999]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['product_id']);
});

it('rejects a negative qty when creating a packaging', function () {
    actingAsProductsPackagingApiUser(['create_product_packaging']);

    $this->postJson(productsPackagingRoute('store'), productsPackagingPayload(['qty' => -1]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['qty']);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a packaging for authorized users', function () {
    actingAsProductsPackagingApiUser(['view_product_packaging']);

    $packaging = Packaging::factory()->create();

    $this->getJson(productsPackagingRoute('show', $packaging))
        ->assertOk()
        ->assertJsonPath('data.id', $packaging->id)
        ->assertJsonPath('data.name', $packaging->name)
        ->assertJsonPath('data.product_id', $packaging->product_id)
        ->assertJsonStructure(['data' => PRODUCTS_PACKAGING_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent packaging', function () {
    actingAsProductsPackagingApiUser(['view_product_packaging']);

    $this->getJson(productsPackagingRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a packaging', function () {
    actingAsProductsPackagingApiUser(['update_product_packaging']);

    $packaging = Packaging::factory()->create();

    $this->patchJson(productsPackagingRoute('update', $packaging), [
        'name' => 'Updated Packaging',
        'qty'  => 24,
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Packaging updated successfully.')
        ->assertJsonPath('data.name', 'Updated Packaging');

    $this->assertDatabaseHas('products_packagings', [
        'id'   => $packaging->id,
        'name' => 'Updated Packaging',
        'qty'  => 24,
    ]);
});

it('returns 404 when updating a non-existent packaging', function () {
    actingAsProductsPackagingApiUser(['update_product_packaging']);

    $this->patchJson(productsPackagingRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('deletes a packaging', function () {
    actingAsProductsPackagingApiUser(['delete_product_packaging']);

    $packaging = Packaging::factory()->create();

    $this->deleteJson(productsPackagingRoute('destroy', $packaging))
        ->assertOk()
        ->assertJsonPath('message', 'Packaging deleted successfully.');

    $this->assertDatabaseMissing('products_packagings', ['id' => $packaging->id]);
});

it('returns 404 when deleting a non-existent packaging', function () {
    actingAsProductsPackagingApiUser(['delete_product_packaging']);

    $this->deleteJson(productsPackagingRoute('destroy', 999999))
        ->assertNotFound();
});
