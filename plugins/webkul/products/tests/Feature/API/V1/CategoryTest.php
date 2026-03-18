<?php

use Webkul\Product\Models\Category;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PRODUCTS_CATEGORY_JSON_STRUCTURE = [
    'id',
    'name',
];

const PRODUCTS_CATEGORY_REQUIRED_FIELDS = [
    'name',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsProductsCategoryApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function productsCategoryRoute(string $action, mixed $category = null): string
{
    $name = "admin.api.v1.products.categories.{$action}";

    return $category ? route($name, $category) : route($name);
}

function productsCategoryPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name' => 'Electronics',
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list product categories', function () {
    $this->getJson(productsCategoryRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a product category', function () {
    $this->postJson(productsCategoryRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a product category', function () {
    $category = Category::factory()->create();

    $this->getJson(productsCategoryRoute('show', $category))
        ->assertUnauthorized();
});

it('requires authentication to update a product category', function () {
    $category = Category::factory()->create();

    $this->patchJson(productsCategoryRoute('update', $category), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a product category', function () {
    $category = Category::factory()->create();

    $this->deleteJson(productsCategoryRoute('destroy', $category))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing categories without permission', function () {
    actingAsProductsCategoryApiUser();

    $this->getJson(productsCategoryRoute('index'))
        ->assertForbidden();
});

it('forbids creating a category without permission', function () {
    actingAsProductsCategoryApiUser();

    $this->postJson(productsCategoryRoute('store'), productsCategoryPayload())
        ->assertForbidden();
});

it('forbids showing a category without permission', function () {
    actingAsProductsCategoryApiUser();

    $category = Category::factory()->create();

    $this->getJson(productsCategoryRoute('show', $category))
        ->assertForbidden();
});

it('forbids updating a category without permission', function () {
    actingAsProductsCategoryApiUser();

    $category = Category::factory()->create();

    $this->patchJson(productsCategoryRoute('update', $category), [])
        ->assertForbidden();
});

it('forbids deleting a category without permission', function () {
    actingAsProductsCategoryApiUser();

    $category = Category::factory()->create();

    $this->deleteJson(productsCategoryRoute('destroy', $category))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists product categories for authorized users', function () {
    actingAsProductsCategoryApiUser(['view_any_product_category']);

    Category::factory()->count(3)->create();

    $this->getJson(productsCategoryRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters categories by name', function () {
    actingAsProductsCategoryApiUser(['view_any_product_category']);

    $category = Category::factory()->create(['name' => 'Unique Electronics Name']);
    Category::factory()->count(2)->create();

    $response = $this->getJson(productsCategoryRoute('index').'?filter[name]=Unique Electronics')
        ->assertOk();

    collect($response->json('data'))->each(function ($item) {
        expect($item['name'])->toContain('Unique');
    });

    expect(collect($response->json('data'))->firstWhere('id', $category->id))->not->toBeNull();
});

it('filters categories by parent_id', function () {
    actingAsProductsCategoryApiUser(['view_any_product_category']);

    $parent = Category::factory()->create();
    $child = Category::factory()->create(['parent_id' => $parent->id]);
    Category::factory()->create();

    $response = $this->getJson(productsCategoryRoute('index')."?filter[parent_id]={$parent->id}")
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($child->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates a product category', function () {
    actingAsProductsCategoryApiUser(['create_product_category']);

    $payload = productsCategoryPayload();

    $this->postJson(productsCategoryRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Category created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => PRODUCTS_CATEGORY_JSON_STRUCTURE]);

    $this->assertDatabaseHas('products_categories', [
        'name' => $payload['name'],
    ]);
});

it('creates a category with a parent', function () {
    actingAsProductsCategoryApiUser(['create_product_category']);

    $parent = Category::factory()->create();
    $payload = productsCategoryPayload(['parent_id' => $parent->id]);

    $this->postJson(productsCategoryRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('data.parent_id', $parent->id);

    $this->assertDatabaseHas('products_categories', [
        'name'      => $payload['name'],
        'parent_id' => $parent->id,
    ]);
});

it('validates required fields when creating a category', function (string $field) {
    actingAsProductsCategoryApiUser(['create_product_category']);

    $payload = productsCategoryPayload();
    unset($payload[$field]);

    $this->postJson(productsCategoryRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(PRODUCTS_CATEGORY_REQUIRED_FIELDS);

it('rejects a non-existent parent_id when creating a category', function () {
    actingAsProductsCategoryApiUser(['create_product_category']);

    $this->postJson(productsCategoryRoute('store'), productsCategoryPayload(['parent_id' => 999999]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['parent_id']);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a product category for authorized users', function () {
    actingAsProductsCategoryApiUser(['view_product_category']);

    $category = Category::factory()->create();

    $this->getJson(productsCategoryRoute('show', $category))
        ->assertOk()
        ->assertJsonPath('data.id', $category->id)
        ->assertJsonPath('data.name', $category->name)
        ->assertJsonStructure(['data' => PRODUCTS_CATEGORY_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent product category', function () {
    actingAsProductsCategoryApiUser(['view_product_category']);

    $this->getJson(productsCategoryRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a product category', function () {
    actingAsProductsCategoryApiUser(['update_product_category']);

    $category = Category::factory()->create();

    $this->patchJson(productsCategoryRoute('update', $category), ['name' => 'Updated Category'])
        ->assertOk()
        ->assertJsonPath('message', 'Category updated successfully.')
        ->assertJsonPath('data.name', 'Updated Category');

    $this->assertDatabaseHas('products_categories', [
        'id'   => $category->id,
        'name' => 'Updated Category',
    ]);
});

it('ignores unknown fields when updating a category', function () {
    actingAsProductsCategoryApiUser(['update_product_category']);

    $category = Category::factory()->create();

    $this->patchJson(productsCategoryRoute('update', $category), [
        'name'    => 'Updated Name',
        'unknown' => 'ignored',
    ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated Name');
});

it('returns 404 when updating a non-existent category', function () {
    actingAsProductsCategoryApiUser(['update_product_category']);

    $this->patchJson(productsCategoryRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('deletes a product category', function () {
    actingAsProductsCategoryApiUser(['delete_product_category']);

    $category = Category::factory()->create();

    $this->deleteJson(productsCategoryRoute('destroy', $category))
        ->assertOk()
        ->assertJsonPath('message', 'Category deleted successfully.');

    $this->assertDatabaseMissing('products_categories', ['id' => $category->id]);
});

it('returns 404 when deleting a non-existent category', function () {
    actingAsProductsCategoryApiUser(['delete_product_category']);

    $this->deleteJson(productsCategoryRoute('destroy', 999999))
        ->assertNotFound();
});
