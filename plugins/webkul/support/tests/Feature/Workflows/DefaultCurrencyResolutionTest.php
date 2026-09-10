<?php

use Webkul\Support\Models\Country;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('derives the currency from the country when one is given', function () {
    $currency = Currency::factory()->create(['name' => 'DCR', 'active' => false]);

    $country = Country::factory()->create(['currency_id' => $currency->id]);

    expect(Currency::resolveDefault($country)?->id)->toBe($currency->id);
});

it('falls back to the configured app currency when no country is given', function () {
    $currency = Currency::factory()->create(['name' => 'DCF']);

    config(['app.currency' => 'DCF']);

    expect(Currency::resolveDefault()?->id)->toBe($currency->id);
});

it('falls back to the configured app currency when the country has no currency', function () {
    $currency = Currency::factory()->create(['name' => 'DCN']);

    config(['app.currency' => 'DCN']);

    $country = Country::factory()->create(['currency_id' => null]);

    expect(Currency::resolveDefault($country)?->id)->toBe($currency->id);
});

it('prefers the country currency over the configured app currency', function () {
    Currency::factory()->create(['name' => 'DCA']);

    $countryCurrency = Currency::factory()->create(['name' => 'DCB']);

    config(['app.currency' => 'DCA']);

    $country = Country::factory()->create(['currency_id' => $countryCurrency->id]);

    expect(Currency::resolveDefault($country)?->id)->toBe($countryCurrency->id);
});

it('still resolves a currency when the configured app currency does not exist', function () {
    config(['app.currency' => 'NOPE']);

    expect(Currency::resolveDefault())->not->toBeNull();
});

it('finds a currency by its iso code', function () {
    $currency = Currency::factory()->create(['name' => 'DCI']);

    expect(Currency::findByCode('DCI')?->id)->toBe($currency->id);
});

it('returns no currency for a blank code', function () {
    expect(Currency::findByCode(null))->toBeNull()
        ->and(Currency::findByCode(''))->toBeNull();
});

it('activates the currency assigned to a company', function () {
    $currency = Currency::factory()->create(['active' => false]);

    CompanyHelper::company(['currency_id' => $currency->id]);

    expect($currency->refresh()->active)->toBeTrue();
});

it('saves a company that has no currency without failing', function () {
    $company = CompanyHelper::company();

    $company->update(['currency_id' => null]);

    expect($company->refresh()->currency_id)->toBeNull();
});
