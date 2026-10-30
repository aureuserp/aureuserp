<?php

use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource\Pages\ManageLostReasons;
use Webkul\Crm\Models\LostReason;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

it('shows only active lost reasons on the active tab', function () {
    CRMHelper::actingAsCRMUser(['lost::reason']);

    $active = LostReason::factory()->create(['name' => 'Too expensive (test)']);
    $archived = LostReason::factory()->archived()->create(['name' => 'Old reason (test)']);

    Livewire::test(ManageLostReasons::class)
        ->set('activeTab', 'active')
        ->assertCanSeeTableRecords([$active])
        ->assertCanNotSeeTableRecords([$archived]);
});

it('shows only archived lost reasons on the archived tab', function () {
    CRMHelper::actingAsCRMUser(['lost::reason']);

    $active = LostReason::factory()->create(['name' => 'Too expensive (test)']);
    $archived = LostReason::factory()->archived()->create(['name' => 'Old reason (test)']);

    Livewire::test(ManageLostReasons::class)
        ->set('activeTab', 'archived')
        ->assertCanSeeTableRecords([$archived])
        ->assertCanNotSeeTableRecords([$active]);
});

it('counts active and archived lost reasons in the tab badges', function () {
    CRMHelper::actingAsCRMUser(['lost::reason']);

    LostReason::factory()->archived()->create();

    $tabs = Livewire::test(ManageLostReasons::class)->instance()->getTabs();

    expect((int) $tabs['active']->getBadge())->toBe(LostReason::query()->count())
        ->and((int) $tabs['archived']->getBadge())->toBe(LostReason::onlyTrashed()->count())
        ->and((int) $tabs['archived']->getBadge())->toBeGreaterThanOrEqual(1);
});

it('creates a lost reason', function () {
    $user = CRMHelper::actingAsCRMUser(['lost::reason']);

    Livewire::test(ManageLostReasons::class)
        ->callAction('create', data: ['name' => 'Chose a competitor (test)'])
        ->assertHasNoActionErrors();

    $lostReason = LostReason::query()->where('name', 'Chose a competitor (test)')->first();

    expect($lostReason)->not->toBeNull()
        ->and($lostReason->creator_id)->toBe($user->id)
        ->and($lostReason->trashed())->toBeFalse();
});

it('requires a name to create a lost reason', function () {
    CRMHelper::actingAsCRMUser(['lost::reason']);

    Livewire::test(ManageLostReasons::class)
        ->callAction('create', data: ['name' => ''])
        ->assertHasActionErrors(['name' => 'required']);
});

it('edits an active lost reason', function () {
    CRMHelper::actingAsCRMUser(['lost::reason']);

    $lostReason = LostReason::factory()->create(['name' => 'No budget (test)']);

    Livewire::test(ManageLostReasons::class)
        ->callAction(TestAction::make('edit')->table($lostReason), data: ['name' => 'No budget this year (test)'])
        ->assertHasNoActionErrors();

    expect($lostReason->refresh()->name)->toBe('No budget this year (test)');
});

it('soft-deletes a lost reason with the core delete action', function () {
    CRMHelper::actingAsCRMUser(['lost::reason']);

    $lostReason = LostReason::factory()->create();

    Livewire::test(ManageLostReasons::class)
        ->callAction(TestAction::make('delete')->table($lostReason));

    $lostReason = LostReason::withTrashed()->find($lostReason->id);

    expect($lostReason)->not->toBeNull()
        ->and($lostReason->trashed())->toBeTrue();
});

it('hides edit and delete and shows restore on a deleted lost reason', function () {
    CRMHelper::actingAsCRMUser(['lost::reason']);

    $archived = LostReason::factory()->archived()->create();

    Livewire::test(ManageLostReasons::class)
        ->set('activeTab', 'archived')
        ->assertActionHidden(TestAction::make('edit')->table($archived))
        ->assertActionHidden(TestAction::make('delete')->table($archived))
        ->assertActionVisible(TestAction::make('restore')->table($archived))
        ->assertActionVisible(TestAction::make('forceDelete')->table($archived));
});

it('restores an archived lost reason', function () {
    CRMHelper::actingAsCRMUser(['lost::reason']);

    $archived = LostReason::factory()->archived()->create();

    Livewire::test(ManageLostReasons::class)
        ->set('activeTab', 'archived')
        ->callAction(TestAction::make('restore')->table($archived));

    expect(LostReason::query()->whereKey($archived->id)->exists())->toBeTrue();
});

it('force deletes an archived lost reason', function () {
    CRMHelper::actingAsCRMUser(['lost::reason']);

    $archived = LostReason::factory()->archived()->create();

    Livewire::test(ManageLostReasons::class)
        ->set('activeTab', 'archived')
        ->callAction(TestAction::make('forceDelete')->table($archived));

    expect(LostReason::withTrashed()->whereKey($archived->id)->exists())->toBeFalse();
});

it('hides restore and force delete without those permissions', function () {
    CRMHelper::actingAs(CRMHelper::permissionsFor(['lost::reason'], ['view_any', 'view', 'create', 'update', 'delete']));

    $archived = LostReason::factory()->archived()->create();

    Livewire::test(ManageLostReasons::class)
        ->set('activeTab', 'archived')
        ->assertActionHidden(TestAction::make('restore')->table($archived))
        ->assertActionHidden(TestAction::make('forceDelete')->table($archived));
});

it('forbids the lost reasons page without crm lost reason permissions', function () {
    CRMHelper::actingAs(['view_any_crm_tag', 'view_any_crm_stage']);

    Livewire::test(ManageLostReasons::class)->assertForbidden();
});
