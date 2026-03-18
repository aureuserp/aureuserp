<?php

use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const CUSTOMER_JSON_STRUCTURE = [
    'id',
    'name',
    'account_type',
];

const CUSTOMER_REQUIRED_FIELDS = [
    'name',
    'account_type',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsCustomerApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function customerRoute(string $action, mixed $customer = null): string
{
    $name = "admin.api.v1.accounts.customers.{$action}";

    return $customer ? route($name, $customer) : route($name);
}

function customerPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name'         => 'Test Customer',
        'account_type' => AccountType::INDIVIDUAL->value,
    ], $overrides);
}

function makeCustomer(array $attributes = []): Partner
{
    return Partner::factory()->create(array_merge(['customer_rank' => 1], $attributes));
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list customers', function () {
    $this->getJson(customerRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a customer', function () {
    $this->postJson(customerRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing customers without permission', function () {
    actingAsCustomerApiUser();

    $this->getJson(customerRoute('index'))
        ->assertForbidden();
});

it('forbids creating a customer without permission', function () {
    actingAsCustomerApiUser();

    $this->postJson(customerRoute('store'), customerPayload())
        ->assertForbidden();
});

it('forbids updating a customer without permission', function () {
    actingAsCustomerApiUser();

    $customer = makeCustomer();

    $this->patchJson(customerRoute('update', $customer), [])
        ->assertForbidden();
});

it('forbids deleting a customer without permission', function () {
    actingAsCustomerApiUser();

    $customer = makeCustomer();

    $this->deleteJson(customerRoute('destroy', $customer))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists customers for authorized users', function () {
    actingAsCustomerApiUser(['view_any_account_partner']);

    makeCustomer();
    makeCustomer();
    makeCustomer();

    $this->getJson(customerRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('only returns customers with customer_rank > 0 in the customer list', function () {
    actingAsCustomerApiUser(['view_any_account_partner']);

    makeCustomer();
    Partner::factory()->create(['customer_rank' => 0, 'supplier_rank' => 1]);

    $response = $this->getJson(customerRoute('index'))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->all();

    foreach ($ids as $id) {
        $partner = Partner::withTrashed()->find($id);
        expect($partner->customer_rank)->toBeGreaterThan(0);
    }
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a customer and auto-sets customer_rank to 1', function () {
    actingAsCustomerApiUser(['create_account_partner']);

    $payload = customerPayload();

    $response = $this->postJson(customerRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Customer created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => CUSTOMER_JSON_STRUCTURE]);

    $customerId = $response->json('data.id');

    $this->assertDatabaseHas('partners_partners', [
        'id'            => $customerId,
        'name'          => $payload['name'],
        'customer_rank' => 1,
    ]);
});

it('validates required fields when creating a customer', function (string $field) {
    actingAsCustomerApiUser(['create_account_partner']);

    $payload = customerPayload();
    unset($payload[$field]);

    $this->postJson(customerRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(CUSTOMER_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a customer for authorized users', function () {
    actingAsCustomerApiUser(['view_account_partner']);

    $customer = makeCustomer();

    $this->getJson(customerRoute('show', $customer))
        ->assertOk()
        ->assertJsonPath('data.id', $customer->id)
        ->assertJsonStructure(['data' => CUSTOMER_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent customer', function () {
    actingAsCustomerApiUser(['view_account_partner']);

    $this->getJson(customerRoute('show', 999999))
        ->assertNotFound();
});

it('cannot show a non-customer partner via the customers endpoint', function () {
    actingAsCustomerApiUser(['view_account_partner']);

    $vendor = Partner::factory()->create(['customer_rank' => 0, 'supplier_rank' => 1]);

    $this->getJson(customerRoute('show', $vendor))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a customer', function () {
    actingAsCustomerApiUser(['update_account_partner']);

    $customer = makeCustomer();

    $this->patchJson(customerRoute('update', $customer), ['name' => 'Updated Customer Name'])
        ->assertOk()
        ->assertJsonPath('message', 'Customer updated successfully.');

    $this->assertDatabaseHas('partners_partners', [
        'id'   => $customer->id,
        'name' => 'Updated Customer Name',
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('soft deletes a customer', function () {
    actingAsCustomerApiUser(['delete_account_partner']);

    $customer = makeCustomer();

    $this->deleteJson(customerRoute('destroy', $customer))
        ->assertOk()
        ->assertJsonPath('message', 'Customer deleted successfully.');

    $this->assertSoftDeleted('partners_partners', ['id' => $customer->id]);
});

// ── Restore ────────────────────────────────────────────────────────────────────

it('requires permission to restore a customer', function () {
    actingAsCustomerApiUser();

    $customer = makeCustomer();
    $customer->delete();

    $this->postJson(customerRoute('restore', $customer))
        ->assertForbidden();
});

it('restores a soft-deleted customer', function () {
    actingAsCustomerApiUser(['restore_account_partner']);

    $customer = makeCustomer();
    $customer->delete();

    $this->postJson(customerRoute('restore', $customer))
        ->assertOk()
        ->assertJsonPath('message', 'Customer restored successfully.');

    $this->assertDatabaseHas('partners_partners', [
        'id'         => $customer->id,
        'deleted_at' => null,
    ]);
});

// ── Force Delete ───────────────────────────────────────────────────────────────

it('requires permission to force delete a customer', function () {
    actingAsCustomerApiUser();

    $customer = makeCustomer();
    $customer->delete();

    $this->deleteJson(customerRoute('force-destroy', $customer))
        ->assertForbidden();
});

it('permanently deletes a customer', function () {
    actingAsCustomerApiUser(['force_delete_account_partner']);

    $customer = makeCustomer();
    $customer->delete();

    $this->deleteJson(customerRoute('force-destroy', $customer))
        ->assertJsonPath('message', 'Customer permanently deleted.');

    $this->assertDatabaseMissing('partners_partners', ['id' => $customer->id]);
});
