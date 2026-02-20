<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Project\Models\TaskStage;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

function ensureBaseCurrencies(): void
{
    $requiredIds = [1, 2, 3];
    $existingIds = Currency::query()->whereIn('id', $requiredIds)->pluck('id')->all();
    $missingIds = array_values(array_diff($requiredIds, $existingIds));

    if ($missingIds === []) {
        return;
    }

    $sequenceStates = array_map(
        fn (int $id): array => ['id' => $id],
        $missingIds
    );

    Currency::factory()
        ->count(count($missingIds))
        ->sequence(...$sequenceStates)
        ->create();
}

beforeEach(function () {
    if (! Schema::hasTable('projects_projects')) {
        $this->artisan('projects:install')->assertSuccessful();
    }

    ensureBaseCurrencies();

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsTaskStageApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function taskStageRoute(string $action, mixed $stage = null): string
{
    $name = "admin.api.v1.projects.task-stages.{$action}";

    return $stage ? route($name, $stage) : route($name);
}

it('requires authentication to list task stages', function () {
    $this->getJson(taskStageRoute('index'))
        ->assertUnauthorized();
});

it('forbids listing task stages without permission', function () {
    actingAsTaskStageApiUser();

    $this->getJson(taskStageRoute('index'))
        ->assertForbidden();
});

it('lists task stages for authorized users', function () {
    actingAsTaskStageApiUser(['view_any_project_task::stage']);

    TaskStage::factory()->count(2)->create();

    $this->getJson(taskStageRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a task stage with valid payload', function () {
    actingAsTaskStageApiUser(['create_project_task::stage']);

    $payload = TaskStage::factory()->make()->toArray();

    $response = $this->postJson(taskStageRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Task stage created successfully.')
        ->assertJsonPath('data.name', $payload['name']);

    $this->assertDatabaseHas('projects_task_stages', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a task stage', function (string $field) {
    actingAsTaskStageApiUser(['create_project_task::stage']);

    $payload = TaskStage::factory()->make()->toArray();
    unset($payload[$field]);

    $this->postJson(taskStageRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(['name', 'project_id']);

it('shows a task stage for authorized users', function () {
    actingAsTaskStageApiUser(['view_project_task::stage']);

    $stage = TaskStage::factory()->create();

    $this->getJson(taskStageRoute('show', $stage))
        ->assertOk()
        ->assertJsonPath('data.id', $stage->id);
});

it('updates a task stage for authorized users', function () {
    actingAsTaskStageApiUser(['update_project_task::stage']);

    $stage = TaskStage::factory()->create();
    $updatedName = TaskStage::factory()->make()->name;

    $this->patchJson(taskStageRoute('update', $stage), ['name' => $updatedName])
        ->assertOk()
        ->assertJsonPath('message', 'Task stage updated successfully.')
        ->assertJsonPath('data.name', $updatedName);

    $this->assertDatabaseHas('projects_task_stages', [
        'id'   => $stage->id,
        'name' => $updatedName,
    ]);
});

it('deletes a task stage for authorized users', function () {
    actingAsTaskStageApiUser(['delete_project_task::stage']);

    $stage = TaskStage::factory()->create();

    $this->deleteJson(taskStageRoute('destroy', $stage))
        ->assertOk()
        ->assertJsonPath('message', 'Task stage deleted successfully.');

    $this->assertSoftDeleted('projects_task_stages', [
        'id' => $stage->id,
    ]);
});

it('restores a task stage for authorized users', function () {
    actingAsTaskStageApiUser(['restore_project_task::stage']);

    $stage = TaskStage::factory()->create();
    $stage->delete();

    $this->postJson(taskStageRoute('restore', $stage->id))
        ->assertOk()
        ->assertJsonPath('message', 'Task stage restored successfully.');

    $this->assertDatabaseHas('projects_task_stages', [
        'id'         => $stage->id,
        'deleted_at' => null,
    ]);
});

it('force deletes a task stage for authorized users', function () {
    actingAsTaskStageApiUser(['force_delete_project_task::stage']);

    $stage = TaskStage::factory()->create();
    $stage->delete();

    $this->deleteJson(taskStageRoute('force-destroy', $stage->id))
        ->assertOk()
        ->assertJsonPath('message', 'Task stage permanently deleted.');

    $this->assertDatabaseMissing('projects_task_stages', [
        'id' => $stage->id,
    ]);
});
