<?php

use Webkul\Sale\Models\Team;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('hides sales teams owned by another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $ownTeam = Team::factory()->company($companyA)->create();
    $otherTeam = Team::factory()->company($companyB)->create();

    CompanyHelper::actingAsCompanyUser($companyA);

    $visible = Team::query()->pluck('id');

    expect($visible)->toContain($ownTeam->id)
        ->not->toContain($otherTeam->id);
});

it('stamps a new sales team with the active company', function () {
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyB);

    $team = Team::factory()->create();

    expect($team->company_id)->toBe($companyB->id);
});
