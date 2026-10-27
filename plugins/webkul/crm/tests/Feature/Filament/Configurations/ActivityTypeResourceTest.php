<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource\Pages\CreateActivityType;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource\Pages\EditActivityType;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource\Pages\ListActivityTypes;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\ActivityTypeResource\Pages\ViewActivityType;
use Webkul\Crm\Models\ActivityType;
use Webkul\Support\Enums\ActivityDelayFrom;
use Webkul\Support\Enums\ActivityDelayUnit;
use Webkul\Support\Models\ActivityType as SupportActivityType;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function crmActivityTypeAttributes(string $plugin, string $name): array
{
    return [
        'name'        => $name,
        'plugin'      => $plugin,
        'delay_count' => 0,
        'delay_unit'  => ActivityDelayUnit::DAYS->value,
        'delay_from'  => ActivityDelayFrom::PREVIOUS_ACTIVITY->value,
        'is_active'   => true,
    ];
}

it('lists only crm activity types', function () {
    CRMHelper::actingAsCRMUser(['activity::type']);

    $crmType = ActivityType::query()->create(crmActivityTypeAttributes('crm', 'Demo call (crm test)'));
    $salesType = SupportActivityType::query()->create(crmActivityTypeAttributes('sales', 'Order follow-up (sales test)'));

    Livewire::test(ListActivityTypes::class)
        ->assertCanSeeTableRecords([$crmType])
        ->assertCanNotSeeTableRecords([$salesType]);
});

it('uses the crm model for the activity types resource', function () {
    expect(ActivityTypeResource::getModel())->toBe(ActivityType::class);
});

it('creates an activity type owned by crm', function () {
    CRMHelper::actingAsCRMUser(['activity::type']);

    Livewire::test(CreateActivityType::class)
        ->fillForm([
            'name'        => 'Site visit (crm test)',
            'delay_count' => 2,
            'delay_unit'  => ActivityDelayUnit::DAYS->value,
            'delay_from'  => ActivityDelayFrom::CURRENT_DATE->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $activityType = ActivityType::query()->where('name', 'Site visit (crm test)')->first();

    expect($activityType)->not->toBeNull()
        ->and($activityType->plugin)->toBe('crm')
        ->and((int) $activityType->delay_count)->toBe(2);
});

it('requires a name to create an activity type', function () {
    CRMHelper::actingAsCRMUser(['activity::type']);

    Livewire::test(CreateActivityType::class)
        ->fillForm(['name' => ''])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

it('edits a crm activity type and keeps it owned by crm', function () {
    CRMHelper::actingAsCRMUser(['activity::type']);

    $activityType = ActivityType::query()->create(crmActivityTypeAttributes('crm', 'Call back (crm test)'));

    Livewire::test(EditActivityType::class, ['record' => $activityType->getRouteKey()])
        ->fillForm(['name' => 'Call back tomorrow (crm test)'])
        ->call('save')
        ->assertHasNoFormErrors();

    $activityType->refresh();

    expect($activityType->name)->toBe('Call back tomorrow (crm test)')
        ->and($activityType->plugin)->toBe('crm');
});

it('shows a crm activity type', function () {
    CRMHelper::actingAsCRMUser(['activity::type']);

    $activityType = ActivityType::query()->create(crmActivityTypeAttributes('crm', 'Lunch meeting (crm test)'));

    Livewire::test(ViewActivityType::class, ['record' => $activityType->getRouteKey()])
        ->assertSee('Lunch meeting (crm test)');
});

it('forbids the crm activity types with only the support and sales permissions', function () {
    CRMHelper::actingAs([
        'view_any_support_activity::type',
        'create_support_activity::type',
        'view_any_sale_activity::type',
        'create_sale_activity::type',
    ]);

    Livewire::test(ListActivityTypes::class)->assertForbidden();
    Livewire::test(CreateActivityType::class)->assertForbidden();
});

it('forbids editing a crm activity type without the update permission', function () {
    CRMHelper::actingAs(['view_any_crm_activity::type', 'view_crm_activity::type']);

    $activityType = ActivityType::query()->create(crmActivityTypeAttributes('crm', 'Read only (crm test)'));

    Livewire::test(EditActivityType::class, ['record' => $activityType->getRouteKey()])
        ->assertForbidden();
});

it('scopes the activity types resource query to crm records', function () {
    $crmType = ActivityType::query()->create(crmActivityTypeAttributes('crm', 'Scoped crm type (crm test)'));
    $salesType = SupportActivityType::query()->create(crmActivityTypeAttributes('sales', 'Scoped sales type (sales test)'));

    $ids = ActivityTypeResource::getEloquentQuery()->pluck('id');

    expect($ids)->toContain($crmType->id)
        ->and($ids)->not->toContain($salesType->id);
});

it('does not open another app\'s activity type from crm by its url', function (string $page) {
    CRMHelper::actingAsCRMUser(['activity::type']);

    $salesType = SupportActivityType::query()->create(crmActivityTypeAttributes('sales', 'Url guessed type (sales test)'));

    expect(fn () => Livewire::test($page, ['record' => $salesType->id]))->toThrow(ModelNotFoundException::class);
})->with([
    'view' => ViewActivityType::class,
    'edit' => EditActivityType::class,
]);

it('keeps another app\'s activity type owned by that app', function () {
    CRMHelper::actingAsCRMUser(['activity::type']);

    $salesType = SupportActivityType::query()->create(crmActivityTypeAttributes('sales', 'Untouched type (sales test)'));

    try {
        Livewire::test(EditActivityType::class, ['record' => $salesType->id])
            ->fillForm(['name' => 'Taken over'])
            ->call('save');
    } catch (ModelNotFoundException) {
        //
    }

    expect($salesType->fresh()->plugin)->toBe('sales')
        ->and($salesType->fresh()->name)->toBe('Untouched type (sales test)');
});
