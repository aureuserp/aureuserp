<?php

use Webkul\Account\Models\Account;
use Webkul\Account\Models\Product;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PRODUCT_VARIANT_JSON_STRUCTURE = [
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
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingWithProductVariantPermissions(array $permissions): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function productVariantRoute(string $action, $parent, $variant = null): string
{
    $name = "admin.api.v1.accounts.products.variants.{$action}";

    if ($variant) {
        return route($name, [$parent, $variant]);
    }

    return route($name, $parent);
}

function createVariantParent()
{
    return Product::factory()->create();
}

function createVariant($parent)
{
    return Product::factory()->create([
        'parent_id' => $parent->id,
    ]);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list product variants', function () {
    $parent = createVariantParent();

    $this->getJson(productVariantRoute('index', $parent))
        ->assertUnauthorized();
});

it('requires authentication to sync product variants', function () {
    $parent = createVariantParent();

    $this->postJson(productVariantRoute('store', $parent))
        ->assertUnauthorized();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('forbids listing product variants without permission', function () {
    actingWithProductVariantPermissions([]);

    $parent = createVariantParent();

    $this->getJson(productVariantRoute('index', $parent))
        ->assertForbidden();
});

it('lists product variants for authorized users', function () {
    actingWithProductVariantPermissions(['view_any_account_product']);

    $parent = createVariantParent();
    createVariant($parent);
    createVariant($parent);

    $this->getJson(productVariantRoute('index', $parent))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('only returns variants belonging to the given parent product', function () {
    actingWithProductVariantPermissions(['view_any_account_product']);

    $parent = createVariantParent();
    $otherParent = createVariantParent();

    $ownVariant = createVariant($parent);
    $otherVariant = createVariant($otherParent);

    $response = $this->getJson(productVariantRoute('index', $parent))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($ownVariant->id)
        ->and($ids)->not->toContain($otherVariant->id);
});

// ── Store (Sync Variants) ──────────────────────────────────────────────────────

it('forbids syncing product variants without permission', function () {
    actingWithProductVariantPermissions([]);

    $parent = createVariantParent();

    $this->postJson(productVariantRoute('store', $parent))
        ->assertForbidden();
});

it('syncs product variants for authorized users', function () {
    actingWithProductVariantPermissions(['update_account_product']);

    $parent = createVariantParent();

    $this->postJson(productVariantRoute('store', $parent))
        ->assertOk()
        ->assertJsonPath('message', 'Product variants synced successfully.');
});

// ── Show ───────────────────────────────────────────────────────────────────────

it('forbids showing a product variant without permission', function () {
    actingWithProductVariantPermissions([]);

    $parent = createVariantParent();
    $variant = createVariant($parent);

    $this->getJson(productVariantRoute('show', $parent, $variant))
        ->assertForbidden();
});

it('shows a product variant for authorized users', function () {
    actingWithProductVariantPermissions(['view_account_product']);

    $parent = createVariantParent();
    $variant = createVariant($parent);

    $this->getJson(productVariantRoute('show', $parent, $variant))
        ->assertOk()
        ->assertJsonPath('data.id', $variant->id)
        ->assertJsonPath('data.parent_id', $parent->id)
        ->assertJsonStructure(['data' => PRODUCT_VARIANT_JSON_STRUCTURE]);
});

it('returns 404 for a variant not belonging to the given parent', function () {
    actingWithProductVariantPermissions(['view_account_product']);

    $parent = createVariantParent();
    $otherParent = createVariantParent();
    $variant = createVariant($otherParent);

    $this->getJson(productVariantRoute('show', $parent, $variant))
        ->assertNotFound();
});

it('shows account-specific fields on a product variant', function () {
    actingWithProductVariantPermissions(['view_account_product']);

    $parent = createVariantParent();
    $income = Account::factory()->create();
    $expense = Account::factory()->create();

    $variant = Product::factory()->create([
        'parent_id'                   => $parent->id,
        'property_account_income_id'  => $income->id,
        'property_account_expense_id' => $expense->id,
        'invoice_policy'              => 'delivery',
        'sales_ok'                    => true,
        'purchase_ok'                 => false,
    ]);

    $this->getJson(productVariantRoute('show', $parent, $variant))
        ->assertOk()
        ->assertJsonPath('data.property_account_income_id', $income->id)
        ->assertJsonPath('data.property_account_expense_id', $expense->id)
        ->assertJsonPath('data.invoice_policy', 'delivery')
        ->assertJsonPath('data.sales_ok', true)
        ->assertJsonPath('data.purchase_ok', false);
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('forbids updating a product variant without permission', function () {
    actingWithProductVariantPermissions([]);

    $parent = createVariantParent();
    $variant = createVariant($parent);

    $this->patchJson(productVariantRoute('update', $parent, $variant), ['name' => 'Updated Variant'])
        ->assertForbidden();
});

it('updates a product variant name', function () {
    actingWithProductVariantPermissions(['update_account_product']);

    $parent = createVariantParent();
    $variant = createVariant($parent);

    $this->patchJson(productVariantRoute('update', $parent, $variant), ['name' => 'Updated Variant'])
        ->assertOk()
        ->assertJsonPath('message', 'Product variant updated successfully.')
        ->assertJsonPath('data.name', 'Updated Variant');

    $this->assertDatabaseHas('products_products', [
        'id'   => $variant->id,
        'name' => 'Updated Variant',
    ]);
});

it('validates update payload for a product variant', function () {
    actingWithProductVariantPermissions(['update_account_product']);

    $parent = createVariantParent();
    $variant = createVariant($parent);

    $this->patchJson(productVariantRoute('update', $parent, $variant), [
        'name' => ['invalid'],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('updates account-specific fields on a product variant', function () {
    actingWithProductVariantPermissions(['update_account_product']);

    $parent = createVariantParent();
    $variant = createVariant($parent);
    $income = Account::factory()->create();
    $expense = Account::factory()->create();

    $this->patchJson(productVariantRoute('update', $parent, $variant), [
        'property_account_income_id'  => $income->id,
        'property_account_expense_id' => $expense->id,
        'invoice_policy'              => 'order',
        'sales_ok'                    => true,
        'purchase_ok'                 => false,
    ])
        ->assertOk()
        ->assertJsonPath('data.property_account_income_id', $income->id)
        ->assertJsonPath('data.property_account_expense_id', $expense->id)
        ->assertJsonPath('data.invoice_policy', 'order')
        ->assertJsonPath('data.sales_ok', true)
        ->assertJsonPath('data.purchase_ok', false);

    $this->assertDatabaseHas('products_products', [
        'id'                          => $variant->id,
        'property_account_income_id'  => $income->id,
        'property_account_expense_id' => $expense->id,
        'invoice_policy'              => 'order',
        'sales_ok'                    => true,
        'purchase_ok'                 => false,
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('forbids deleting a product variant without permission', function () {
    actingWithProductVariantPermissions([]);

    $parent = createVariantParent();
    $variant = createVariant($parent);

    $this->deleteJson(productVariantRoute('destroy', $parent, $variant))
        ->assertForbidden();
});

it('soft deletes a product variant', function () {
    actingWithProductVariantPermissions(['delete_account_product']);

    $parent = createVariantParent();
    $variant = createVariant($parent);

    $this->deleteJson(productVariantRoute('destroy', $parent, $variant))
        ->assertOk()
        ->assertJsonPath('message', 'Product variant deleted successfully.');

    $this->assertSoftDeleted('products_products', ['id' => $variant->id]);
});

// ── Restore ────────────────────────────────────────────────────────────────────

it('forbids restoring a product variant without permission', function () {
    actingWithProductVariantPermissions([]);

    $parent = createVariantParent();
    $variant = createVariant($parent);
    $variant->delete();

    $this->postJson(productVariantRoute('restore', $parent, $variant))
        ->assertForbidden();
});

it('restores a soft-deleted product variant', function () {
    actingWithProductVariantPermissions(['restore_account_product']);

    $parent = createVariantParent();
    $variant = createVariant($parent);
    $variant->delete();

    $this->postJson(productVariantRoute('restore', $parent, $variant))
        ->assertOk()
        ->assertJsonPath('message', 'Product variant restored successfully.');

    $this->assertDatabaseHas('products_products', [
        'id'         => $variant->id,
        'deleted_at' => null,
    ]);
});

// ── Force Delete ───────────────────────────────────────────────────────────────

it('forbids permanently deleting a product variant without permission', function () {
    actingWithProductVariantPermissions([]);

    $parent = createVariantParent();
    $variant = createVariant($parent);
    $variant->delete();

    $this->deleteJson(productVariantRoute('force-destroy', $parent, $variant))
        ->assertForbidden();
});

it('permanently deletes a product variant', function () {
    actingWithProductVariantPermissions(['force_delete_account_product']);

    $parent = createVariantParent();
    $variant = createVariant($parent);
    $variant->delete();

    $this->deleteJson(productVariantRoute('force-destroy', $parent, $variant))
        ->assertOk()
        ->assertJsonPath('message', 'Product variant permanently deleted.');

    $this->assertDatabaseMissing('products_products', ['id' => $variant->id]);
});
