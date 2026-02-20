<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Support\Models\UOM;
use Webkul\Support\Models\UOMCategory;

require_once __DIR__.'/../../../Helpers/SecurityHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

const UOM_JSON_STRUCTURE = [
    'id',
    'type',
    'name',
    'factor',
    'rounding',
    'category_id',
    'creator_id',
    'created_at',
    'updated_at',
    'deleted_at',
];

beforeEach(function () {
    if (! Schema::hasTable('unit_of_measures')) {
        test()->markTestSkipped(
            'Required plugin tables are missing. Install/migrate the plugin before running this suite.'
        );
    }

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsUomApiUser(array $permissions): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function uomRoute(string $action, mixed $category, mixed $uom = null): string
{
    $name = "admin.api.v1.support.uom-categories.uoms.{$action}";

    if (in_array($action, ['restore', 'force-destroy'], true)) {
        return route($name, [
            'uom_category_id' => $category,
            'id'              => $uom,
        ]);
    }

    $parameters = ['uom_category' => $category];

    if ($uom !== null) {
        $parameters['uom'] = $uom;
    }

    return route($name, $parameters);
}

it('requires authentication to list uoms', function () {
    $category = UOMCategory::factory()->create();

    $this->getJson(uomRoute('index', $category))
        ->assertUnauthorized();
});

it('forbids listing uoms without permission', function () {
    actingAsUomApiUser([]);

    $category = UOMCategory::factory()->create();

    $this->getJson(uomRoute('index', $category))
        ->assertForbidden();
});

it('lists uoms for authorized users', function () {
    actingAsUomApiUser(['view_support_u::o::m::category']);

    $category = UOMCategory::factory()->create();
    $uomOne = UOM::factory()->create([
        'category_id' => $category->id,
        'rounding'    => 0.01,
    ]);
    $uomTwo = UOM::factory()->create([
        'category_id' => $category->id,
        'rounding'    => 0.01,
    ]);

    $response = $this->getJson(uomRoute('index', $category));

    $response
        ->assertOk()
        ->assertJsonStructure(['data' => ['*' => UOM_JSON_STRUCTURE]]);

    expect(collect($response->json('data'))->pluck('id')->all())
        ->toContain($uomOne->id, $uomTwo->id);
});

it('creates a uom with valid payload', function () {
    actingAsUomApiUser(['update_support_u::o::m::category']);

    $category = UOMCategory::factory()->create();
    $payload = UOM::factory()->make([
        'category_id' => $category->id,
        'rounding'    => 0.01,
    ])->toArray();

    $this->postJson(uomRoute('store', $category), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'UOM created successfully.')
        ->assertJsonPath('data.name', $payload['name']);

    $this->assertDatabaseHas('unit_of_measures', [
        'name'        => $payload['name'],
        'category_id' => $category->id,
    ]);
});

it('validates required fields when creating a uom', function (string $field) {
    actingAsUomApiUser(['update_support_u::o::m::category']);

    $category = UOMCategory::factory()->create();
    $payload = UOM::factory()->make([
        'category_id' => $category->id,
        'rounding'    => 0.01,
    ])->toArray();
    unset($payload[$field]);

    $this->postJson(uomRoute('store', $category), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(['type', 'name', 'factor', 'rounding', 'category_id']);

it('shows a uom for authorized users', function () {
    actingAsUomApiUser(['view_support_u::o::m::category']);

    $category = UOMCategory::factory()->create();
    $uom = UOM::factory()->create([
        'category_id' => $category->id,
        'rounding'    => 0.01,
    ]);

    $this->getJson(uomRoute('show', $category, $uom))
        ->assertOk()
        ->assertJsonPath('data.id', $uom->id)
        ->assertJsonStructure(['data' => UOM_JSON_STRUCTURE]);
});

it('updates a uom for authorized users', function () {
    actingAsUomApiUser(['update_support_u::o::m::category']);

    $category = UOMCategory::factory()->create();
    $uom = UOM::factory()->create([
        'category_id' => $category->id,
        'rounding'    => 0.01,
    ]);

    $this->patchJson(uomRoute('update', $category, $uom), ['name' => 'Updated UOM Name'])
        ->assertOk()
        ->assertJsonPath('message', 'UOM updated successfully.')
        ->assertJsonPath('data.name', 'Updated UOM Name');

    $this->assertDatabaseHas('unit_of_measures', [
        'id'   => $uom->id,
        'name' => 'Updated UOM Name',
    ]);
});

it('soft deletes a uom for authorized users', function () {
    actingAsUomApiUser(['update_support_u::o::m::category']);

    $category = UOMCategory::factory()->create();
    $uom = UOM::factory()->create([
        'category_id' => $category->id,
        'rounding'    => 0.01,
    ]);

    $this->deleteJson(uomRoute('destroy', $category, $uom))
        ->assertOk()
        ->assertJsonPath('message', 'UOM deleted successfully.');

    $this->assertSoftDeleted('unit_of_measures', ['id' => $uom->id]);
});

it('restores a soft deleted uom for authorized users', function () {
    actingAsUomApiUser(['update_support_u::o::m::category']);

    $category = UOMCategory::factory()->create();
    $uom = UOM::factory()->create([
        'category_id' => $category->id,
        'rounding'    => 0.01,
    ]);
    $uom->delete();

    $this->postJson(uomRoute('restore', $category->id, $uom->id))
        ->assertOk()
        ->assertJsonPath('message', 'UOM restored successfully.');

    $this->assertDatabaseHas('unit_of_measures', [
        'id'         => $uom->id,
        'deleted_at' => null,
    ]);
});

it('force deletes a uom for authorized users', function () {
    actingAsUomApiUser(['update_support_u::o::m::category']);

    $category = UOMCategory::factory()->create();
    $uom = UOM::factory()->create([
        'category_id' => $category->id,
        'rounding'    => 0.01,
    ]);
    $uom->delete();

    $this->deleteJson(uomRoute('force-destroy', $category->id, $uom->id))
        ->assertOk()
        ->assertJsonPath('message', 'UOM permanently deleted.');

    $this->assertDatabaseMissing('unit_of_measures', ['id' => $uom->id]);
});
