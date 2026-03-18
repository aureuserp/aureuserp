<?php

use Webkul\Product\Models\Product;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PRODUCTS_PRODUCT_VARIANT_JSON_STRUCTURE = [
    'id',
    'type',
    'name',
    'price',
    'parent_id',
    'category_id',
    'created_at',
    'updated_at',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsProductsProductVariantApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function productsVariantRoute(string $action, mixed $product, mixed $variant = null): string
{
    $name = "admin.api.v1.products.products.variants.{$action}";

    if ($variant) {
        return route($name, [$product, $variant]);
    }

    return route($name, $product);
}

function createVariantProduct()
{
    return Product::factory()->create();
}

function createProductVariant($parent)
{
    return Product::factory()->create([
        'parent_id' => $parent->id,
    ]);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list product variants', function () {
    $parent = createVariantProduct();

    $this->getJson(productsVariantRoute('index', $parent))
        ->assertUnauthorized();
});

it('requires authentication to sync product variants', function () {
    $parent = createVariantProduct();

    $this->postJson(productsVariantRoute('store', $parent))
        ->assertUnauthorized();
});

it('requires authentication to show a product variant', function () {
    $parent = createVariantProduct();
    $variant = createProductVariant($parent);

    $this->getJson(productsVariantRoute('show', $parent, $variant))
        ->assertUnauthorized();
});

it('requires authentication to update a product variant', function () {
    $parent = createVariantProduct();
    $variant = createProductVariant($parent);

    $this->patchJson(productsVariantRoute('update', $parent, $variant), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a product variant', function () {
    $parent = createVariantProduct();
    $variant = createProductVariant($parent);

    $this->deleteJson(productsVariantRoute('destroy', $parent, $variant))
        ->assertUnauthorized();
});

it('requires authentication to restore a product variant', function () {
    $parent = createVariantProduct();
    $variant = createProductVariant($parent);
    $variant->delete();

    $this->postJson(productsVariantRoute('restore', $parent, $variant))
        ->assertUnauthorized();
});

it('requires authentication to force-delete a product variant', function () {
    $parent = createVariantProduct();
    $variant = createProductVariant($parent);
    $variant->delete();

    $this->deleteJson(productsVariantRoute('force-destroy', $parent, $variant))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing product variants without permission', function () {
    actingAsProductsProductVariantApiUser();

    $parent = createVariantProduct();

    $this->getJson(productsVariantRoute('index', $parent))
        ->assertForbidden();
});

it('forbids syncing product variants without permission', function () {
    actingAsProductsProductVariantApiUser();

    $parent = createVariantProduct();

    $this->postJson(productsVariantRoute('store', $parent))
        ->assertForbidden();
});

it('forbids showing a product variant without permission', function () {
    actingAsProductsProductVariantApiUser();

    $parent = createVariantProduct();
    $variant = createProductVariant($parent);

    $this->getJson(productsVariantRoute('show', $parent, $variant))
        ->assertForbidden();
});

it('forbids updating a product variant without permission', function () {
    actingAsProductsProductVariantApiUser();

    $parent = createVariantProduct();
    $variant = createProductVariant($parent);

    $this->patchJson(productsVariantRoute('update', $parent, $variant), [])
        ->assertForbidden();
});

it('forbids deleting a product variant without permission', function () {
    actingAsProductsProductVariantApiUser();

    $parent = createVariantProduct();
    $variant = createProductVariant($parent);

    $this->deleteJson(productsVariantRoute('destroy', $parent, $variant))
        ->assertForbidden();
});

it('forbids restoring a product variant without permission', function () {
    actingAsProductsProductVariantApiUser();

    $parent = createVariantProduct();
    $variant = createProductVariant($parent);
    $variant->delete();

    $this->postJson(productsVariantRoute('restore', $parent, $variant))
        ->assertForbidden();
});

it('forbids force-deleting a product variant without permission', function () {
    actingAsProductsProductVariantApiUser();

    $parent = createVariantProduct();
    $variant = createProductVariant($parent);
    $variant->delete();

    $this->deleteJson(productsVariantRoute('force-destroy', $parent, $variant))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists product variants for authorized users', function () {
    actingAsProductsProductVariantApiUser(['view_any_product_product']);

    $parent = createVariantProduct();
    createProductVariant($parent);
    createProductVariant($parent);

    $this->getJson(productsVariantRoute('index', $parent))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('only returns variants belonging to the given parent product', function () {
    actingAsProductsProductVariantApiUser(['view_any_product_product']);

    $parent = createVariantProduct();
    $otherParent = createVariantProduct();

    $ownVariant = createProductVariant($parent);
    $otherVariant = createProductVariant($otherParent);

    $response = $this->getJson(productsVariantRoute('index', $parent))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($ownVariant->id)
        ->and($ids)->not->toContain($otherVariant->id);
});

it('excludes soft-deleted variants from default listing', function () {
    actingAsProductsProductVariantApiUser(['view_any_product_product']);

    $parent = createVariantProduct();
    $activeVariant = createProductVariant($parent);
    $deletedVariant = createProductVariant($parent);
    $deletedVariant->delete();

    $response = $this->getJson(productsVariantRoute('index', $parent))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($activeVariant->id)
        ->and($ids)->not->toContain($deletedVariant->id);
});

it('includes soft-deleted variants with filter[trashed]=with', function () {
    actingAsProductsProductVariantApiUser(['view_any_product_product']);

    $parent = createVariantProduct();
    $deletedVariant = createProductVariant($parent);
    $deletedVariant->delete();

    $response = $this->getJson(productsVariantRoute('index', $parent).'?filter[trashed]=with')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($deletedVariant->id);
});

// ── Store (Sync Variants) ─────────────────────────────────────────────────────

it('syncs product variants for authorized users', function () {
    actingAsProductsProductVariantApiUser(['create_product_product']);

    $parent = createVariantProduct();

    $this->postJson(productsVariantRoute('store', $parent))
        ->assertOk()
        ->assertJsonPath('message', 'Product variants synced successfully.');
});

it('returns 404 when syncing variants for a non-existent product', function () {
    actingAsProductsProductVariantApiUser(['create_product_product']);

    $this->postJson(productsVariantRoute('store', 999999))
        ->assertNotFound();
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a product variant for authorized users', function () {
    actingAsProductsProductVariantApiUser(['view_product_product']);

    $parent = createVariantProduct();
    $variant = createProductVariant($parent);

    $this->getJson(productsVariantRoute('show', $parent, $variant))
        ->assertOk()
        ->assertJsonPath('data.id', $variant->id)
        ->assertJsonPath('data.parent_id', $parent->id)
        ->assertJsonStructure(['data' => PRODUCTS_PRODUCT_VARIANT_JSON_STRUCTURE]);
});

it('returns 404 for a variant not belonging to the given parent', function () {
    actingAsProductsProductVariantApiUser(['view_product_product']);

    $parent = createVariantProduct();
    $otherParent = createVariantProduct();
    $variant = createProductVariant($otherParent);

    $this->getJson(productsVariantRoute('show', $parent, $variant))
        ->assertNotFound();
});

it('returns 404 for a non-existent variant', function () {
    actingAsProductsProductVariantApiUser(['view_product_product']);

    $parent = createVariantProduct();

    $this->getJson(productsVariantRoute('show', $parent, 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a product variant name', function () {
    actingAsProductsProductVariantApiUser(['update_product_product']);

    $parent = createVariantProduct();
    $variant = createProductVariant($parent);

    $this->patchJson(productsVariantRoute('update', $parent, $variant), ['name' => 'Updated Variant'])
        ->assertOk()
        ->assertJsonPath('message', 'Product variant updated successfully.')
        ->assertJsonPath('data.name', 'Updated Variant');

    $this->assertDatabaseHas('products_products', [
        'id'   => $variant->id,
        'name' => 'Updated Variant',
    ]);
});

it('returns 404 when updating a variant not belonging to the given parent', function () {
    actingAsProductsProductVariantApiUser(['update_product_product']);

    $parent = createVariantProduct();
    $otherParent = createVariantProduct();
    $variant = createProductVariant($otherParent);

    $this->patchJson(productsVariantRoute('update', $parent, $variant), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy (Soft Delete) ─────────────────────────────────────────────────────

it('soft deletes a product variant', function () {
    actingAsProductsProductVariantApiUser(['delete_product_product']);

    $parent = createVariantProduct();
    $variant = createProductVariant($parent);

    $this->deleteJson(productsVariantRoute('destroy', $parent, $variant))
        ->assertOk()
        ->assertJsonPath('message', 'Product variant deleted successfully.');

    $this->assertSoftDeleted('products_products', ['id' => $variant->id]);
});

it('returns 404 when deleting a variant not belonging to the given parent', function () {
    actingAsProductsProductVariantApiUser(['delete_product_product']);

    $parent = createVariantProduct();
    $otherParent = createVariantProduct();
    $variant = createProductVariant($otherParent);

    $this->deleteJson(productsVariantRoute('destroy', $parent, $variant))
        ->assertNotFound();
});

// ── Restore ───────────────────────────────────────────────────────────────────

it('restores a soft-deleted product variant', function () {
    actingAsProductsProductVariantApiUser(['restore_product_product']);

    $parent = createVariantProduct();
    $variant = createProductVariant($parent);
    $variant->delete();

    $this->postJson(productsVariantRoute('restore', $parent, $variant))
        ->assertOk()
        ->assertJsonPath('message', 'Product variant restored successfully.');

    $this->assertDatabaseHas('products_products', [
        'id'         => $variant->id,
        'deleted_at' => null,
    ]);
});

it('returns 404 when restoring a variant not belonging to the given parent', function () {
    actingAsProductsProductVariantApiUser(['restore_product_product']);

    $parent = createVariantProduct();
    $otherParent = createVariantProduct();
    $variant = createProductVariant($otherParent);
    $variant->delete();

    $this->postJson(productsVariantRoute('restore', $parent, $variant))
        ->assertNotFound();
});

// ── Force Delete ──────────────────────────────────────────────────────────────

it('permanently deletes a product variant', function () {
    actingAsProductsProductVariantApiUser(['force_delete_product_product']);

    $parent = createVariantProduct();
    $variant = createProductVariant($parent);
    $variant->delete();

    $this->deleteJson(productsVariantRoute('force-destroy', $parent, $variant))
        ->assertOk()
        ->assertJsonPath('message', 'Product variant permanently deleted.');

    $this->assertDatabaseMissing('products_products', ['id' => $variant->id]);
});

it('returns 404 when force-deleting a variant not belonging to the given parent', function () {
    actingAsProductsProductVariantApiUser(['force_delete_product_product']);

    $parent = createVariantProduct();
    $otherParent = createVariantProduct();
    $variant = createProductVariant($otherParent);
    $variant->delete();

    $this->deleteJson(productsVariantRoute('force-destroy', $parent, $variant))
        ->assertNotFound();
});
