<?php

use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Enums\AddressType;
use Webkul\Partner\Models\Partner;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

beforeEach(function () {
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsAddressApiUser(array $permissions = []): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function createParentPartner(): Partner
{
    return Partner::factory()->create([
        'account_type' => AccountType::INDIVIDUAL,
        'company_id'   => null,
        'title_id'     => null,
        'industry_id'  => null,
    ]);
}

function createAddressRecord(Partner $partner, array $overrides = []): Partner
{
    return Partner::factory()->create(array_merge([
        'account_type' => AccountType::ADDRESS,
        'sub_type'     => AddressType::INVOICE,
        'parent_id'    => $partner->id,
        'company_id'   => null,
        'title_id'     => null,
        'industry_id'  => null,
    ], $overrides));
}

function addressRoute(string $action, Partner $partner, mixed $address = null): string
{
    $name = "admin.api.v1.partners.partners.addresses.{$action}";

    return $address ? route($name, [$partner, $address]) : route($name, $partner);
}

it('requires authentication to list addresses', function () {
    $partner = createParentPartner();

    $this->getJson(addressRoute('index', $partner))->assertUnauthorized();
});

it('forbids listing addresses without view permission on parent partner', function () {
    $partner = createParentPartner();
    actingAsAddressApiUser();

    $this->getJson(addressRoute('index', $partner))->assertForbidden();
});

it('lists addresses for authorized users', function () {
    actingAsAddressApiUser(['view_partner_partner']);
    $partner = createParentPartner();
    createAddressRecord($partner);
    createAddressRecord($partner);

    $this->getJson(addressRoute('index', $partner))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates an address for authorized users', function () {
    actingAsAddressApiUser(['update_partner_partner']);
    $partner = createParentPartner();

    $payload = Partner::factory()->make([
        'sub_type'    => AddressType::INVOICE,
        'name'        => fake()->name(),
        'company_id'  => null,
        'title_id'    => null,
        'industry_id' => null,
    ])->toArray();

    $this->postJson(addressRoute('store', $partner), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Address created successfully.');
});

it('shows an address for authorized users', function () {
    actingAsAddressApiUser(['view_partner_partner']);
    $partner = createParentPartner();
    $address = createAddressRecord($partner);

    $this->getJson(addressRoute('show', $partner, $address))
        ->assertOk()
        ->assertJsonPath('data.id', $address->id);
});

it('updates an address for authorized users', function () {
    actingAsAddressApiUser(['update_partner_partner']);
    $partner = createParentPartner();
    $address = createAddressRecord($partner);

    $this->patchJson(addressRoute('update', $partner, $address), ['name' => 'Updated Address'])
        ->assertOk()
        ->assertJsonPath('message', 'Address updated successfully.')
        ->assertJsonPath('data.name', 'Updated Address');
});

it('deletes, restores and force deletes an address for authorized users', function () {
    actingAsAddressApiUser(['update_partner_partner']);
    $partner = createParentPartner();
    $address = createAddressRecord($partner);

    $this->deleteJson(addressRoute('destroy', $partner, $address))
        ->assertOk()
        ->assertJsonPath('message', 'Address deleted successfully.');

    $this->postJson(addressRoute('restore', $partner, $address->id))
        ->assertOk()
        ->assertJsonPath('message', 'Address restored successfully.');

    $address->delete();

    $this->deleteJson(addressRoute('force-destroy', $partner, $address->id))
        ->assertOk()
        ->assertJsonPath('message', 'Address permanently deleted.');
});
