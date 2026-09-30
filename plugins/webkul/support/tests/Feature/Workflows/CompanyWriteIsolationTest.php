<?php

use Illuminate\Support\Facades\Validator;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Rules\WithinAllowedCompanies;
use Webkul\Support\Support\CompanyConsistencyGuard;

require_once __DIR__.'/../../Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('fails validation for a company outside the allowed set', function () {
    $allowed = CompanyHelper::company();
    $forbidden = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($allowed);

    $validator = Validator::make(
        ['company_id' => $forbidden->id],
        ['company_id' => [new WithinAllowedCompanies]],
    );

    expect($validator->fails())->toBeTrue();
});

it('passes validation for a company inside the allowed set', function () {
    $allowed = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($allowed);

    $validator = Validator::make(
        ['company_id' => $allowed->id],
        ['company_id' => [new WithinAllowedCompanies]],
    );

    expect($validator->fails())->toBeFalse();
});

it('names a conflicting record the user is allowed to see', function () {
    $active = CompanyHelper::company();
    $otherAllowed = CompanyHelper::company();

    $campaign = UtmCampaign::factory()->company($otherAllowed)->create(['name' => 'Visible Campaign']);

    CompanyHelper::actingAsCompanyUser([$active, $otherAllowed], activeIds: [$active->id]);

    $conflicts = CompanyConsistencyGuard::detect(
        $active->id,
        [['campaign_id' => $campaign->id]],
        ['campaign_id' => UtmCampaign::class],
    );

    expect($conflicts)->toBe(['Visible Campaign']);
});

it('only exposes the id of a conflicting record outside the allowed companies', function () {
    $active = CompanyHelper::company();
    $forbidden = CompanyHelper::company();

    $campaign = UtmCampaign::factory()->company($forbidden)->create(['name' => 'Secret Campaign']);

    CompanyHelper::actingAsCompanyUser($active);

    $conflicts = CompanyConsistencyGuard::detect(
        $active->id,
        [['campaign_id' => $campaign->id]],
        ['campaign_id' => UtmCampaign::class],
    );

    expect($conflicts)->toBe([$campaign->id]);
});
