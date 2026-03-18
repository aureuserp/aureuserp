<?php

use Webkul\Account\Enums\RoundingMethod;
use Webkul\Account\Enums\RoundingStrategy;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\CashRounding;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const CASH_ROUNDING_JSON_STRUCTURE = [
    'id',
    'name',
    'strategy',
    'rounding_method',
    'rounding',
];

const CASH_ROUNDING_REQUIRED_FIELDS = [
    'name',
    'strategy',
    'rounding_method',
    'rounding',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsCashRoundingApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function cashRoundingRoute(string $action, mixed $cashRounding = null): string
{
    $name = "admin.api.v1.accounts.cash-roundings.{$action}";

    return $cashRounding ? route($name, $cashRounding) : route($name);
}

function cashRoundingPayload(array $overrides = []): array
{
    $profitAccount = Account::factory()->create();
    $lossAccount = Account::factory()->create();

    return array_replace_recursive([
        'name'              => '0.05 Rounding',
        'strategy'          => RoundingStrategy::BIGGEST_TAX->value,
        'rounding_method'   => RoundingMethod::HALF_UP->value,
        'rounding'          => 0.05,
        'profit_account_id' => $profitAccount->id,
        'loss_account_id'   => $lossAccount->id,
    ], $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list cash roundings', function () {
    $this->getJson(cashRoundingRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a cash rounding', function () {
    $this->postJson(cashRoundingRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing cash roundings without permission', function () {
    actingAsCashRoundingApiUser();

    $this->getJson(cashRoundingRoute('index'))
        ->assertForbidden();
});

it('forbids creating a cash rounding without permission', function () {
    actingAsCashRoundingApiUser();

    $this->postJson(cashRoundingRoute('store'), cashRoundingPayload())
        ->assertForbidden();
});

it('forbids updating a cash rounding without permission', function () {
    actingAsCashRoundingApiUser();

    $cashRounding = CashRounding::factory()->create();

    $this->patchJson(cashRoundingRoute('update', $cashRounding), [])
        ->assertForbidden();
});

it('forbids deleting a cash rounding without permission', function () {
    actingAsCashRoundingApiUser();

    $cashRounding = CashRounding::factory()->create();

    $this->deleteJson(cashRoundingRoute('destroy', $cashRounding))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists cash roundings for authorized users', function () {
    actingAsCashRoundingApiUser(['view_any_account_cash::rounding']);

    CashRounding::factory()->count(3)->create();

    $this->getJson(cashRoundingRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters cash roundings by strategy', function () {
    actingAsCashRoundingApiUser(['view_any_account_cash::rounding']);

    $cashRounding = CashRounding::factory()->create(['strategy' => RoundingStrategy::ADD_INVOICE_LINE]);
    CashRounding::factory()->create(['strategy' => RoundingStrategy::BIGGEST_TAX]);

    $response = $this->getJson(cashRoundingRoute('index').'?filter[strategy]='.RoundingStrategy::ADD_INVOICE_LINE->value)
        ->assertOk();

    collect($response->json('data'))->each(function ($item) {
        expect($item['strategy'])->toBe(RoundingStrategy::ADD_INVOICE_LINE->value);
    });

    expect(collect($response->json('data'))->firstWhere('id', $cashRounding->id))->not->toBeNull();
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a cash rounding', function () {
    actingAsCashRoundingApiUser(['create_account_cash::rounding']);

    $payload = cashRoundingPayload();

    $this->postJson(cashRoundingRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Cash rounding created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonPath('data.rounding', $payload['rounding'])
        ->assertJsonStructure(['data' => CASH_ROUNDING_JSON_STRUCTURE]);

    $this->assertDatabaseHas('accounts_cash_roundings', [
        'name'     => $payload['name'],
        'rounding' => $payload['rounding'],
    ]);
});

it('validates required fields when creating a cash rounding', function (string $field) {
    actingAsCashRoundingApiUser(['create_account_cash::rounding']);

    $payload = cashRoundingPayload();
    unset($payload[$field]);

    $this->postJson(cashRoundingRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(CASH_ROUNDING_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a cash rounding for authorized users', function () {
    actingAsCashRoundingApiUser(['view_account_cash::rounding']);

    $cashRounding = CashRounding::factory()->create();

    $this->getJson(cashRoundingRoute('show', $cashRounding))
        ->assertOk()
        ->assertJsonPath('data.id', $cashRounding->id)
        ->assertJsonStructure(['data' => CASH_ROUNDING_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent cash rounding', function () {
    actingAsCashRoundingApiUser(['view_account_cash::rounding']);

    $this->getJson(cashRoundingRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a cash rounding', function () {
    actingAsCashRoundingApiUser(['update_account_cash::rounding']);

    $cashRounding = CashRounding::factory()->create();

    $this->patchJson(cashRoundingRoute('update', $cashRounding), ['name' => 'Updated Rounding'])
        ->assertOk()
        ->assertJsonPath('message', 'Cash rounding updated successfully.')
        ->assertJsonPath('data.name', 'Updated Rounding');

    $this->assertDatabaseHas('accounts_cash_roundings', [
        'id'   => $cashRounding->id,
        'name' => 'Updated Rounding',
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes a cash rounding', function () {
    actingAsCashRoundingApiUser(['delete_account_cash::rounding']);

    $cashRounding = CashRounding::factory()->create();

    $this->deleteJson(cashRoundingRoute('destroy', $cashRounding))
        ->assertOk()
        ->assertJsonPath('message', 'Cash rounding deleted successfully.');

    $this->assertDatabaseMissing('accounts_cash_roundings', ['id' => $cashRounding->id]);
});
