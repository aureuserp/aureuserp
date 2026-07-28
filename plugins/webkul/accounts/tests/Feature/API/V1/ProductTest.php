<?php

use Webkul\Account\Models\Account;
use Webkul\Account\Models\Product;
use Webkul\Product\Models\Category;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UOM;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PRODUCT_JSON_STRUCTURE = [
    'id',
    'type',
    'name',
    'price',
    'category_id',
    'sales_ok',
    'purchase_ok',
    'created_at',
    'updated_at',
];

const PRODUCT_REQUIRED_FIELDS = [
    'type',
    'name',
    'price',
    'category_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingWithProductPermissions(array $permissions): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function productRoute(string $action, mixed $product = null): string
{
    $name = "admin.api.v1.accounts.products.{$action}";

    return $product ? route($name, $product) : route($name);
}

function productPayload(array $overrides = []): array
{
    $uom = UOM::factory()->create();
    $category = Category::factory()->create();

    return array_replace_recursive([
        'type'        => 'goods',
        'name'        => 'Test Product',
        'price'       => 99.99,
        'uom_id'      => $uom->id,
        'uom_po_id'   => $uom->id,
        'category_id' => $category->id,
    ], $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list products', function () {
    $this->getJson(productRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a product', function () {
    $this->postJson(productRoute('store'), [])
        ->assertUnauthorized();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('forbids listing products without permission', function () {
    actingWithProductPermissions([]);

    $this->getJson(productRoute('index'))
        ->assertForbidden();
});

it('lists products for authorized users', function () {
    actingWithProductPermissions(['view_any_account_product']);

    Product::factory()->count(3)->create();

    $this->getJson(productRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters products by type', function () {
    actingWithProductPermissions(['view_any_account_product']);

    $product = Product::factory()->create(['type' => 'goods']);
    Product::factory()->count(2)->create(['type' => 'service']);

    $response = $this->getJson(productRoute('index').'?filter[type]=goods')
        ->assertOk();

    collect($response->json('data'))->each(function ($item) {
        expect($item['type'])->toBe('goods');
    });

    expect(collect($response->json('data'))->firstWhere('id', $product->id))->not->toBeNull();
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('forbids creating a product without permission', function () {
    actingWithProductPermissions([]);

    $this->postJson(productRoute('store'), productPayload())
        ->assertForbidden();
});

it('creates a product', function () {
    actingWithProductPermissions(['create_account_product']);

    $payload = productPayload();

    $this->postJson(productRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Product created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonPath('data.type', $payload['type'])
        ->assertJsonStructure(['data' => PRODUCT_JSON_STRUCTURE]);

    $this->assertDatabaseHas('products_products', [
        'name'        => $payload['name'],
        'category_id' => $payload['category_id'],
    ]);
});

it('creates a product with account-specific income and expense accounts', function () {
    actingWithProductPermissions(['create_account_product']);

    $incomeAccount = Account::factory()->create();
    $expenseAccount = Account::factory()->create();

    $payload = productPayload([
        'property_account_income_id'  => $incomeAccount->id,
        'property_account_expense_id' => $expenseAccount->id,
        'invoice_policy'              => 'order',
        'sales_ok'                    => true,
        'purchase_ok'                 => true,
    ]);

    $this->postJson(productRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('data.property_account_income_id', $incomeAccount->id)
        ->assertJsonPath('data.property_account_expense_id', $expenseAccount->id)
        ->assertJsonPath('data.invoice_policy', 'order')
        ->assertJsonPath('data.sales_ok', true)
        ->assertJsonPath('data.purchase_ok', true);

    $this->assertDatabaseHas('products_products', [
        'name'                        => $payload['name'],
        'property_account_income_id'  => $incomeAccount->id,
        'property_account_expense_id' => $expenseAccount->id,
        'invoice_policy'              => 'order',
        'sales_ok'                    => true,
        'purchase_ok'                 => true,
    ]);
});

it('validates required fields when creating a product', function (string $field) {
    actingWithProductPermissions(['create_account_product']);

    $payload = productPayload();
    unset($payload[$field]);

    $this->postJson(productRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(PRODUCT_REQUIRED_FIELDS);

it('validates invoice_policy accepts only allowed values', function () {
    actingWithProductPermissions(['create_account_product']);

    $this->postJson(productRoute('store'), productPayload(['invoice_policy' => 'invalid']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['invoice_policy']);
});

// ── Show ───────────────────────────────────────────────────────────────────────

it('forbids showing a product without permission', function () {
    actingWithProductPermissions([]);

    $product = Product::factory()->create();

    $this->getJson(productRoute('show', $product))
        ->assertForbidden();
});

it('shows a product for authorized users', function () {
    actingWithProductPermissions(['view_account_product']);

    $product = Product::factory()->create();

    $this->getJson(productRoute('show', $product))
        ->assertOk()
        ->assertJsonPath('data.id', $product->id)
        ->assertJsonStructure(['data' => PRODUCT_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent product', function () {
    actingWithProductPermissions(['view_account_product']);

    $this->getJson(productRoute('show', 999999))
        ->assertNotFound();
});

it('shows account-specific fields on a product', function () {
    actingWithProductPermissions(['view_account_product']);

    $incomeAccount = Account::factory()->create();
    $expenseAccount = Account::factory()->create();

    $product = Product::factory()->create([
        'property_account_income_id'  => $incomeAccount->id,
        'property_account_expense_id' => $expenseAccount->id,
        'invoice_policy'              => 'delivery',
        'sales_ok'                    => true,
        'purchase_ok'                 => false,
    ]);

    $this->getJson(productRoute('show', $product))
        ->assertOk()
        ->assertJsonPath('data.invoice_policy', 'delivery')
        ->assertJsonPath('data.sales_ok', true)
        ->assertJsonPath('data.purchase_ok', false)
        ->assertJsonPath('data.property_account_income_id', $incomeAccount->id)
        ->assertJsonPath('data.property_account_expense_id', $expenseAccount->id);
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('forbids updating a product without permission', function () {
    actingWithProductPermissions([]);

    $product = Product::factory()->create();

    $this->patchJson(productRoute('update', $product), ['name' => 'Updated Product Name'])
        ->assertForbidden();
});

it('updates a product name', function () {
    actingWithProductPermissions(['update_account_product']);

    $product = Product::factory()->create();

    $this->patchJson(productRoute('update', $product), ['name' => 'Updated Product Name'])
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated Product Name');

    $this->assertDatabaseHas('products_products', [
        'id'   => $product->id,
        'name' => 'Updated Product Name',
    ]);
});

it('updates account-specific fields on a product', function () {
    actingWithProductPermissions(['update_account_product']);

    $product = Product::factory()->create();
    $incomeAccount = Account::factory()->create();
    $expenseAccount = Account::factory()->create();

    $this->patchJson(productRoute('update', $product), [
        'property_account_income_id'  => $incomeAccount->id,
        'property_account_expense_id' => $expenseAccount->id,
        'invoice_policy'              => 'delivery',
        'sales_ok'                    => false,
        'purchase_ok'                 => true,
    ])
        ->assertOk()
        ->assertJsonPath('data.property_account_income_id', $incomeAccount->id)
        ->assertJsonPath('data.property_account_expense_id', $expenseAccount->id)
        ->assertJsonPath('data.invoice_policy', 'delivery')
        ->assertJsonPath('data.sales_ok', false)
        ->assertJsonPath('data.purchase_ok', true);

    $this->assertDatabaseHas('products_products', [
        'id'                          => $product->id,
        'property_account_income_id'  => $incomeAccount->id,
        'property_account_expense_id' => $expenseAccount->id,
        'invoice_policy'              => 'delivery',
        'sales_ok'                    => false,
        'purchase_ok'                 => true,
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('forbids deleting a product without permission', function () {
    actingWithProductPermissions([]);

    $product = Product::factory()->create();

    $this->deleteJson(productRoute('destroy', $product))
        ->assertForbidden();
});

it('soft deletes a product', function () {
    actingWithProductPermissions(['delete_account_product']);

    $product = Product::factory()->create();

    $this->deleteJson(productRoute('destroy', $product))
        ->assertOk()
        ->assertJsonPath('message', 'Product deleted successfully.');

    $this->assertSoftDeleted('products_products', ['id' => $product->id]);
});

// ── Restore ────────────────────────────────────────────────────────────────────

it('forbids restoring a product without permission', function () {
    actingWithProductPermissions([]);

    $product = Product::factory()->create();
    $product->delete();

    $this->postJson(productRoute('restore', $product))
        ->assertForbidden();
});

it('restores a soft-deleted product', function () {
    actingWithProductPermissions(['restore_account_product']);

    $product = Product::factory()->create();
    $product->delete();

    $this->postJson(productRoute('restore', $product))
        ->assertOk()
        ->assertJsonPath('message', 'Product restored successfully.');

    $this->assertDatabaseHas('products_products', [
        'id'         => $product->id,
        'deleted_at' => null,
    ]);
});

// ── Force Delete ───────────────────────────────────────────────────────────────

it('forbids permanently deleting a product without permission', function () {
    actingWithProductPermissions([]);

    $product = Product::factory()->create();
    $product->delete();

    $this->deleteJson(productRoute('force-destroy', $product))
        ->assertForbidden();
});

it('permanently deletes a product', function () {
    actingWithProductPermissions(['force_delete_account_product']);

    $product = Product::factory()->create();
    $product->delete();

    $this->deleteJson(productRoute('force-destroy', $product))
        ->assertOk()
        ->assertJsonPath('message', 'Product permanently deleted.');

    $this->assertDatabaseMissing('products_products', ['id' => $product->id]);
});
