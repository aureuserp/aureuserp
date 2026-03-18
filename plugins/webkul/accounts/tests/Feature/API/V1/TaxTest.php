<?php

use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxGroup;
use Webkul\Account\Models\TaxPartition;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const TAX_JSON_STRUCTURE = [
    'id',
    'name',
    'type_tax_use',
    'amount_type',
    'amount',
];

const TAX_REQUIRED_FIELDS = [
    'name',
    'type_tax_use',
    'amount_type',
    'amount',
    'tax_group_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsTaxApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function taxRoute(string $action, mixed $tax = null): string
{
    $name = "admin.api.v1.accounts.taxes.{$action}";

    return $tax ? route($name, $tax) : route($name);
}

function taxPayload(array $overrides = []): array
{
    $taxGroup = TaxGroup::factory()->create();

    return array_replace_recursive([
        'name'                      => 'Sales Tax 10%',
        'type_tax_use'              => TypeTaxUse::SALE->value,
        'amount_type'               => AmountType::PERCENT->value,
        'amount'                    => 10,
        'tax_group_id'              => $taxGroup->id,
        'invoice_repartition_lines' => [
            ['repartition_type' => 'base'],
            ['repartition_type' => 'tax', 'factor_percent' => 100],
        ],
        'refund_repartition_lines' => [
            ['repartition_type' => 'base'],
            ['repartition_type' => 'tax', 'factor_percent' => 100],
        ],
    ], $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list taxes', function () {
    $this->getJson(taxRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a tax', function () {
    $this->postJson(taxRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing taxes without permission', function () {
    actingAsTaxApiUser();

    $this->getJson(taxRoute('index'))
        ->assertForbidden();
});

it('forbids creating a tax without permission', function () {
    actingAsTaxApiUser();

    $this->postJson(taxRoute('store'), taxPayload())
        ->assertForbidden();
});

it('forbids updating a tax without permission', function () {
    actingAsTaxApiUser();

    $tax = Tax::factory()->create();

    $this->patchJson(taxRoute('update', $tax), [])
        ->assertForbidden();
});

it('forbids deleting a tax without permission', function () {
    actingAsTaxApiUser();

    $tax = Tax::factory()->create();

    $this->deleteJson(taxRoute('destroy', $tax))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists taxes for authorized users', function () {
    actingAsTaxApiUser(['view_any_account_tax']);

    Tax::factory()->count(3)->create();

    $this->getJson(taxRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a tax with repartition lines', function () {
    $user = actingAsTaxApiUser(['create_account_tax']);

    $currency = Currency::first() ?? Currency::factory()->create();
    $company = Company::factory()->create(['currency_id' => $currency->id]);
    $user->forceFill(['default_company_id' => $company->id])->saveQuietly();

    $payload = taxPayload(['company_id' => $company->id]);

    $response = $this->postJson(taxRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Tax created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonPath('data.amount', $payload['amount'])
        ->assertJsonStructure(['data' => TAX_JSON_STRUCTURE]);

    $taxId = $response->json('data.id');

    $this->assertDatabaseHas('accounts_taxes', [
        'id'           => $taxId,
        'name'         => $payload['name'],
        'amount'       => 10,
        'tax_group_id' => $payload['tax_group_id'],
    ]);

    expect(TaxPartition::where('tax_id', $taxId)->count())->toBe(4);
});

it('validates required fields when creating a tax', function (string $field) {
    actingAsTaxApiUser(['create_account_tax']);

    $payload = taxPayload();
    unset($payload[$field]);

    $this->postJson(taxRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(TAX_REQUIRED_FIELDS);

it('validates that invoice_repartition_lines must have exactly 1 base line', function () {
    actingAsTaxApiUser(['create_account_tax']);

    $payload = taxPayload([
        'invoice_repartition_lines' => [
            ['repartition_type' => 'tax', 'factor_percent' => 100],
        ],
    ]);

    $this->postJson(taxRoute('store'), $payload)
        ->assertUnprocessable();
});

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a tax for authorized users', function () {
    actingAsTaxApiUser(['view_account_tax']);

    $tax = Tax::factory()->create();

    $this->getJson(taxRoute('show', $tax))
        ->assertOk()
        ->assertJsonPath('data.id', $tax->id)
        ->assertJsonStructure(['data' => TAX_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent tax', function () {
    actingAsTaxApiUser(['view_account_tax']);

    $this->getJson(taxRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a tax name', function () {
    actingAsTaxApiUser(['update_account_tax']);

    $tax = Tax::factory()->create();

    $this->patchJson(taxRoute('update', $tax), ['name' => 'Updated Tax 15%'])
        ->assertOk()
        ->assertJsonPath('message', 'Tax updated successfully.')
        ->assertJsonPath('data.name', 'Updated Tax 15%');

    $this->assertDatabaseHas('accounts_taxes', [
        'id'   => $tax->id,
        'name' => 'Updated Tax 15%',
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes a tax', function () {
    actingAsTaxApiUser(['delete_account_tax']);

    $tax = Tax::factory()->create();

    $this->deleteJson(taxRoute('destroy', $tax))
        ->assertOk()
        ->assertJsonPath('message', 'Tax deleted successfully.');

    $this->assertDatabaseMissing('accounts_taxes', ['id' => $tax->id]);
});
