<?php

use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const VENDOR_JSON_STRUCTURE = [
    'id',
    'name',
    'account_type',
];

const VENDOR_REQUIRED_FIELDS = [
    'name',
    'account_type',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsVendorApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function vendorRoute(string $action, mixed $vendor = null): string
{
    $name = "admin.api.v1.accounts.vendors.{$action}";

    return $vendor ? route($name, $vendor) : route($name);
}

function vendorPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name'         => 'Test Vendor',
        'account_type' => AccountType::COMPANY->value,
    ], $overrides);
}

function makeVendor(array $attributes = []): Partner
{
    return Partner::factory()->create(array_merge(['supplier_rank' => 1], $attributes));
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list vendors', function () {
    $this->getJson(vendorRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a vendor', function () {
    $this->postJson(vendorRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing vendors without permission', function () {
    actingAsVendorApiUser();

    $this->getJson(vendorRoute('index'))
        ->assertForbidden();
});

it('forbids creating a vendor without permission', function () {
    actingAsVendorApiUser();

    $this->postJson(vendorRoute('store'), vendorPayload())
        ->assertForbidden();
});

it('forbids updating a vendor without permission', function () {
    actingAsVendorApiUser();

    $vendor = makeVendor();

    $this->patchJson(vendorRoute('update', $vendor), [])
        ->assertForbidden();
});

it('forbids deleting a vendor without permission', function () {
    actingAsVendorApiUser();

    $vendor = makeVendor();

    $this->deleteJson(vendorRoute('destroy', $vendor))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists vendors for authorized users', function () {
    actingAsVendorApiUser(['view_any_account_partner']);

    makeVendor();
    makeVendor();
    makeVendor();

    $this->getJson(vendorRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('only returns vendors with supplier_rank > 0 in the vendor list', function () {
    actingAsVendorApiUser(['view_any_account_partner']);

    makeVendor();
    Partner::factory()->create(['supplier_rank' => 0, 'customer_rank' => 1]);

    $response = $this->getJson(vendorRoute('index'))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->all();

    foreach ($ids as $id) {
        $partner = Partner::withTrashed()->find($id);
        expect($partner->supplier_rank)->toBeGreaterThan(0);
    }
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a vendor and auto-sets supplier_rank to 1', function () {
    actingAsVendorApiUser(['create_account_partner']);

    $payload = vendorPayload();

    $response = $this->postJson(vendorRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Vendor created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => VENDOR_JSON_STRUCTURE]);

    $vendorId = $response->json('data.id');

    $this->assertDatabaseHas('partners_partners', [
        'id'            => $vendorId,
        'name'          => $payload['name'],
        'supplier_rank' => 1,
    ]);
});

it('validates required fields when creating a vendor', function (string $field) {
    actingAsVendorApiUser(['create_account_partner']);

    $payload = vendorPayload();
    unset($payload[$field]);

    $this->postJson(vendorRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(VENDOR_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a vendor for authorized users', function () {
    actingAsVendorApiUser(['view_account_partner']);

    $vendor = makeVendor();

    $this->getJson(vendorRoute('show', $vendor))
        ->assertOk()
        ->assertJsonPath('data.id', $vendor->id)
        ->assertJsonStructure(['data' => VENDOR_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent vendor', function () {
    actingAsVendorApiUser(['view_account_partner']);

    $this->getJson(vendorRoute('show', 999999))
        ->assertNotFound();
});

it('cannot show a non-vendor partner via the vendors endpoint', function () {
    actingAsVendorApiUser(['view_account_partner']);

    $customer = Partner::factory()->create(['supplier_rank' => 0, 'customer_rank' => 1]);

    $this->getJson(vendorRoute('show', $customer))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a vendor', function () {
    actingAsVendorApiUser(['update_account_partner']);

    $vendor = makeVendor();

    $this->patchJson(vendorRoute('update', $vendor), ['name' => 'Updated Vendor Name'])
        ->assertOk()
        ->assertJsonPath('message', 'Vendor updated successfully.');

    $this->assertDatabaseHas('partners_partners', [
        'id'   => $vendor->id,
        'name' => 'Updated Vendor Name',
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('soft deletes a vendor', function () {
    actingAsVendorApiUser(['delete_account_partner']);

    $vendor = makeVendor();

    $this->deleteJson(vendorRoute('destroy', $vendor))
        ->assertOk()
        ->assertJsonPath('message', 'Vendor deleted successfully.');

    $this->assertSoftDeleted('partners_partners', ['id' => $vendor->id]);
});

// ── Restore ────────────────────────────────────────────────────────────────────

it('requires permission to restore a vendor', function () {
    actingAsVendorApiUser();

    $vendor = makeVendor();
    $vendor->delete();

    $this->postJson(vendorRoute('restore', $vendor))
        ->assertForbidden();
});

it('restores a soft-deleted vendor', function () {
    actingAsVendorApiUser(['restore_account_partner']);

    $vendor = makeVendor();
    $vendor->delete();

    $this->postJson(vendorRoute('restore', $vendor))
        ->assertOk()
        ->assertJsonPath('message', 'Vendor restored successfully.');

    $this->assertDatabaseHas('partners_partners', [
        'id'         => $vendor->id,
        'deleted_at' => null,
    ]);
});

// ── Force Delete ───────────────────────────────────────────────────────────────

it('requires permission to force delete a vendor', function () {
    actingAsVendorApiUser();

    $vendor = makeVendor();
    $vendor->delete();

    $this->deleteJson(vendorRoute('force-destroy', $vendor))
        ->assertForbidden();
});

it('permanently deletes a vendor', function () {
    actingAsVendorApiUser(['force_delete_account_partner']);

    $vendor = makeVendor();
    $vendor->delete();

    $this->deleteJson(vendorRoute('force-destroy', $vendor))
        ->assertJsonPath('message', 'Vendor permanently deleted.');

    $this->assertDatabaseMissing('partners_partners', ['id' => $vendor->id]);
});
