<?php

use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource\Pages\ManageStages;
use Webkul\Crm\Models\Stage;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

it('lists the pipeline stages', function () {
    CRMHelper::actingAsCRMUser(['stage']);

    $qualified = Stage::factory()->create(['name' => 'Qualified (test)']);
    $won = Stage::factory()->won()->create(['name' => 'Won (test)']);

    Livewire::test(ManageStages::class)
        ->assertCanSeeTableRecords([$qualified, $won]);
});

it('creates a stage with the won flag and requirements', function () {
    CRMHelper::actingAsCRMUser(['stage']);

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
    CRMHelper::actingAsCRMUser(['stage']);

    Livewire::test(ManageStages::class)
        ->callAction('create', data: ['name' => ''])
        ->assertHasActionErrors(['name' => 'required']);
});

it('edits a stage', function () {
    CRMHelper::actingAsCRMUser(['stage']);

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
    CRMHelper::actingAsCRMUser(['stage']);

    $stage = Stage::factory()->create(['name' => 'Obsolete (test)']);

    Livewire::test(ManageStages::class)
        ->callAction(TestAction::make('delete')->table($stage));

    expect(Stage::query()->whereKey($stage->id)->exists())->toBeFalse();
});

it('hides edit and delete without the update and delete permissions', function () {
    CRMHelper::actingAs(['view_any_crm_stage', 'view_crm_stage']);

    $stage = Stage::factory()->create(['name' => 'Read only (test)']);

    Livewire::test(ManageStages::class)
        ->assertCanSeeTableRecords([$stage])
        ->assertActionHidden(TestAction::make('edit')->table($stage))
        ->assertActionHidden(TestAction::make('delete')->table($stage));
});

it('reorders stages with the reorder permission', function () {
    CRMHelper::actingAsCRMUser(['stage']);

    $first = Stage::factory()->create(['name' => 'First (test)']);
    $second = Stage::factory()->create(['name' => 'Second (test)']);

    expect($first->sort)->toBeLessThan($second->sort);

    $component = Livewire::test(ManageStages::class);

    expect($component->instance()->getTable()->isReorderable())->toBeTrue();

    $component->call('reorderTable', [(string) $second->id, (string) $first->id]);

    expect($second->refresh()->sort)->toBeLessThan($first->refresh()->sort);
});

it('does not allow reordering stages without the reorder permission', function () {
    CRMHelper::actingAs(CRMHelper::permissionsFor(['stage'], ['view_any', 'view', 'create', 'update']));

    $first = Stage::factory()->create(['name' => 'First (test)']);
    $second = Stage::factory()->create(['name' => 'Second (test)']);

    $component = Livewire::test(ManageStages::class);

    expect($component->instance()->getTable()->isReorderable())->toBeFalse();

    $component->call('reorderTable', [(string) $second->id, (string) $first->id]);

    expect($first->refresh()->sort)->toBeLessThan($second->refresh()->sort);
});

it('forbids the stages page without crm stage permissions', function () {
    CRMHelper::actingAs(['view_any_sale_team', 'view_any_crm_tag']);

    Livewire::test(ManageStages::class)->assertForbidden();
});

it('refuses to delete a stage that still has records from the stages page', function () {
    CRMHelper::actingAsCRMUser(['stage', 'opportunity']);

    $stage = Stage::factory()->create(['name' => 'Used stage (test)']);
    CRMHelper::opportunity(['stage_id' => $stage->id]);

    Livewire::test(ManageStages::class)
        ->callAction(TestAction::make('delete')->table($stage))
        ->assertNotified(__('crm::filament/clusters/configurations/resources/stage.table.actions.delete.in-use.title', ['name' => 'Used stage (test)']));

    expect(Stage::query()->whereKey($stage->id)->exists())->toBeTrue();
});

it('keeps a stage that still has records, even a lost one, when it is deleted', function () {
    CRMHelper::actingAs();

    $stage = Stage::factory()->create();
    $lost = CRMHelper::opportunity(['stage_id' => $stage->id]);
    $lost->delete();

    expect($stage->recordsCount())->toBe(1)
        ->and($stage->delete())->toBeFalse()
        ->and(Stage::query()->whereKey($stage->id)->exists())->toBeTrue();
});
