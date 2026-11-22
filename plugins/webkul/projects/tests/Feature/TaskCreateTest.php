<?php

use Livewire\Livewire;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageTaskStages;
use Webkul\Project\Filament\Resources\TaskResource\Pages\CreateTask;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
    FilamentHelper::bootAdminPanel();
    FilamentHelper::actingAs([
        'view_any_project_task',
        'create_project_task',
        'view_any_project_project',
        'view_project_project',
        'update_project_project',
        'view_any_project_task::stage',
        'create_project_task::stage',
    ]);
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('allows creating a task without a project or task stages', function () {
    Livewire::test(CreateTask::class)
        ->assertFormFieldIsHidden('stage_id')
        ->fillForm([
            'title' => 'Standalone Task',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $task = Task::where('title', 'Standalone Task')->first();

    expect($task)->not->toBeNull()
        ->and($task->stage_id)->toBeNull();
});

it('requires and defaults stage_id when the selected project has stages', function () {
    $project = Project::factory()->create();
    $stage = TaskStage::factory()->create([
        'project_id' => $project->id,
        'sort'       => 1,
    ]);

    Livewire::test(CreateTask::class)
        ->fillForm([
            'title'      => 'Project Task',
            'project_id' => $project->id,
        ])
        ->assertFormFieldIsVisible('stage_id')
        ->assertFormSet([
            'stage_id' => $stage->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $task = Task::where('title', 'Project Task')->first();

    expect($task)->not->toBeNull()
        ->and($task->project_id)->toBe($project->id)
        ->and($task->stage_id)->toBe($stage->id);
});

it('prefills project, partner, and default stage when creating a task from a project', function () {
    $partner = Partner::factory()->create();
    $project = Project::factory()->create([
        'partner_id' => $partner->id,
    ]);
    $stage = TaskStage::factory()->create([
        'project_id' => $project->id,
        'sort'       => 1,
    ]);

    Livewire::withQueryParams(['project_id' => $project->id])
        ->test(CreateTask::class)
        ->assertFormSet([
            'project_id' => $project->id,
            'partner_id' => $partner->id,
            'stage_id'   => $stage->id,
        ]);
});

it('prefills project_id when creating a task stage inside ManageTaskStages', function () {
    $project = Project::factory()->create();

    Livewire::test(ManageTaskStages::class, ['record' => $project->getRouteKey()])
        ->mountTableAction('create')
        ->assertTableActionDataSet([
            'project_id' => $project->id,
        ])
        ->setTableActionData([
            'name' => 'In Review',
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect(TaskStage::where('project_id', $project->id)->where('name', 'In Review')->exists())->toBeTrue();
});
