<?php

use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource\Pages\ManageStages;
use Webkul\Crm\Models\Stage;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

it('lists the pipeline stages', function () {
    CrmTestHelper::actingAsCrmUser(['stage']);

    $qualified = Stage::factory()->create(['name' => 'Qualified (test)']);
    $won = Stage::factory()->won()->create(['name' => 'Won (test)']);

    Livewire::test(ManageStages::class)
        ->assertCanSeeTableRecords([$qualified, $won]);
});

it('creates a stage with the won flag and requirements', function () {
    CrmTestHelper::actingAsCrmUser(['stage']);

    Livewire::test(ManageStages::class)
        ->callAction('create', data: [
            'name'         => 'Negotiation (test)',
            'is_won'       => true,
            'requirements' => 'Signed NDA and budget confirmed',
        ])
        ->assertHasNoActionErrors();

    $stage = Stage::query()->where('name', 'Negotiation (test)')->first();

    expect($stage)->not->toBeNull()
        ->and($stage->is_won)->toBeTrue()
        ->and($stage->requirements)->toBe('Signed NDA and budget confirmed');
});

it('requires a name to create a stage', function () {
    CrmTestHelper::actingAsCrmUser(['stage']);

    Livewire::test(ManageStages::class)
        ->callAction('create', data: ['name' => ''])
        ->assertHasActionErrors(['name' => 'required']);
});

it('edits a stage', function () {
    CrmTestHelper::actingAsCrmUser(['stage']);

    $stage = Stage::factory()->create(['name' => 'Proposition (test)', 'is_won' => false]);

    Livewire::test(ManageStages::class)
        ->callAction(TestAction::make('edit')->table($stage), data: [
            'name'         => 'Proposal sent (test)',
            'is_won'       => true,
            'requirements' => 'Quotation sent',
        ])
        ->assertHasNoActionErrors();

    $stage->refresh();

    expect($stage->name)->toBe('Proposal sent (test)')
        ->and($stage->is_won)->toBeTrue()
        ->and($stage->requirements)->toBe('Quotation sent');
});

it('deletes a stage', function () {
    CrmTestHelper::actingAsCrmUser(['stage']);

    $stage = Stage::factory()->create(['name' => 'Obsolete (test)']);

    Livewire::test(ManageStages::class)
        ->callAction(TestAction::make('delete')->table($stage));

    expect(Stage::query()->whereKey($stage->id)->exists())->toBeFalse();
});

it('hides edit and delete without the update and delete permissions', function () {
    CrmTestHelper::actingAs(['view_any_crm_stage', 'view_crm_stage']);

    $stage = Stage::factory()->create(['name' => 'Read only (test)']);

    Livewire::test(ManageStages::class)
        ->assertCanSeeTableRecords([$stage])
        ->assertActionHidden(TestAction::make('edit')->table($stage))
        ->assertActionHidden(TestAction::make('delete')->table($stage));
});

it('reorders stages with the reorder permission', function () {
    CrmTestHelper::actingAsCrmUser(['stage']);

    $first = Stage::factory()->create(['name' => 'First (test)']);
    $second = Stage::factory()->create(['name' => 'Second (test)']);

    expect($first->sort)->toBeLessThan($second->sort);

    $component = Livewire::test(ManageStages::class);

    expect($component->instance()->getTable()->isReorderable())->toBeTrue();

    $component->call('reorderTable', [(string) $second->id, (string) $first->id]);

    expect($second->refresh()->sort)->toBeLessThan($first->refresh()->sort);
});

it('does not allow reordering stages without the reorder permission', function () {
    CrmTestHelper::actingAs(CrmTestHelper::permissionsFor(['stage'], ['view_any', 'view', 'create', 'update']));

    $first = Stage::factory()->create(['name' => 'First (test)']);
    $second = Stage::factory()->create(['name' => 'Second (test)']);

    $component = Livewire::test(ManageStages::class);

    expect($component->instance()->getTable()->isReorderable())->toBeFalse();

    $component->call('reorderTable', [(string) $second->id, (string) $first->id]);

    expect($first->refresh()->sort)->toBeLessThan($second->refresh()->sort);
});

it('forbids the stages page without crm stage permissions', function () {
    CrmTestHelper::actingAs(['view_any_sale_team', 'view_any_crm_tag']);

    Livewire::test(ManageStages::class)->assertForbidden();
});
