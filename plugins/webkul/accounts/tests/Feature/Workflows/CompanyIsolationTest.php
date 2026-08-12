<?php

use Webkul\Account\Models\Tax;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('hides taxes owned by another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $ownTax = Tax::factory()->company($companyA)->create();
    $otherTax = Tax::factory()->company($companyB)->create();

    CompanyHelper::actingAsCompanyUser($companyA);

    $visible = Tax::query()->pluck('id');

    expect($visible)->toContain($ownTax->id)
        ->not->toContain($otherTax->id);
});

it('stamps a new tax with the active company', function () {
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyB);

    $tax = Tax::factory()->create();

    expect($tax->company_id)->toBe($companyB->id);
});
