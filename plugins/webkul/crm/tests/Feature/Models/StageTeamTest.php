<?php

use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource\Pages\ManageStages;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function stageTeam(): Team
{
    return Team::factory()->create(['company_id' => CRMHelper::companyId(), 'is_active' => true]);
}

it('links stages to sales teams through crm_stage_team_rel', function () {
    CRMHelper::actingAs();

    $team = stageTeam();

    $stage = Stage::query()->create(['name' => 'Team stage '.uniqid()]);
    $stage->teams()->sync([$team->id]);

    $this->assertDatabaseHas('crm_stage_team_rel', ['stage_id' => $stage->id, 'team_id' => $team->id]);

    $stage->fresh()->update(['name' => 'Renamed '.uniqid()]);

    expect($stage->fresh()->teams->modelKeys())->toBe([$team->id]);
});

it('creates a stage with its sales teams from the stages page', function () {
    CRMHelper::actingAsCRMUser(['stage']);

    $team = stageTeam();
    $name = 'Created with team '.uniqid();

    Livewire::test(ManageStages::class)
        ->callAction('create', data: ['name' => $name, 'teams' => [$team->id]])
        ->assertHasNoActionErrors();

    expect(Stage::query()->where('name', $name)->first()?->teams->modelKeys())->toBe([$team->id]);
});

it('saves the sales teams of a stage from the stage form without touching other companies\' teams', function () {
    CRMHelper::actingAsCRMUser(['stage']);

    $team = stageTeam();
    $otherCompanyTeam = Team::factory()->create(['company_id' => Company::factory()->create()->id, 'is_active' => true]);

    $stage = Stage::query()->create(['name' => 'Form stage '.uniqid()]);
    $stage->teams()->sync([$otherCompanyTeam->id]);

    Livewire::test(ManageStages::class)
        ->mountAction(TestAction::make('edit')->table($stage))
        ->assertSchemaStateSet(['teams' => []])
        ->setActionData(['name' => $stage->name, 'teams' => [$team->id]])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    expect($stage->fresh()->teams()->withoutGlobalScopes()->pluck('sales_teams.id')->sort()->values()->all())
        ->toBe(collect([$team->id, $otherCompanyTeam->id])->sort()->values()->all());
});

it('shows a team its own and the shared stages only', function () {
    CRMHelper::actingAs();

    $team = stageTeam();
    $otherTeam = stageTeam();

    $shared = Stage::query()->create(['name' => 'Shared '.uniqid()]);
    $own = Stage::query()->create(['name' => 'Own '.uniqid()]);
    $own->teams()->sync([$team->id]);
    $others = Stage::query()->create(['name' => 'Others '.uniqid()]);
    $others->teams()->sync([$otherTeam->id]);

    $visible = Stage::query()->forTeam($team->id)->pluck('id');

    expect($visible)->toContain($shared->id)->toContain($own->id)->not->toContain($others->id)
        ->and(Stage::hiddenColumnsForTeam($team->id))->toContain((string) $others->id)->not->toContain((string) $own->id)
        ->and(Stage::hiddenColumnsForTeam(null))->toBe([]);
});

it('hides other teams\' stages on a team\'s pipeline board', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $team = stageTeam();
    $others = Stage::query()->create(['name' => 'Others '.uniqid()]);
    $others->teams()->sync([stageTeam()->id]);

    $columns = collect(Livewire::withQueryParams(['team' => $team->id])->test(ListOpportunities::class)->instance()->getKanbanColumns())
        ->pluck('value')
        ->all();

    expect($columns)->not->toContain((string) $others->id);
});

it('links a stage added from a team\'s board to that team', function () {
    CRMHelper::actingAsCRMUser(['opportunity', 'stage']);

    $team = stageTeam();
    $name = 'Board stage '.uniqid();

    Livewire::withQueryParams(['team' => $team->id])
        ->test(ListOpportunities::class)
        ->call('addKanbanColumn', $name);

    expect(Stage::query()->where('name', $name)->first()?->teams->modelKeys())->toBe([$team->id]);
});

it('pre-selects the board\'s team in the add stage modal and saves the chosen teams', function () {
    CRMHelper::actingAsCRMUser(['opportunity', 'stage']);

    $team = stageTeam();
    $otherTeam = stageTeam();
    $name = 'Modal team stage '.uniqid();

    Livewire::withQueryParams(['team' => $team->id])
        ->test(ListOpportunities::class)
        ->mountAction('addKanbanColumn')
        ->assertSchemaStateSet(['teams' => [$team->id]])
        ->setActionData(['name' => $name, 'teams' => [$otherTeam->id]])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    expect(Stage::query()->where('name', $name)->first()?->teams->modelKeys())->toBe([$otherTeam->id]);
});

it('edits the teams of a stage from the board column settings', function () {
    CRMHelper::actingAsCRMUser(['opportunity', 'stage']);

    $team = stageTeam();
    $stage = Stage::query()->create(['name' => 'Column team stage '.uniqid()]);

    Livewire::withQueryParams(['activeTableView' => 'default'])->test(ListOpportunities::class)
        ->callAction('editKanbanColumn', data: ['name' => $stage->name, 'teams' => [$team->id]], arguments: ['column' => (string) $stage->id])
        ->assertHasNoActionErrors();

    expect($stage->fresh()->teams->modelKeys())->toBe([$team->id]);
});
