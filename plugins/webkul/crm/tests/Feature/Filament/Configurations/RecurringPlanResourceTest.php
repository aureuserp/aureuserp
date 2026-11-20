<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RecurringPlanResource\Pages\ManageRecurringPlans;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\EditOpportunity;
use Webkul\Crm\Models\RecurringPlan;
use Webkul\Crm\Settings\CRMSettings;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(function () {
    CRMHelper::bootstrap();

    $settings = app(CRMSettings::class);
    $settings->use_recurring_revenues = true;
    $settings->save();
});

it('has the default recurring plans', function () {
    expect(RecurringPlan::query()->orderBy('sort')->pluck('number_of_months', 'name')->take(4)->all())
        ->toBe(['Monthly' => 1, 'Yearly' => 12, 'Over 3 years' => 36, 'Over 5 years' => 60]);
});

it('creates and edits recurring plans with the permission', function () {
    CRMHelper::actingAs(CRMHelper::permissionsFor(['recurring::plan']));

    $monthly = RecurringPlan::query()->where('name', 'Monthly')->firstOrFail();

    Livewire::test(ManageRecurringPlans::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$monthly])
        ->callAction('create', data: ['name' => 'Quarterly', 'number_of_months' => 3])
        ->assertHasNoActionErrors()
        ->callAction('create', data: ['name' => 'Broken', 'number_of_months' => 0])
        ->assertHasActionErrors(['number_of_months']);

    expect(RecurringPlan::query()->where('name', 'Quarterly')->value('number_of_months'))->toBe(3);
});

it('hides recurring plans when Recurring Revenue Plans is off', function () {
    CRMHelper::actingAs(CRMHelper::permissionsFor(['recurring::plan']));

    $settings = app(CRMSettings::class);
    $settings->use_recurring_revenues = false;
    $settings->save();

    expect(RecurringPlanResource::canViewAny())->toBeFalse();
});

it('stores the recurring revenue of an opportunity and its monthly amount', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $yearly = RecurringPlan::query()->where('name', 'Yearly')->firstOrFail();
    $opportunity = CRMHelper::opportunity();

    Livewire::test(EditOpportunity::class, ['record' => $opportunity->getRouteKey()])
        ->fillForm(['recurring_revenue' => 1200, 'recurring_plan_id' => null])
        ->call('save')
        ->assertHasFormErrors(['recurring_plan_id' => 'required'])
        ->fillForm(['recurring_revenue' => 1200, 'recurring_plan_id' => $yearly->id])
        ->call('save')
        ->assertHasNoFormErrors();

    $opportunity->refresh();

    expect((float) $opportunity->recurring_revenue)->toBe(1200.0)
        ->and($opportunity->recurringPlan->name)->toBe('Yearly')
        ->and($opportunity->monthlyRecurringRevenue())->toBe(100.0);
});
