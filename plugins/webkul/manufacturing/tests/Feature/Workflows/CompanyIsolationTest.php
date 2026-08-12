<?php

use Webkul\Manufacturing\Models\WorkCenter;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('manufacturing');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('hides work centers owned by another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $ownWorkCenter = WorkCenter::factory()->company($companyA)->create();
    $otherWorkCenter = WorkCenter::factory()->company($companyB)->create();

    CompanyHelper::actingAsCompanyUser($companyA);

    $visible = WorkCenter::query()->pluck('id');

    expect($visible)->toContain($ownWorkCenter->id)
        ->not->toContain($otherWorkCenter->id);
});

it('stamps a new work center with the active company', function () {
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyB);

    $workCenter = WorkCenter::factory()->create();

    expect($workCenter->company_id)->toBe($companyB->id);
});
