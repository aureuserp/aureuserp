<?php

use Webkul\Support\Models\UOMCategory;

require_once __DIR__.'/../../../Helpers/SecurityHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

const UOM_CATEGORY_JSON_STRUCTURE = [
    'id',
    'name',
    'created_at',
    'updated_at',
];

beforeEach(function () {
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsUomCategoryApiUser(array $permissions): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function uomCategoryRoute(string $action, mixed $category = null): string
{
    $name = "admin.api.v1.support.uom-categories.{$action}";

    return $category ? route($name, $category) : route($name);
}

it('requires authentication to list uom categories', function () {
    $this->getJson(uomCategoryRoute('index'))
        ->assertUnauthorized();
});

it('forbids listing uom categories without permission', function () {
    actingAsUomCategoryApiUser([]);

    $this->getJson(uomCategoryRoute('index'))
        ->assertForbidden();
});

it('lists uom categories for authorized users', function () {
    actingAsUomCategoryApiUser(['view_any_support_u::o::m::category']);

    UOMCategory::factory()->count(2)->create();

    $this->getJson(uomCategoryRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => ['*' => UOM_CATEGORY_JSON_STRUCTURE]]);
});

it('creates a uom category with valid payload', function () {
    actingAsUomCategoryApiUser(['create_support_u::o::m::category']);

    $payload = UOMCategory::factory()->make()->toArray();

    $this->postJson(uomCategoryRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'UOM category created successfully.')
        ->assertJsonPath('data.name', $payload['name']);

    $this->assertDatabaseHas('unit_of_measure_categories', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a uom category', function () {
    actingAsUomCategoryApiUser(['create_support_u::o::m::category']);

    $this->postJson(uomCategoryRoute('store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('shows a uom category for authorized users', function () {
    actingAsUomCategoryApiUser(['view_support_u::o::m::category']);

    $category = UOMCategory::factory()->create();

    $this->getJson(uomCategoryRoute('show', $category))
        ->assertOk()
        ->assertJsonPath('data.id', $category->id)
        ->assertJsonStructure(['data' => UOM_CATEGORY_JSON_STRUCTURE]);
});

it('updates a uom category for authorized users', function () {
    actingAsUomCategoryApiUser(['update_support_u::o::m::category']);

    $category = UOMCategory::factory()->create();
    $updatedName = 'CATEGORY-'.fake()->unique()->numerify('####');

    $this->patchJson(uomCategoryRoute('update', $category), ['name' => $updatedName])
        ->assertOk()
        ->assertJsonPath('message', 'UOM category updated successfully.')
        ->assertJsonPath('data.name', $updatedName);

    $this->assertDatabaseHas('unit_of_measure_categories', [
        'id'   => $category->id,
        'name' => $updatedName,
    ]);
});

it('deletes a uom category for authorized users', function () {
    actingAsUomCategoryApiUser(['delete_support_u::o::m::category']);

    $category = UOMCategory::factory()->create();

    $this->deleteJson(uomCategoryRoute('destroy', $category))
        ->assertOk()
        ->assertJsonPath('message', 'UOM category deleted successfully.');

    $this->assertDatabaseMissing('unit_of_measure_categories', ['id' => $category->id]);
});
