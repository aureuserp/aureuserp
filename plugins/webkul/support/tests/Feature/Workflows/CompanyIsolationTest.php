<?php

use Webkul\Support\Models\ActivityPlan;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Services\CompanyContext;

require_once __DIR__.'/../../Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('hides utm campaigns owned by another company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $ownCampaign = UtmCampaign::factory()->company($companyA)->create();
    $otherCampaign = UtmCampaign::factory()->company($companyB)->create();

    CompanyHelper::actingAsCompanyUser($companyA);

    $visible = UtmCampaign::query()->pluck('id');

    expect($visible)->toContain($ownCampaign->id)
        ->not->toContain($otherCampaign->id);
});

it('never resolves a current company outside the allowed set', function () {
    $allowed = CompanyHelper::company();
    $forbidden = CompanyHelper::company();

    $user = CompanyHelper::actingAsCompanyUser($allowed);

    $user->forceFill(['default_company_id' => $forbidden->id])->saveQuietly();

    app()->forgetInstance(CompanyContext::class);

    $context = app(CompanyContext::class);

    expect($context->allowedIds())->toBe([$allowed->id])
        ->and($context->currentId())->toBe($allowed->id)
        ->and($context->activeIds())->not->toContain($forbidden->id);
});

it('shows records from every active company when several are switched on', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();
    $companyC = CompanyHelper::company();

    $inA = UtmCampaign::factory()->company($companyA)->create();
    $inB = UtmCampaign::factory()->company($companyB)->create();
    $inC = UtmCampaign::factory()->company($companyC)->create();

    CompanyHelper::actingAsCompanyUser([$companyA, $companyB, $companyC], activeIds: [$companyA->id, $companyB->id]);

    $visible = UtmCampaign::query()->pluck('id');

    expect($visible)->toContain($inA->id)
        ->toContain($inB->id)
        ->not->toContain($inC->id);
});

it('narrows visibility when an active company is switched off', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $inA = UtmCampaign::factory()->company($companyA)->create();
    $inB = UtmCampaign::factory()->company($companyB)->create();

    CompanyHelper::actingAsCompanyUser([$companyA, $companyB]);

    expect(UtmCampaign::query()->pluck('id'))->toContain($inA->id)->toContain($inB->id);

    CompanyHelper::setActive([$companyA->id]);

    expect(UtmCampaign::query()->pluck('id'))->toContain($inA->id)
        ->not->toContain($inB->id);
});

it('ignores an active company the user is not allowed to access', function () {
    $allowed = CompanyHelper::company();
    $forbidden = CompanyHelper::company();

    $visibleCampaign = UtmCampaign::factory()->company($allowed)->create();
    $hiddenCampaign = UtmCampaign::factory()->company($forbidden)->create();

    CompanyHelper::actingAsCompanyUser($allowed, activeIds: [$allowed->id, $forbidden->id]);

    $visible = UtmCampaign::query()->pluck('id');

    expect($visible)->toContain($visibleCampaign->id)
        ->not->toContain($hiddenCampaign->id);
});

it('shows records with no company to every active company', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $shared = UtmCampaign::factory()->company($companyA)->create();
    $shared->forceFill(['company_id' => null])->saveQuietly();

    $owned = UtmCampaign::factory()->company($companyA)->create();

    CompanyHelper::actingAsCompanyUser($companyA);

    expect(UtmCampaign::query()->pluck('id'))->toContain($shared->id)
        ->toContain($owned->id);

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(UtmCampaign::query()->pluck('id'))->toContain($shared->id)
        ->not->toContain($owned->id);
});

it('leaves a new working calendar shared across companies', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $calendar = Calendar::factory()->create();

    expect($calendar->company_id)->toBeNull()
        ->and(Calendar::query()->pluck('id'))->toContain($calendar->id);

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(Calendar::query()->pluck('id'))->toContain($calendar->id);
});

it('leaves a new activity plan shared across companies', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $plan = ActivityPlan::query()->create(['name' => 'Shared plan '.uniqid()]);

    expect($plan->company_id)->toBeNull()
        ->and(ActivityPlan::query()->pluck('id'))->toContain($plan->id);

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(ActivityPlan::query()->pluck('id'))->toContain($plan->id);
});

it('leaves a new utm campaign shared across companies', function () {
    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser($companyA);

    $campaign = UtmCampaign::factory()->create();

    expect($campaign->company_id)->toBeNull();

    CompanyHelper::actingAsCompanyUser($companyB);

    expect(UtmCampaign::query()->pluck('id'))->toContain($campaign->id);
});
