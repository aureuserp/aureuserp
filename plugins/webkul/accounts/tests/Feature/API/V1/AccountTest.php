<?php

use Webkul\Account\Enums\AccountType;
use Webkul\Account\Models\Account;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const ACCOUNT_JSON_STRUCTURE = [
    'id',
    'name',
    'code',
    'account_type',
];

const ACCOUNT_REQUIRED_FIELDS = [
    'name',
    'code',
    'account_type',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsAccountApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function accountRoute(string $action, mixed $account = null): string
{
    $name = "admin.api.v1.accounts.accounts.{$action}";

    return $account ? route($name, $account) : route($name);
}

function accountPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name'         => 'Cash and Bank',
        'code'         => '100'.rand(10, 99),
        'account_type' => AccountType::ASSET_CURRENT->value,
    ], $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list accounts', function () {
    $this->getJson(accountRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create an account', function () {
    $this->postJson(accountRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing accounts without permission', function () {
    actingAsAccountApiUser();

    $this->getJson(accountRoute('index'))
        ->assertForbidden();
});

it('forbids creating an account without permission', function () {
    actingAsAccountApiUser();

    $this->postJson(accountRoute('store'), accountPayload())
        ->assertForbidden();
});

it('forbids updating an account without permission', function () {
    actingAsAccountApiUser();

    $account = Account::factory()->create();

    $this->patchJson(accountRoute('update', $account), [])
        ->assertForbidden();
});

it('forbids deleting an account without permission', function () {
    actingAsAccountApiUser();

    $account = Account::factory()->create();

    $this->deleteJson(accountRoute('destroy', $account))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists accounts for authorized users', function () {
    actingAsAccountApiUser(['view_any_account_account']);

    Account::factory()->count(3)->create();

    $this->getJson(accountRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters accounts by account type', function () {
    actingAsAccountApiUser(['view_any_account_account']);

    $account = Account::factory()->receivable()->create();
    Account::factory()->expense()->count(2)->create();

    $response = $this->getJson(accountRoute('index').'?filter[account_type]='.AccountType::ASSET_RECEIVABLE->value)
        ->assertOk();

    collect($response->json('data'))->each(function ($item) {
        expect($item['account_type'])->toBe(AccountType::ASSET_RECEIVABLE->value);
    });

    expect(collect($response->json('data'))->firstWhere('id', $account->id))->not->toBeNull();
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates an account', function () {
    actingAsAccountApiUser(['create_account_account']);

    $payload = accountPayload(['code' => '1001', 'name' => 'Test Cash Account']);

    $this->postJson(accountRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Account created successfully.')
        ->assertJsonPath('data.name', 'Test Cash Account')
        ->assertJsonPath('data.code', '1001')
        ->assertJsonStructure(['data' => ACCOUNT_JSON_STRUCTURE]);

    $this->assertDatabaseHas('accounts_accounts', [
        'name'         => 'Test Cash Account',
        'code'         => '1001',
        'account_type' => AccountType::ASSET_CURRENT->value,
    ]);
});

it('validates required fields when creating an account', function (string $field) {
    actingAsAccountApiUser(['create_account_account']);

    $payload = accountPayload();
    unset($payload[$field]);

    $this->postJson(accountRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(ACCOUNT_REQUIRED_FIELDS);

it('validates account_type must be a valid enum value', function () {
    actingAsAccountApiUser(['create_account_account']);

    $payload = accountPayload(['account_type' => 'invalid_type']);

    $this->postJson(accountRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['account_type']);
});

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows an account for authorized users', function () {
    actingAsAccountApiUser(['view_account_account']);

    $account = Account::factory()->create();

    $this->getJson(accountRoute('show', $account))
        ->assertOk()
        ->assertJsonPath('data.id', $account->id)
        ->assertJsonStructure(['data' => ACCOUNT_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent account', function () {
    actingAsAccountApiUser(['view_account_account']);

    $this->getJson(accountRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates an account', function () {
    actingAsAccountApiUser(['update_account_account']);

    $account = Account::factory()->create();

    $this->patchJson(accountRoute('update', $account), ['name' => 'Updated Account Name'])
        ->assertOk()
        ->assertJsonPath('message', 'Account updated successfully.')
        ->assertJsonPath('data.name', 'Updated Account Name');

    $this->assertDatabaseHas('accounts_accounts', [
        'id'   => $account->id,
        'name' => 'Updated Account Name',
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes an account', function () {
    actingAsAccountApiUser(['delete_account_account']);

    $account = Account::factory()->create();

    $this->deleteJson(accountRoute('destroy', $account))
        ->assertOk()
        ->assertJsonPath('message', 'Account deleted successfully.');

    $this->assertDatabaseMissing('accounts_accounts', ['id' => $account->id]);
});
