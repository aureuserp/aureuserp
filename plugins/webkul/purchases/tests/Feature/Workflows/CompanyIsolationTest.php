<?php

use Webkul\Purchase\Models\Requisition;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('hides requisitions owned by another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $ownRequisition = Requisition::factory()->company($companyA)->create();
    $otherRequisition = Requisition::factory()->company($companyB)->create();

    CompanyHelper::actingAsCompanyUser($companyA);

    $visible = Requisition::query()->pluck('id');

    expect($visible)->toContain($ownRequisition->id)
        ->not->toContain($otherRequisition->id);
});

it('stamps a new requisition with the active company', function () {
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyB);

    $requisition = Requisition::factory()->create();

    expect($requisition->company_id)->toBe($companyB->id);
});
