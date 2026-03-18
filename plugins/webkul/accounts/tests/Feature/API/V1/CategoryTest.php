<?php

use Webkul\Account\Models\Category;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const CATEGORY_JSON_STRUCTURE = [
    'id',
    'name',
];

const CATEGORY_REQUIRED_FIELDS = [
    'name',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsCategoryApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function categoryRoute(string $action, mixed $category = null): string
{
    $name = "admin.api.v1.accounts.categories.{$action}";

    return $category ? route($name, $category) : route($name);
}

function categoryPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name' => 'Office Supplies',
    ], $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list categories', function () {
    $this->getJson(categoryRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a category', function () {
    $this->postJson(categoryRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing categories without permission', function () {
    actingAsCategoryApiUser();

    $this->getJson(categoryRoute('index'))
        ->assertForbidden();
});

it('forbids creating a category without permission', function () {
    actingAsCategoryApiUser();

    $this->postJson(categoryRoute('store'), categoryPayload())
        ->assertForbidden();
});

it('forbids updating a category without permission', function () {
    actingAsCategoryApiUser();

    $category = Category::factory()->create();

    $this->patchJson(categoryRoute('update', $category), [])
        ->assertForbidden();
});

it('forbids deleting a category without permission', function () {
    actingAsCategoryApiUser();

    $category = Category::factory()->create();

    $this->deleteJson(categoryRoute('destroy', $category))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists categories for authorized users', function () {
    actingAsCategoryApiUser(['view_any_account_product::category']);

    Category::factory()->count(3)->create();

    $this->getJson(categoryRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters categories by name', function () {
    actingAsCategoryApiUser(['view_any_account_product::category']);

    $category = Category::factory()->create(['name' => 'Unique Category Name']);
    Category::factory()->count(2)->create();

    $response = $this->getJson(categoryRoute('index').'?filter[name]=Unique')
        ->assertOk();

    collect($response->json('data'))->each(function ($item) {
        expect($item['name'])->toContain('Unique');
    });

    expect(collect($response->json('data'))->firstWhere('id', $category->id))->not->toBeNull();
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a category', function () {
    actingAsCategoryApiUser(['create_account_product::category']);

    $payload = categoryPayload();

    $this->postJson(categoryRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Category created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => CATEGORY_JSON_STRUCTURE]);

    $this->assertDatabaseHas('products_categories', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a category', function (string $field) {
    actingAsCategoryApiUser(['create_account_product::category']);

    $payload = categoryPayload();
    unset($payload[$field]);

    $this->postJson(categoryRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(CATEGORY_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a category for authorized users', function () {
    actingAsCategoryApiUser(['view_account_product::category']);

    $category = Category::factory()->create();

    $this->getJson(categoryRoute('show', $category))
        ->assertOk()
        ->assertJsonPath('data.id', $category->id)
        ->assertJsonStructure(['data' => CATEGORY_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent category', function () {
    actingAsCategoryApiUser(['view_account_product::category']);

    $this->getJson(categoryRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a category', function () {
    actingAsCategoryApiUser(['update_account_product::category']);

    $category = Category::factory()->create();

    $this->patchJson(categoryRoute('update', $category), ['name' => 'Updated Category'])
        ->assertOk()
        ->assertJsonPath('message', 'Category updated successfully.')
        ->assertJsonPath('data.name', 'Updated Category');

    $this->assertDatabaseHas('products_categories', [
        'id'   => $category->id,
        'name' => 'Updated Category',
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes a category', function () {
    actingAsCategoryApiUser(['delete_account_product::category']);

    $category = Category::factory()->create();

    $this->deleteJson(categoryRoute('destroy', $category))
        ->assertOk()
        ->assertJsonPath('message', 'Category deleted successfully.');

    $this->assertDatabaseMissing('products_categories', ['id' => $category->id]);
});
