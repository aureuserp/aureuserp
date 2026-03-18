<?php

use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryQuantityApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function inventoryQuantityRoute(string $action, mixed $quantity = null): string
{
    $name = "admin.api.v1.inventories.quantities.{$action}";

    return $quantity ? route($name, $quantity) : route($name);
}

function inventoryQuantityPayload(Product $product, Location $location, array $overrides = []): array
{
    $draftQuantity = ProductQuantity::factory()->make([
        'product_id'       => $product->id,
        'location_id'      => $location->id,
        'lot_id'           => null,
        'package_id'       => null,
        'counted_quantity' => fake()->randomFloat(2, 1, 100),
    ]);

    return array_replace_recursive([
        'location_id'      => $draftQuantity->location_id,
        'product_id'       => $draftQuantity->product_id,
        'lot_id'           => $draftQuantity->lot_id,
        'package_id'       => $draftQuantity->package_id,
        'counted_quantity' => $draftQuantity->counted_quantity,
        'scheduled_at'     => now()->toDateString(),
    ], $overrides);
}

it('requires authentication to list quantities', function () {
    $this->getJson(inventoryQuantityRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to show a quantity', function () {
    $quantity = ProductQuantity::factory()->create();

    $this->getJson(inventoryQuantityRoute('show', $quantity))
        ->assertUnauthorized();
});

it('requires authentication to create a quantity', function () {
    $this->postJson(inventoryQuantityRoute('store'), [])
        ->assertUnauthorized();
});

it('forbids listing quantities without permission', function () {
    actingAsInventoryQuantityApiUser();

    $this->getJson(inventoryQuantityRoute('index'))
        ->assertForbidden();
});

it('forbids showing a quantity without permission', function () {
    actingAsInventoryQuantityApiUser();

    $quantity = ProductQuantity::factory()->create();

    $this->getJson(inventoryQuantityRoute('show', $quantity))
        ->assertForbidden();
});

it('lists quantities for authorized users', function () {
    actingAsInventoryQuantityApiUser(['view_any_inventory_quantity']);

    ProductQuantity::factory()->count(3)->create();

    $this->getJson(inventoryQuantityRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters quantities by product_id', function () {
    actingAsInventoryQuantityApiUser(['view_any_inventory_quantity']);

    $product = Product::factory()->create();

    ProductQuantity::factory()->create(['product_id' => $product->id]);
    ProductQuantity::factory()->count(2)->create();

    $response = $this->getJson(inventoryQuantityRoute('index')."?filter[product_id]={$product->id}&include=product")
        ->assertOk();

    $productIds = collect($response->json('data'))
        ->pluck('product.id')
        ->unique()
        ->filter();

    expect($productIds)->toContain($product->id);
});

it('shows a quantity for authorized users', function () {
    actingAsInventoryQuantityApiUser(['view_any_inventory_quantity']);

    $quantity = ProductQuantity::factory()->create();

    $this->getJson(inventoryQuantityRoute('show', $quantity))
        ->assertOk()
        ->assertJsonPath('data.id', $quantity->id);
});

it('returns 404 for a non-existent quantity', function () {
    actingAsInventoryQuantityApiUser(['view_any_inventory_quantity']);

    $this->getJson(inventoryQuantityRoute('show', 999999))
        ->assertNotFound();
});

it('creates a quantity for a valid variant payload', function () {
    actingAsInventoryQuantityApiUser(['create_inventory_quantity']);

    $parentProduct = Product::factory()->create([
        'is_configurable' => true,
        'is_storable'     => true,
        'tracking'        => ProductTracking::LOT,
    ]);

    $variantProduct = Product::factory()->create([
        'parent_id'       => $parentProduct->id,
        'is_configurable' => false,
        'is_storable'     => true,
        'tracking'        => ProductTracking::LOT,
    ]);

    $location = Location::factory()->create(['type' => LocationType::INTERNAL]);
    $lot = Lot::factory()->create(['product_id' => $variantProduct->id]);
    $package = Package::factory()->create(['location_id' => $location->id]);

    $payload = inventoryQuantityPayload($variantProduct, $location, [
        'lot_id'      => $lot->id,
        'package_id'  => $package->id,
    ]);

    $this->postJson(inventoryQuantityRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Quantity created successfully.');
});

it('rejects configurable products and requires variant products', function () {
    actingAsInventoryQuantityApiUser(['create_inventory_quantity']);

    $configurableProduct = Product::factory()->create([
        'is_configurable' => true,
        'is_storable'     => true,
        'tracking'        => ProductTracking::QTY,
    ]);

    $location = Location::factory()->create(['type' => LocationType::INTERNAL]);

    $this->postJson(inventoryQuantityRoute('store'), inventoryQuantityPayload($configurableProduct, $location))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['product_id']);
});

it('requires lot_id for tracked products', function () {
    actingAsInventoryQuantityApiUser(['create_inventory_quantity']);

    $trackedProduct = Product::factory()->create([
        'is_configurable' => false,
        'is_storable'     => true,
        'tracking'        => ProductTracking::LOT,
    ]);

    $location = Location::factory()->create(['type' => LocationType::INTERNAL]);

    $this->postJson(inventoryQuantityRoute('store'), inventoryQuantityPayload($trackedProduct, $location, [
        'lot_id' => null,
    ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['lot_id']);
});

it('rejects lots that do not belong to the selected product', function () {
    actingAsInventoryQuantityApiUser(['create_inventory_quantity']);

    $product = Product::factory()->create([
        'is_configurable' => false,
        'is_storable'     => true,
        'tracking'        => ProductTracking::LOT,
    ]);

    $otherProduct = Product::factory()->create([
        'is_configurable' => false,
        'is_storable'     => true,
        'tracking'        => ProductTracking::LOT,
    ]);

    $location = Location::factory()->create(['type' => LocationType::INTERNAL]);
    $otherProductLot = Lot::factory()->create(['product_id' => $otherProduct->id]);

    $this->postJson(inventoryQuantityRoute('store'), inventoryQuantityPayload($product, $location, [
        'lot_id' => $otherProductLot->id,
    ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['lot_id']);
});

it('rejects packages that do not belong to the selected location', function () {
    actingAsInventoryQuantityApiUser(['create_inventory_quantity']);

    $product = Product::factory()->create([
        'is_configurable' => false,
        'is_storable'     => true,
        'tracking'        => ProductTracking::QTY,
    ]);

    $sourceLocation = Location::factory()->create(['type' => LocationType::INTERNAL]);
    $otherLocation = Location::factory()->create(['type' => LocationType::INTERNAL]);
    $otherLocationPackage = Package::factory()->create(['location_id' => $otherLocation->id]);

    $this->postJson(inventoryQuantityRoute('store'), inventoryQuantityPayload($product, $sourceLocation, [
        'package_id' => $otherLocationPackage->id,
    ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['package_id']);
});

it('rejects counted_quantity greater than one for serial tracked products', function () {
    actingAsInventoryQuantityApiUser(['create_inventory_quantity']);

    $serialProduct = Product::factory()->create([
        'is_configurable' => false,
        'is_storable'     => true,
        'tracking'        => ProductTracking::SERIAL,
    ]);

    $location = Location::factory()->create(['type' => LocationType::INTERNAL]);
    $lot = Lot::factory()->create(['product_id' => $serialProduct->id]);

    $this->postJson(inventoryQuantityRoute('store'), inventoryQuantityPayload($serialProduct, $location, [
        'lot_id'           => $lot->id,
        'counted_quantity' => 2,
    ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['counted_quantity']);
});
