<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\KanbanOpportunities;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;

require_once __DIR__.'/../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function stageTeam(): Team
{
    return Team::factory()->create(['company_id' => CRMHelper::companyId(), 'is_active' => true]);
}

it('links stages to sales teams through crm_stage_team_rel', function () {
    CRMHelper::actingAs();

    $team = stageTeam();

    $stage = Stage::query()->create(['name' => 'Team stage '.uniqid(), 'team_ids' => [$team->id]]);

    $this->assertDatabaseHas('crm_stage_team_rel', ['stage_id' => $stage->id, 'team_id' => $team->id]);

    expect($stage->fresh()->team_ids)->toBe([$team->id])
        ->and($stage->fresh()->attributesToArray()['team_ids'])->toBe([$team->id]);

    $stage->fresh()->update(['name' => 'Renamed '.uniqid()]);

    expect($stage->fresh()->team_ids)->toBe([$team->id]);

    $stage->fresh()->update(['team_ids' => []]);

    expect($stage->fresh()->team_ids)->toBe([]);
});

it('shows a team its own and the shared stages only', function () {
    CRMHelper::actingAs();

    $team = stageTeam();
    $otherTeam = stageTeam();

    $shared = Stage::query()->create(['name' => 'Shared '.uniqid()]);
    $own = Stage::query()->create(['name' => 'Own '.uniqid(), 'team_ids' => [$team->id]]);
    $others = Stage::query()->create(['name' => 'Others '.uniqid(), 'team_ids' => [$otherTeam->id]]);

    $visible = Stage::query()->forTeam($team->id)->pluck('id');

    expect($visible)->toContain($shared->id)->toContain($own->id)->not->toContain($others->id)
        ->and(Stage::hiddenColumnsForTeam($team->id))->toContain((string) $others->id)->not->toContain((string) $own->id)
        ->and(Stage::hiddenColumnsForTeam(null))->toBe([]);
});

it('hides other teams\' stages on a team\'s pipeline board', function () {
    CRMHelper::actingAsCRMUser(['opportunity']);

    $team = stageTeam();
    $others = Stage::query()->create(['name' => 'Others '.uniqid(), 'team_ids' => [stageTeam()->id]]);

    $columns = collect(Livewire::withQueryParams(['team' => $team->id])->test(KanbanOpportunities::class)->instance()->getKanbanColumns())
        ->pluck('value')
        ->all();

    expect($columns)->not->toContain((string) $others->id);
});

it('links a stage added from a team\'s board to that team', function () {
    CRMHelper::actingAsCRMUser(['opportunity', 'stage']);

    $team = stageTeam();
    $name = 'Board stage '.uniqid();

    Livewire::withQueryParams(['team' => $team->id])
        ->test(KanbanOpportunities::class)
        ->call('addKanbanColumn', $name);

    expect(Stage::query()->where('name', $name)->first()?->team_ids)->toBe([$team->id]);
});
