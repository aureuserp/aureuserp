<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

require_once __DIR__.'/../../../Helpers/SecurityHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

const STATE_JSON_STRUCTURE = [
    'id',
    'name',
    'code',
    'country_id',
];

beforeEach(function () {
    if (! Schema::hasTable('states')) {
        test()->markTestSkipped(
            'Support plugin tables are missing. Install/migrate the support plugin before running this suite.'
        );
    }

    SecurityHelper::disableUserEvents();
});
afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsStateApiUser(array $permissions = []): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function stateRoute(string $action, mixed $state = null): string
{
    $name = "admin.api.v1.support.states.{$action}";

    return $state ? route($name, $state) : route($name);
}

it('requires authentication to list states', function () {
    $this->getJson(stateRoute('index'))
        ->assertUnauthorized();
});

it('lists states for authenticated users', function () {
    actingAsStateApiUser();

    State::factory()->count(2)->create();

    $this->getJson(stateRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => ['*' => STATE_JSON_STRUCTURE]]);
});

it('creates a state with valid payload', function () {
    actingAsStateApiUser();

    $payload = State::factory()->make()->toArray();

    $this->postJson(stateRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'State created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => STATE_JSON_STRUCTURE]);

    $this->assertDatabaseHas('states', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a state', function (string $field) {
    actingAsStateApiUser();

    $payload = State::factory()->make()->toArray();
    unset($payload[$field]);

    $this->postJson(stateRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(['name', 'country_id']);

it('shows a state for authenticated users', function () {
    actingAsStateApiUser();

    $state = State::factory()->create();

    $this->getJson(stateRoute('show', $state))
        ->assertOk()
        ->assertJsonPath('data.id', $state->id)
        ->assertJsonStructure(['data' => STATE_JSON_STRUCTURE]);
});

it('returns 404 for non-existent state', function () {
    actingAsStateApiUser();

    $this->getJson(stateRoute('show', 999999))
        ->assertNotFound();
});

it('updates a state for authenticated users', function () {
    actingAsStateApiUser();

    $state = State::factory()->create();
    $country = Country::factory()->create();

    $this->patchJson(stateRoute('update', $state), [
        'name'       => 'Updated State Name',
        'country_id' => $country->id,
    ])
        ->assertOk()
        ->assertJsonPath('message', 'State updated successfully.')
        ->assertJsonPath('data.name', 'Updated State Name');

    $this->assertDatabaseHas('states', [
        'id'         => $state->id,
        'name'       => 'Updated State Name',
        'country_id' => $country->id,
    ]);
});

it('deletes a state for authenticated users', function () {
    actingAsStateApiUser();

    $state = State::factory()->create();

    $this->deleteJson(stateRoute('destroy', $state))
        ->assertOk()
        ->assertJsonPath('message', 'State deleted successfully.');

    $this->assertDatabaseMissing('states', [
        'id' => $state->id,
    ]);
});
