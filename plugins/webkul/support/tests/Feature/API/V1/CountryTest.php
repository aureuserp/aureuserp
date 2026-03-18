<?php

use Webkul\Support\Models\Country;

require_once __DIR__.'/../../../Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../Helpers/TestBootstrapHelper.php';

const COUNTRY_JSON_STRUCTURE = [
    'id',
    'name',
    'code',
    'phone_code',
    'state_required',
    'zip_required',
    'currency_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();
    SecurityHelper::disableUserEvents();
});
afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingWithCountryPermissions(array $permissions): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function countryRoute(string $action, mixed $country = null): string
{
    $name = "admin.api.v1.support.countries.{$action}";

    return $country ? route($name, $country) : route($name);
}

it('requires authentication to list countries', function () {
    $this->getJson(countryRoute('index'))
        ->assertUnauthorized();
});

it('forbids listing countries without permission', function () {
    actingWithCountryPermissions([]);

    $this->getJson(countryRoute('index'))
        ->assertForbidden();
});

it('lists countries for authorized users', function () {
    actingWithCountryPermissions(['view_any_support_country']);

    $response = $this->getJson(countryRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data' => ['*' => COUNTRY_JSON_STRUCTURE]]);

    // Verify we have data (seeded countries)
    expect($response->json('data'))->not->toBeEmpty();
});

it('shows a country for authorized users', function () {
    actingWithCountryPermissions(['view_support_country']);

    $country = Country::factory()->create();

    $this->getJson(countryRoute('show', $country))
        ->assertOk()
        ->assertJsonPath('data.id', $country->id)
        ->assertJsonStructure(['data' => COUNTRY_JSON_STRUCTURE]);
});

it('forbids showing a country without permission', function () {
    actingWithCountryPermissions([]);

    $country = Country::factory()->create();

    $this->getJson(countryRoute('show', $country))
        ->assertForbidden();
});

it('returns 404 for non-existent country', function () {
    actingWithCountryPermissions(['view_support_country']);

    $this->getJson(countryRoute('show', 999999))
        ->assertNotFound();
});
