<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages\EditActivityPlan;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages\ListActivityPlans;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages\ViewActivityPlan;
use Webkul\Crm\Models\ActivityPlan;
use Webkul\Support\Models\ActivityPlan as SupportActivityPlan;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

it('lists only crm activity plans', function () {
    CRMHelper::actingAsCRMUser(['activity::plan']);

    $crmPlan = ActivityPlan::query()->create([
        'name'       => 'Onboarding (crm test)',
        'plugin'     => 'crm',
        'company_id' => CRMHelper::companyId(),
        'is_active'  => true,
    ]);

    $salesPlan = SupportActivityPlan::query()->create([
        'name'       => 'Order follow-up (sales test)',
        'plugin'     => 'sales',
        'company_id' => CRMHelper::companyId(),
        'is_active'  => true,
    ]);

    Livewire::test(ListActivityPlans::class)
        ->assertCanSeeTableRecords([$crmPlan])
        ->assertCanNotSeeTableRecords([$salesPlan]);
});

it('uses the crm model and has no separate create page', function () {
    expect(ActivityPlanResource::getModel())->toBe(ActivityPlan::class)
        ->and(ActivityPlanResource::getPages())->toHaveKeys(['index', 'view', 'edit'])
        ->and(ActivityPlanResource::getPages())->not->toHaveKey('create');
});

it('creates an activity plan owned by crm from the list page', function () {
    CRMHelper::actingAsCRMUser(['activity::plan']);

    Livewire::test(ListActivityPlans::class)
        ->callAction('create', data: [
            'name'       => 'Qualification (crm test)',
            'company_id' => CRMHelper::companyId(),
            'is_active'  => true,
        ])
        ->assertHasNoActionErrors();

    $plan = ActivityPlan::query()->where('name', 'Qualification (crm test)')->first();

    expect($plan)->not->toBeNull()
        ->and($plan->plugin)->toBe('crm')
        ->and($plan->is_active)->toBeTrue();
});

it('requires a name to create an activity plan', function () {
    CRMHelper::actingAsCRMUser(['activity::plan']);

    Livewire::test(ListActivityPlans::class)
        ->callAction('create', data: ['name' => ''])
        ->assertHasActionErrors(['name' => 'required']);
});

it('shows a crm activity plan', function () {
    CRMHelper::actingAsCRMUser(['activity::plan']);

    $plan = ActivityPlan::query()->create([
        'name'       => 'Demo plan (crm test)',
        'plugin'     => 'crm',
        'company_id' => CRMHelper::companyId(),
        'is_active'  => true,
    ]);

    Livewire::test(ViewActivityPlan::class, ['record' => $plan->getRouteKey()])
        ->assertSee('Demo plan (crm test)');
});

it('edits a crm activity plan and keeps it owned by crm', function () {
    CRMHelper::actingAsCRMUser(['activity::plan']);

    $plan = ActivityPlan::query()->create([
        'name'       => 'Renewal (crm test)',
        'plugin'     => 'crm',
        'company_id' => CRMHelper::companyId(),
        'is_active'  => true,
    ]);

    Livewire::test(EditActivityPlan::class, ['record' => $plan->getRouteKey()])
        ->fillForm(['name' => 'Renewal reminder (crm test)', 'is_active' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    $plan->refresh();

    expect($plan->name)->toBe('Renewal reminder (crm test)')
        ->and($plan->is_active)->toBeFalse()
        ->and($plan->plugin)->toBe('crm');
});

it('forbids the crm activity plans with only the sales and support permissions', function () {
    CRMHelper::actingAs([
        'view_any_sale_activity::plan',
        'create_sale_activity::plan',
        'view_any_support_activity::plan',
    ]);

    Livewire::test(ListActivityPlans::class)->assertForbidden();
});

it('forbids editing a crm activity plan without the update permission', function () {
    CRMHelper::actingAs(['view_any_crm_activity::plan', 'view_crm_activity::plan']);

    $plan = ActivityPlan::query()->create([
        'name'       => 'Read only (crm test)',
        'plugin'     => 'crm',
        'company_id' => CRMHelper::companyId(),
        'is_active'  => true,
    ]);

    Livewire::test(EditActivityPlan::class, ['record' => $plan->getRouteKey()])
        ->assertForbidden();
});

it('scopes the activity plans resource query to crm records', function () {
    CRMHelper::actingAsCRMUser(['activity::plan']);

    $crmPlan = ActivityPlan::query()->create([
        'name'       => 'Scoped crm plan (crm test)',
        'plugin'     => 'crm',
        'company_id' => CRMHelper::companyId(),
        'is_active'  => true,
    ]);

    $salesPlan = SupportActivityPlan::query()->create([
        'name'       => 'Scoped sales plan (sales test)',
        'plugin'     => 'sales',
        'company_id' => CRMHelper::companyId(),
        'is_active'  => true,
    ]);

    $ids = ActivityPlanResource::getEloquentQuery()->pluck('id');

    expect($ids)->toContain($crmPlan->id)
        ->and($ids)->not->toContain($salesPlan->id);
});

it('does not open another app\'s activity plan from crm by its url', function (string $page) {
    CRMHelper::actingAsCRMUser(['activity::plan']);

    $salesPlan = SupportActivityPlan::query()->create([
        'name'       => 'Url guessed plan (sales test)',
        'plugin'     => 'sales',
        'company_id' => CRMHelper::companyId(),
        'is_active'  => true,
    ]);

    expect(fn () => Livewire::test($page, ['record' => $salesPlan->id]))->toThrow(ModelNotFoundException::class);
})->with([
    'view' => ViewActivityPlan::class,
    'edit' => EditActivityPlan::class,
]);
