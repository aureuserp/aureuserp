<?php

use Webkul\Product\Enums\AttributeType;
use Webkul\Product\Models\Attribute;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PRODUCTS_ATTRIBUTE_JSON_STRUCTURE = [
    'id',
    'name',
    'type',
];

const PRODUCTS_ATTRIBUTE_REQUIRED_FIELDS = [
    'name',
    'type',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsProductsAttributeApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function productsAttributeRoute(string $action, mixed $attribute = null): string
{
    $name = "admin.api.v1.products.attributes.{$action}";

    return $attribute ? route($name, $attribute) : route($name);
}

function productsAttributePayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name' => 'Color',
        'type' => AttributeType::RADIO->value,
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list attributes', function () {
    $this->getJson(productsAttributeRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create an attribute', function () {
    $this->postJson(productsAttributeRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show an attribute', function () {
    $attribute = Attribute::factory()->create();

    $this->getJson(productsAttributeRoute('show', $attribute))
        ->assertUnauthorized();
});

it('requires authentication to update an attribute', function () {
    $attribute = Attribute::factory()->create();

    $this->patchJson(productsAttributeRoute('update', $attribute), [])
        ->assertUnauthorized();
});

it('requires authentication to delete an attribute', function () {
    $attribute = Attribute::factory()->create();

    $this->deleteJson(productsAttributeRoute('destroy', $attribute))
        ->assertUnauthorized();
});

it('requires authentication to restore an attribute', function () {
    $attribute = Attribute::factory()->create();
    $attribute->delete();

    $this->postJson(productsAttributeRoute('restore', $attribute))
        ->assertUnauthorized();
});

it('requires authentication to force-delete an attribute', function () {
    $attribute = Attribute::factory()->create();
    $attribute->delete();

    $this->deleteJson(productsAttributeRoute('force-destroy', $attribute))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing attributes without permission', function () {
    actingAsProductsAttributeApiUser();

    $this->getJson(productsAttributeRoute('index'))
        ->assertForbidden();
});

it('forbids creating an attribute without permission', function () {
    actingAsProductsAttributeApiUser();

    $this->postJson(productsAttributeRoute('store'), productsAttributePayload())
        ->assertForbidden();
});

it('forbids showing an attribute without permission', function () {
    actingAsProductsAttributeApiUser();

    $attribute = Attribute::factory()->create();

    $this->getJson(productsAttributeRoute('show', $attribute))
        ->assertForbidden();
});

it('forbids updating an attribute without permission', function () {
    actingAsProductsAttributeApiUser();

    $attribute = Attribute::factory()->create();

    $this->patchJson(productsAttributeRoute('update', $attribute), [])
        ->assertForbidden();
});

it('forbids deleting an attribute without permission', function () {
    actingAsProductsAttributeApiUser();

    $attribute = Attribute::factory()->create();

    $this->deleteJson(productsAttributeRoute('destroy', $attribute))
        ->assertForbidden();
});

it('forbids restoring an attribute without permission', function () {
    actingAsProductsAttributeApiUser();

    $attribute = Attribute::factory()->create();
    $attribute->delete();

    $this->postJson(productsAttributeRoute('restore', $attribute))
        ->assertForbidden();
});

it('forbids force-deleting an attribute without permission', function () {
    actingAsProductsAttributeApiUser();

    $attribute = Attribute::factory()->create();
    $attribute->delete();

    $this->deleteJson(productsAttributeRoute('force-destroy', $attribute))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists attributes for authorized users', function () {
    actingAsProductsAttributeApiUser(['view_any_product_attribute']);

    Attribute::factory()->count(3)->create();

    $this->getJson(productsAttributeRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters attributes by name', function () {
    actingAsProductsAttributeApiUser(['view_any_product_attribute']);

    $attribute = Attribute::factory()->create(['name' => 'UniqueAttributeXYZ']);
    Attribute::factory()->count(2)->create();

    $response = $this->getJson(productsAttributeRoute('index').'?filter[name]=UniqueAttributeXYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($attribute->id);
});

it('filters attributes by type', function () {
    actingAsProductsAttributeApiUser(['view_any_product_attribute']);

    $radioAttr = Attribute::factory()->create(['type' => AttributeType::RADIO->value]);
    $selectAttr = Attribute::factory()->create(['type' => AttributeType::SELECT->value]);

    $response = $this->getJson(productsAttributeRoute('index').'?filter[type]='.AttributeType::RADIO->value)
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($radioAttr->id);
    collect($response->json('data'))->each(fn ($item) => expect($item['type'])->toBe(AttributeType::RADIO->value));
});

it('excludes soft-deleted attributes from default listing', function () {
    actingAsProductsAttributeApiUser(['view_any_product_attribute']);

    $active = Attribute::factory()->create();
    $deleted = Attribute::factory()->create();
    $deleted->delete();

    $response = $this->getJson(productsAttributeRoute('index'))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($active->id)
        ->and($ids)->not->toContain($deleted->id);
});

it('includes soft-deleted attributes when filter[trashed]=with', function () {
    actingAsProductsAttributeApiUser(['view_any_product_attribute']);

    $deleted = Attribute::factory()->create();
    $deleted->delete();

    $response = $this->getJson(productsAttributeRoute('index').'?filter[trashed]=with')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($deleted->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates an attribute', function () {
    actingAsProductsAttributeApiUser(['create_product_attribute']);

    $payload = productsAttributePayload();

    $this->postJson(productsAttributeRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Attribute created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonPath('data.type', $payload['type'])
        ->assertJsonStructure(['data' => PRODUCTS_ATTRIBUTE_JSON_STRUCTURE]);

    $this->assertDatabaseHas('products_attributes', [
        'name' => $payload['name'],
        'type' => $payload['type'],
    ]);
});

it('validates required fields when creating an attribute', function (string $field) {
    actingAsProductsAttributeApiUser(['create_product_attribute']);

    $payload = productsAttributePayload();
    unset($payload[$field]);

    $this->postJson(productsAttributeRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(PRODUCTS_ATTRIBUTE_REQUIRED_FIELDS);

it('rejects an invalid attribute type', function () {
    actingAsProductsAttributeApiUser(['create_product_attribute']);

    $this->postJson(productsAttributeRoute('store'), productsAttributePayload(['type' => 'invalid']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows an attribute for authorized users', function () {
    actingAsProductsAttributeApiUser(['view_product_attribute']);

    $attribute = Attribute::factory()->create();

    $this->getJson(productsAttributeRoute('show', $attribute))
        ->assertOk()
        ->assertJsonPath('data.id', $attribute->id)
        ->assertJsonPath('data.name', $attribute->name)
        ->assertJsonStructure(['data' => PRODUCTS_ATTRIBUTE_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent attribute', function () {
    actingAsProductsAttributeApiUser(['view_product_attribute']);

    $this->getJson(productsAttributeRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates an attribute', function () {
    actingAsProductsAttributeApiUser(['update_product_attribute']);

    $attribute = Attribute::factory()->create();

    $this->patchJson(productsAttributeRoute('update', $attribute), ['name' => 'Updated Attribute'])
        ->assertOk()
        ->assertJsonPath('message', 'Attribute updated successfully.')
        ->assertJsonPath('data.name', 'Updated Attribute');

    $this->assertDatabaseHas('products_attributes', [
        'id'   => $attribute->id,
        'name' => 'Updated Attribute',
    ]);
});

it('returns 404 when updating a non-existent attribute', function () {
    actingAsProductsAttributeApiUser(['update_product_attribute']);

    $this->patchJson(productsAttributeRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy (Soft Delete) ─────────────────────────────────────────────────────

it('soft deletes an attribute', function () {
    actingAsProductsAttributeApiUser(['delete_product_attribute']);

    $attribute = Attribute::factory()->create();

    $this->deleteJson(productsAttributeRoute('destroy', $attribute))
        ->assertOk()
        ->assertJsonPath('message', 'Attribute deleted successfully.');

    $this->assertSoftDeleted('products_attributes', ['id' => $attribute->id]);
});

it('returns 404 when deleting a non-existent attribute', function () {
    actingAsProductsAttributeApiUser(['delete_product_attribute']);

    $this->deleteJson(productsAttributeRoute('destroy', 999999))
        ->assertNotFound();
});

// ── Restore ───────────────────────────────────────────────────────────────────

it('restores a soft-deleted attribute', function () {
    actingAsProductsAttributeApiUser(['restore_product_attribute']);

    $attribute = Attribute::factory()->create();
    $attribute->delete();

    $this->postJson(productsAttributeRoute('restore', $attribute))
        ->assertOk()
        ->assertJsonPath('message', 'Attribute restored successfully.');

    $this->assertDatabaseHas('products_attributes', [
        'id'         => $attribute->id,
        'deleted_at' => null,
    ]);
});

it('returns 404 when restoring a non-existent attribute', function () {
    actingAsProductsAttributeApiUser(['restore_product_attribute']);

    $this->postJson(productsAttributeRoute('restore', 999999))
        ->assertNotFound();
});

// ── Force Delete ──────────────────────────────────────────────────────────────

it('permanently deletes an attribute', function () {
    actingAsProductsAttributeApiUser(['force_delete_product_attribute']);

    $attribute = Attribute::factory()->create();
    $attribute->delete();

    $this->deleteJson(productsAttributeRoute('force-destroy', $attribute))
        ->assertOk()
        ->assertJsonPath('message', 'Attribute permanently deleted.');

    $this->assertDatabaseMissing('products_attributes', ['id' => $attribute->id]);
});

it('returns 404 when force-deleting a non-existent attribute', function () {
    actingAsProductsAttributeApiUser(['force_delete_product_attribute']);

    $this->deleteJson(productsAttributeRoute('force-destroy', 999999))
        ->assertNotFound();
});
