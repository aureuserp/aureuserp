<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../Helpers/SecurityHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

const CURRENCY_JSON_STRUCTURE = [
    'id',
    'name',
    'symbol',
    'iso_numeric',
    'decimal_places',
    'full_name',
    'rounding',
    'active',
];

beforeEach(function () {
    if (! Schema::hasTable('currencies')) {
        test()->markTestSkipped(
            'Required plugin tables are missing. Install/migrate the plugin before running this suite.'
        );
    }

    SecurityHelper::disableUserEvents();
});
afterEach(fn () => SecurityHelper::restoreUserEvents());

/**
 * Authenticate with the given permissions and return a fluent test instance.
 */
function actingWithPermissions(array $permissions): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function currencyRoute(string $action, mixed $currency = null): string
{
    $name = "admin.api.v1.support.currencies.{$action}";

    return $currency ? route($name, $currency) : route($name);
}

it('requires authentication to list currencies', function () {
    $this->getJson(currencyRoute('index'))
        ->assertUnauthorized();
});

it('forbids listing currencies without permission', function () {
    actingWithPermissions([]);

    $this->getJson(currencyRoute('index'))
        ->assertForbidden();
});

it('lists currencies for authorized users', function () {
    actingWithPermissions(['view_any_support_currency']);

    Currency::factory()->count(2)->create();

    $this->getJson(currencyRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => ['*' => CURRENCY_JSON_STRUCTURE]]);
});

it('creates a currency with valid payload', function () {
    actingWithPermissions(['create_support_currency']);

    $payload = Currency::factory()->make()->toArray();

    $this->postJson(currencyRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Currency created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => CURRENCY_JSON_STRUCTURE]);

    $this->assertDatabaseHas('currencies', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a currency', function (string $field) {
    actingWithPermissions(['create_support_currency']);

    $payload = Currency::factory()->make()->toArray();
    unset($payload[$field]);

    $this->postJson(currencyRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(['name', 'symbol', 'decimal_places', 'active']);

it('shows a currency for authorized users', function () {
    actingWithPermissions(['view_support_currency']);

    $currency = Currency::factory()->create();

    $this->getJson(currencyRoute('show', $currency))
        ->assertOk()
        ->assertJsonPath('data.id', $currency->id)
        ->assertJsonStructure(['data' => CURRENCY_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent currency', function () {
    actingWithPermissions(['view_support_currency']);

    $this->getJson(currencyRoute('show', 999999))
        ->assertNotFound();
});

it('updates a currency for authorized users', function () {
    actingWithPermissions(['update_support_currency']);

    $currency = Currency::factory()->create();
    $updatedName = Currency::factory()->make()->name;

    $this->patchJson(currencyRoute('update', $currency), ['name' => $updatedName])
        ->assertOk()
        ->assertJsonPath('message', 'Currency updated successfully.')
        ->assertJsonPath('data.name', $updatedName);

    $this->assertDatabaseHas('currencies', [
        'id'   => $currency->id,
        'name' => $updatedName,
    ]);
});

it('forbids updating a currency without permission', function () {
    actingWithPermissions([]);

    $currency = Currency::factory()->create();

    $this->patchJson(currencyRoute('update', $currency), ['name' => 'New Name'])
        ->assertForbidden();
});

it('deletes a currency for authorized users', function () {
    actingWithPermissions(['delete_support_currency']);

    $currency = Currency::factory()->create();

    $this->deleteJson(currencyRoute('destroy', $currency))
        ->assertOk()
        ->assertJsonPath('message', 'Currency deleted successfully.');

    $this->assertDatabaseMissing('currencies', [
        'id' => $currency->id,
    ]);
});

it('forbids deleting a currency without permission', function () {
    actingWithPermissions([]);

    $currency = Currency::factory()->create();

    $this->deleteJson(currencyRoute('destroy', $currency))
        ->assertForbidden();
});
