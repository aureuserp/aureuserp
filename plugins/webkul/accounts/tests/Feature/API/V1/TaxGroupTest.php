<?php

use Webkul\Account\Models\TaxGroup;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const TAX_GROUP_JSON_STRUCTURE = [
    'id',
    'name',
];

const TAX_GROUP_REQUIRED_FIELDS = [
    'name',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsTaxGroupApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function taxGroupRoute(string $action, mixed $taxGroup = null): string
{
    $name = "admin.api.v1.accounts.tax-groups.{$action}";

    return $taxGroup ? route($name, $taxGroup) : route($name);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list tax groups', function () {
    $this->getJson(taxGroupRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a tax group', function () {
    $this->postJson(taxGroupRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing tax groups without permission', function () {
    actingAsTaxGroupApiUser();

    $this->getJson(taxGroupRoute('index'))
        ->assertForbidden();
});

it('forbids creating a tax group without permission', function () {
    actingAsTaxGroupApiUser();

    $this->postJson(taxGroupRoute('store'), ['name' => 'VAT Group'])
        ->assertForbidden();
});

it('forbids updating a tax group without permission', function () {
    actingAsTaxGroupApiUser();

    $taxGroup = TaxGroup::factory()->create();

    $this->patchJson(taxGroupRoute('update', $taxGroup), [])
        ->assertForbidden();
});

it('forbids deleting a tax group without permission', function () {
    actingAsTaxGroupApiUser();

    $taxGroup = TaxGroup::factory()->create();

    $this->deleteJson(taxGroupRoute('destroy', $taxGroup))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists tax groups for authorized users', function () {
    actingAsTaxGroupApiUser(['view_any_account_tax::group']);

    TaxGroup::factory()->count(3)->create();

    $this->getJson(taxGroupRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a tax group', function () {
    actingAsTaxGroupApiUser(['create_account_tax::group']);

    $payload = ['name' => 'Standard VAT Group'];

    $this->postJson(taxGroupRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Tax group created successfully.')
        ->assertJsonPath('data.name', 'Standard VAT Group')
        ->assertJsonStructure(['data' => TAX_GROUP_JSON_STRUCTURE]);

    $this->assertDatabaseHas('accounts_tax_groups', ['name' => 'Standard VAT Group']);
});

it('validates required fields when creating a tax group', function (string $field) {
    actingAsTaxGroupApiUser(['create_account_tax::group']);

    $payload = ['name' => 'VAT Group'];
    unset($payload[$field]);

    $this->postJson(taxGroupRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(TAX_GROUP_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a tax group for authorized users', function () {
    actingAsTaxGroupApiUser(['view_account_tax::group']);

    $taxGroup = TaxGroup::factory()->create();

    $this->getJson(taxGroupRoute('show', $taxGroup))
        ->assertOk()
        ->assertJsonPath('data.id', $taxGroup->id)
        ->assertJsonStructure(['data' => TAX_GROUP_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent tax group', function () {
    actingAsTaxGroupApiUser(['view_account_tax::group']);

    $this->getJson(taxGroupRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a tax group', function () {
    actingAsTaxGroupApiUser(['update_account_tax::group']);

    $taxGroup = TaxGroup::factory()->create();

    $this->patchJson(taxGroupRoute('update', $taxGroup), ['name' => 'Reduced VAT Group'])
        ->assertOk()
        ->assertJsonPath('message', 'Tax group updated successfully.')
        ->assertJsonPath('data.name', 'Reduced VAT Group');

    $this->assertDatabaseHas('accounts_tax_groups', [
        'id'   => $taxGroup->id,
        'name' => 'Reduced VAT Group',
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes a tax group', function () {
    actingAsTaxGroupApiUser(['delete_account_tax::group']);

    $taxGroup = TaxGroup::factory()->create();

    $this->deleteJson(taxGroupRoute('destroy', $taxGroup))
        ->assertOk()
        ->assertJsonPath('message', 'Tax group deleted successfully.');

    $this->assertDatabaseMissing('accounts_tax_groups', ['id' => $taxGroup->id]);
});
