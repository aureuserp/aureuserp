<?php

use Webkul\Product\Models\Attribute;
use Webkul\Product\Models\AttributeOption;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PRODUCTS_ATTRIBUTE_OPTION_JSON_STRUCTURE = [
    'id',
    'name',
    'attribute_id',
];

const PRODUCTS_ATTRIBUTE_OPTION_REQUIRED_FIELDS = [
    'name',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsProductsAttributeOptionApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function productsAttributeOptionRoute(string $action, mixed $attribute, mixed $option = null): string
{
    $name = "admin.api.v1.products.attributes.options.{$action}";

    if ($option) {
        return route($name, [$attribute, $option]);
    }

    return route($name, $attribute);
}

function productsAttributeOptionPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name' => 'Large',
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list attribute options', function () {
    $attribute = Attribute::factory()->create();

    $this->getJson(productsAttributeOptionRoute('index', $attribute))
        ->assertUnauthorized();
});

it('requires authentication to create an attribute option', function () {
    $attribute = Attribute::factory()->create();

    $this->postJson(productsAttributeOptionRoute('store', $attribute), [])
        ->assertUnauthorized();
});

it('requires authentication to show an attribute option', function () {
    $option = AttributeOption::factory()->create();

    $this->getJson(productsAttributeOptionRoute('show', $option->attribute_id, $option))
        ->assertUnauthorized();
});

it('requires authentication to update an attribute option', function () {
    $option = AttributeOption::factory()->create();

    $this->patchJson(productsAttributeOptionRoute('update', $option->attribute_id, $option), [])
        ->assertUnauthorized();
});

it('requires authentication to delete an attribute option', function () {
    $option = AttributeOption::factory()->create();

    $this->deleteJson(productsAttributeOptionRoute('destroy', $option->attribute_id, $option))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing attribute options without permission', function () {
    actingAsProductsAttributeOptionApiUser();

    $attribute = Attribute::factory()->create();

    $this->getJson(productsAttributeOptionRoute('index', $attribute))
        ->assertForbidden();
});

it('forbids creating an attribute option without permission', function () {
    actingAsProductsAttributeOptionApiUser();

    $attribute = Attribute::factory()->create();

    $this->postJson(productsAttributeOptionRoute('store', $attribute), productsAttributeOptionPayload())
        ->assertForbidden();
});

it('forbids creating an option with only view permission (needs update)', function () {
    actingAsProductsAttributeOptionApiUser(['view_product_attribute']);

    $attribute = Attribute::factory()->create();

    $this->postJson(productsAttributeOptionRoute('store', $attribute), productsAttributeOptionPayload())
        ->assertForbidden();
});

it('forbids showing an attribute option without permission', function () {
    actingAsProductsAttributeOptionApiUser();

    $option = AttributeOption::factory()->create();

    $this->getJson(productsAttributeOptionRoute('show', $option->attribute_id, $option))
        ->assertForbidden();
});

it('forbids updating an attribute option without permission', function () {
    actingAsProductsAttributeOptionApiUser();

    $option = AttributeOption::factory()->create();

    $this->patchJson(productsAttributeOptionRoute('update', $option->attribute_id, $option), [])
        ->assertForbidden();
});

it('forbids deleting an attribute option without permission', function () {
    actingAsProductsAttributeOptionApiUser();

    $option = AttributeOption::factory()->create();

    $this->deleteJson(productsAttributeOptionRoute('destroy', $option->attribute_id, $option))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists attribute options for authorized users', function () {
    actingAsProductsAttributeOptionApiUser(['view_product_attribute']);

    $attribute = Attribute::factory()->create();
    AttributeOption::factory()->count(3)->create(['attribute_id' => $attribute->id]);

    $this->getJson(productsAttributeOptionRoute('index', $attribute))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('only returns options belonging to the given attribute', function () {
    actingAsProductsAttributeOptionApiUser(['view_product_attribute']);

    $attribute = Attribute::factory()->create();
    $otherAttribute = Attribute::factory()->create();

    $ownOption = AttributeOption::factory()->create(['attribute_id' => $attribute->id]);
    $otherOption = AttributeOption::factory()->create(['attribute_id' => $otherAttribute->id]);

    $response = $this->getJson(productsAttributeOptionRoute('index', $attribute))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($ownOption->id)
        ->and($ids)->not->toContain($otherOption->id);
});

it('filters attribute options by name', function () {
    actingAsProductsAttributeOptionApiUser(['view_product_attribute']);

    $attribute = Attribute::factory()->create();
    $option = AttributeOption::factory()->create([
        'attribute_id' => $attribute->id,
        'name'         => 'UniqueOptionXYZ',
    ]);
    AttributeOption::factory()->count(2)->create(['attribute_id' => $attribute->id]);

    $response = $this->getJson(productsAttributeOptionRoute('index', $attribute).'?filter[name]=UniqueOptionXYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($option->id);
});

it('returns 404 when listing options for a non-existent attribute', function () {
    actingAsProductsAttributeOptionApiUser(['view_product_attribute']);

    $this->getJson(productsAttributeOptionRoute('index', 999999))
        ->assertNotFound();
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates an attribute option', function () {
    actingAsProductsAttributeOptionApiUser(['update_product_attribute']);

    $attribute = Attribute::factory()->create();
    $payload = productsAttributeOptionPayload();

    $this->postJson(productsAttributeOptionRoute('store', $attribute), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Attribute option created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => PRODUCTS_ATTRIBUTE_OPTION_JSON_STRUCTURE]);

    $this->assertDatabaseHas('products_attribute_options', [
        'name'         => $payload['name'],
        'attribute_id' => $attribute->id,
    ]);
});

it('creates an attribute option with extra_price', function () {
    actingAsProductsAttributeOptionApiUser(['update_product_attribute']);

    $attribute = Attribute::factory()->create();

    $this->postJson(productsAttributeOptionRoute('store', $attribute), productsAttributeOptionPayload([
        'extra_price' => 9.99,
        'color'       => '#AABBCC',
    ]))
        ->assertCreated()
        ->assertJsonPath('data.extra_price', 9.99);
});

it('validates required fields when creating an attribute option', function (string $field) {
    actingAsProductsAttributeOptionApiUser(['update_product_attribute']);

    $attribute = Attribute::factory()->create();
    $payload = productsAttributeOptionPayload();
    unset($payload[$field]);

    $this->postJson(productsAttributeOptionRoute('store', $attribute), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(PRODUCTS_ATTRIBUTE_OPTION_REQUIRED_FIELDS);

it('returns 404 when creating an option for a non-existent attribute', function () {
    actingAsProductsAttributeOptionApiUser(['update_product_attribute']);

    $this->postJson(productsAttributeOptionRoute('store', 999999), productsAttributeOptionPayload())
        ->assertNotFound();
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows an attribute option for authorized users', function () {
    actingAsProductsAttributeOptionApiUser(['view_product_attribute']);

    $option = AttributeOption::factory()->create();

    $this->getJson(productsAttributeOptionRoute('show', $option->attribute_id, $option))
        ->assertOk()
        ->assertJsonPath('data.id', $option->id)
        ->assertJsonPath('data.name', $option->name)
        ->assertJsonPath('data.attribute_id', $option->attribute_id)
        ->assertJsonStructure(['data' => PRODUCTS_ATTRIBUTE_OPTION_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent attribute option', function () {
    actingAsProductsAttributeOptionApiUser(['view_product_attribute']);

    $attribute = Attribute::factory()->create();

    $this->getJson(productsAttributeOptionRoute('show', $attribute, 999999))
        ->assertNotFound();
});

it('returns 404 when option does not belong to the given attribute', function () {
    actingAsProductsAttributeOptionApiUser(['view_product_attribute']);

    $attribute = Attribute::factory()->create();
    $otherAttribute = Attribute::factory()->create();
    $option = AttributeOption::factory()->create(['attribute_id' => $otherAttribute->id]);

    $this->getJson(productsAttributeOptionRoute('show', $attribute, $option))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates an attribute option', function () {
    actingAsProductsAttributeOptionApiUser(['update_product_attribute']);

    $option = AttributeOption::factory()->create();

    $this->patchJson(productsAttributeOptionRoute('update', $option->attribute_id, $option), ['name' => 'Updated Option'])
        ->assertOk()
        ->assertJsonPath('message', 'Attribute option updated successfully.')
        ->assertJsonPath('data.name', 'Updated Option');

    $this->assertDatabaseHas('products_attribute_options', [
        'id'   => $option->id,
        'name' => 'Updated Option',
    ]);
});

it('returns 404 when updating an option for a non-existent attribute', function () {
    actingAsProductsAttributeOptionApiUser(['update_product_attribute']);

    $this->patchJson(productsAttributeOptionRoute('update', 999999, 1), ['name' => 'X'])
        ->assertNotFound();
});

it('returns 404 when updating a non-existent attribute option', function () {
    actingAsProductsAttributeOptionApiUser(['update_product_attribute']);

    $attribute = Attribute::factory()->create();

    $this->patchJson(productsAttributeOptionRoute('update', $attribute, 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('deletes an attribute option', function () {
    actingAsProductsAttributeOptionApiUser(['update_product_attribute']);

    $option = AttributeOption::factory()->create();

    $this->deleteJson(productsAttributeOptionRoute('destroy', $option->attribute_id, $option))
        ->assertOk()
        ->assertJsonPath('message', 'Attribute option deleted successfully.');

    $this->assertDatabaseMissing('products_attribute_options', ['id' => $option->id]);
});

it('returns 404 when deleting a non-existent attribute option', function () {
    actingAsProductsAttributeOptionApiUser(['update_product_attribute']);

    $attribute = Attribute::factory()->create();

    $this->deleteJson(productsAttributeOptionRoute('destroy', $attribute, 999999))
        ->assertNotFound();
});

it('returns 404 when deleting an option that does not belong to the given attribute', function () {
    actingAsProductsAttributeOptionApiUser(['update_product_attribute']);

    $attribute = Attribute::factory()->create();
    $otherAttribute = Attribute::factory()->create();
    $option = AttributeOption::factory()->create(['attribute_id' => $otherAttribute->id]);

    $this->deleteJson(productsAttributeOptionRoute('destroy', $attribute, $option))
        ->assertNotFound();
});
