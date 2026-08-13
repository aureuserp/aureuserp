<?php

use Webkul\Inventory\Models\StorageCategory;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('hides storage categories owned by another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $ownCategory = StorageCategory::factory()->company($companyA)->create();
    $otherCategory = StorageCategory::factory()->company($companyB)->create();

    CompanyHelper::actingAsCompanyUser($companyA);

    $visible = StorageCategory::query()->pluck('id');

    expect($visible)->toContain($ownCategory->id)
        ->not->toContain($otherCategory->id);
});

it('stamps a new storage category with the active company', function () {
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyB);

    $category = StorageCategory::factory()->create();

    expect($category->company_id)->toBe($companyB->id);
});
