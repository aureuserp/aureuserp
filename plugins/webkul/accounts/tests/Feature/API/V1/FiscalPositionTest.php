<?php

use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\Tax;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const FISCAL_POSITION_JSON_STRUCTURE = [
    'id',
    'name',
];

const FISCAL_POSITION_REQUIRED_FIELDS = [
    'name',
    'company_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsFiscalPositionApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function fiscalPositionRoute(string $action, mixed $fiscalPosition = null): string
{
    $name = "admin.api.v1.accounts.fiscal-positions.{$action}";

    return $fiscalPosition ? route($name, $fiscalPosition) : route($name);
}

function fiscalPositionPayload(array $overrides = []): array
{
    $currency = Currency::first() ?? Currency::factory()->create();
    $company = Company::factory()->create(['currency_id' => $currency->id]);

    return array_replace_recursive([
        'name'       => 'Domestic',
        'company_id' => $company->id,
    ], $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list fiscal positions', function () {
    $this->getJson(fiscalPositionRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a fiscal position', function () {
    $this->postJson(fiscalPositionRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing fiscal positions without permission', function () {
    actingAsFiscalPositionApiUser();

    $this->getJson(fiscalPositionRoute('index'))
        ->assertForbidden();
});

it('forbids creating a fiscal position without permission', function () {
    actingAsFiscalPositionApiUser();

    $this->postJson(fiscalPositionRoute('store'), fiscalPositionPayload())
        ->assertForbidden();
});

it('forbids updating a fiscal position without permission', function () {
    actingAsFiscalPositionApiUser();

    $fiscalPosition = FiscalPosition::factory()->create();

    $this->patchJson(fiscalPositionRoute('update', $fiscalPosition), [])
        ->assertForbidden();
});

it('forbids deleting a fiscal position without permission', function () {
    actingAsFiscalPositionApiUser();

    $fiscalPosition = FiscalPosition::factory()->create();

    $this->deleteJson(fiscalPositionRoute('destroy', $fiscalPosition))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists fiscal positions for authorized users', function () {
    actingAsFiscalPositionApiUser(['view_any_account_fiscal::position']);

    FiscalPosition::factory()->count(3)->create();

    $this->getJson(fiscalPositionRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters fiscal positions by name', function () {
    actingAsFiscalPositionApiUser(['view_any_account_fiscal::position']);

    $fiscalPosition = FiscalPosition::factory()->create(['name' => 'EU Import Position']);
    FiscalPosition::factory()->count(2)->create();

    $response = $this->getJson(fiscalPositionRoute('index').'?filter[name]=EU')
        ->assertOk();

    collect($response->json('data'))->each(function ($item) {
        expect($item['name'])->toContain('EU');
    });

    expect(collect($response->json('data'))->firstWhere('id', $fiscalPosition->id))->not->toBeNull();
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a fiscal position', function () {
    actingAsFiscalPositionApiUser(['create_account_fiscal::position']);

    $payload = fiscalPositionPayload();

    $this->postJson(fiscalPositionRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Fiscal position created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => FISCAL_POSITION_JSON_STRUCTURE]);

    $this->assertDatabaseHas('accounts_fiscal_positions', [
        'name'       => $payload['name'],
        'company_id' => $payload['company_id'],
    ]);
});

it('creates a fiscal position with tax mappings', function () {
    actingAsFiscalPositionApiUser(['create_account_fiscal::position']);

    $taxSource = Tax::factory()->create();
    $taxDestination = Tax::factory()->create();

    $payload = fiscalPositionPayload([
        'taxes' => [
            [
                'tax_source_id'      => $taxSource->id,
                'tax_destination_id' => $taxDestination->id,
            ],
        ],
    ]);

    $response = $this->postJson(fiscalPositionRoute('store'), $payload)
        ->assertCreated();

    $this->assertDatabaseHas('accounts_fiscal_position_taxes', [
        'fiscal_position_id' => $response->json('data.id'),
        'tax_source_id'      => $taxSource->id,
        'tax_destination_id' => $taxDestination->id,
    ]);
});

it('validates required fields when creating a fiscal position', function (string $field) {
    actingAsFiscalPositionApiUser(['create_account_fiscal::position']);

    $payload = fiscalPositionPayload();
    unset($payload[$field]);

    $this->postJson(fiscalPositionRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(FISCAL_POSITION_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a fiscal position for authorized users', function () {
    actingAsFiscalPositionApiUser(['view_account_fiscal::position']);

    $fiscalPosition = FiscalPosition::factory()->create();

    $this->getJson(fiscalPositionRoute('show', $fiscalPosition))
        ->assertOk()
        ->assertJsonPath('data.id', $fiscalPosition->id)
        ->assertJsonStructure(['data' => FISCAL_POSITION_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent fiscal position', function () {
    actingAsFiscalPositionApiUser(['view_account_fiscal::position']);

    $this->getJson(fiscalPositionRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a fiscal position', function () {
    actingAsFiscalPositionApiUser(['update_account_fiscal::position']);

    $fiscalPosition = FiscalPosition::factory()->create();

    $this->patchJson(fiscalPositionRoute('update', $fiscalPosition), ['name' => 'Updated Fiscal Position'])
        ->assertOk()
        ->assertJsonPath('message', 'Fiscal position updated successfully.')
        ->assertJsonPath('data.name', 'Updated Fiscal Position');

    $this->assertDatabaseHas('accounts_fiscal_positions', [
        'id'   => $fiscalPosition->id,
        'name' => 'Updated Fiscal Position',
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes a fiscal position', function () {
    actingAsFiscalPositionApiUser(['delete_account_fiscal::position']);

    $fiscalPosition = FiscalPosition::factory()->create();

    $this->deleteJson(fiscalPositionRoute('destroy', $fiscalPosition))
        ->assertOk()
        ->assertJsonPath('message', 'Fiscal position deleted successfully.');

    $this->assertDatabaseMissing('accounts_fiscal_positions', ['id' => $fiscalPosition->id]);
});
