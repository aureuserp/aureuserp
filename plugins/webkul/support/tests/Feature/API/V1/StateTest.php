<?php

use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

require_once __DIR__.'/../../../Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../Helpers/TestBootstrapHelper.php';

const STATE_JSON_STRUCTURE = [
    'id',
    'name',
    'code',
    'country_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();
    SecurityHelper::disableUserEvents();
});
afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingWithStatePermissions(array $permissions): void
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

it('forbids listing states without permission', function () {
    actingWithStatePermissions([]);

    $this->getJson(stateRoute('index'))
        ->assertForbidden();
});

it('lists states for authorized users', function () {
    actingWithStatePermissions(['view_any_support_state']);

    $response = $this->getJson(stateRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data' => ['*' => STATE_JSON_STRUCTURE]]);

    // Verify we have data (seeded states)
    expect($response->json('data'))->not->toBeEmpty();
});

it('creates a state with valid payload', function () {
    actingWithStatePermissions(['create_support_state']);

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

it('forbids creating a state without permission', function () {
    actingWithStatePermissions([]);

    $payload = State::factory()->make()->toArray();

    $this->postJson(stateRoute('store'), $payload)
        ->assertForbidden();
});

it('validates required fields when creating a state', function (string $field) {
    actingWithStatePermissions(['create_support_state']);

    $payload = State::factory()->make()->toArray();
    unset($payload[$field]);

    $this->postJson(stateRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(['name', 'country_id']);

it('shows a state for authorized users', function () {
    actingWithStatePermissions(['view_support_state']);

    $state = State::factory()->create();

    $this->getJson(stateRoute('show', $state))
        ->assertOk()
        ->assertJsonPath('data.id', $state->id)
        ->assertJsonStructure(['data' => STATE_JSON_STRUCTURE]);
});

it('forbids showing a state without permission', function () {
    actingWithStatePermissions([]);

    $state = State::factory()->create();

    $this->getJson(stateRoute('show', $state))
        ->assertForbidden();
});

it('returns 404 for non-existent state', function () {
    actingWithStatePermissions(['view_support_state']);

    $this->getJson(stateRoute('show', 999999))
        ->assertNotFound();
});

it('updates a state for authorized users', function () {
    actingWithStatePermissions(['update_support_state']);

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

it('forbids updating a state without permission', function () {
    actingWithStatePermissions([]);

    $state = State::factory()->create();
    $country = Country::factory()->create();

    $this->patchJson(stateRoute('update', $state), [
        'name'       => 'Updated State Name',
        'country_id' => $country->id,
    ])
        ->assertForbidden();
});

it('deletes a state for authorized users', function () {
    actingWithStatePermissions(['delete_support_state']);

    $state = State::factory()->create();

    $this->deleteJson(stateRoute('destroy', $state))
        ->assertOk()
        ->assertJsonPath('message', 'State deleted successfully.');

    $this->assertDatabaseMissing('states', [
        'id' => $state->id,
    ]);
});

it('forbids deleting a state without permission', function () {
    actingWithStatePermissions([]);

    $state = State::factory()->create();

    $this->deleteJson(stateRoute('destroy', $state))
        ->assertForbidden();
});
