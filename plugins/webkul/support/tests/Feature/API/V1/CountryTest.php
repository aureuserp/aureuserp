<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Support\Models\Country;

require_once __DIR__.'/../../../Helpers/SecurityHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

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
    if (! Schema::hasTable('countries')) {
        test()->markTestSkipped(
            'Support plugin tables are missing. Install/migrate the support plugin before running this suite.'
        );
    }

    SecurityHelper::disableUserEvents();
});
afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsCountryApiUser(array $permissions = []): void
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

it('lists countries for authenticated users', function () {
    actingAsCountryApiUser();

    Country::factory()->count(2)->create();

    $this->getJson(countryRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => ['*' => COUNTRY_JSON_STRUCTURE]]);
});

it('shows a country for authenticated users', function () {
    actingAsCountryApiUser();

    $country = Country::factory()->create();

    $this->getJson(countryRoute('show', $country))
        ->assertOk()
        ->assertJsonPath('data.id', $country->id)
        ->assertJsonStructure(['data' => COUNTRY_JSON_STRUCTURE]);
});

it('returns 404 for non-existent country', function () {
    actingAsCountryApiUser();

    $this->getJson(countryRoute('show', 999999))
        ->assertNotFound();
});
