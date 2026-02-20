<?php

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Schema;
use Webkul\Project\Models\Task;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('projects');

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsTaskApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function taskRoute(string $action, mixed $task = null): string
{
    $name = "admin.api.v1.projects.tasks.{$action}";

    return $task ? route($name, $task) : route($name);
}

function taskPayload(array $overrides = []): array
{
    $payload = Task::factory()->make($overrides)->toArray();

    unset($payload['visibility']);

    return $payload;
}

function createTaskRecord(array $overrides = [], int $count = 1): Task|EloquentCollection
{
    $tasks = Task::factory()
        ->count($count)
        ->afterMaking(function (Task $task): void {
            unset($task['visibility']);
        })
        ->create($overrides);

    return $count === 1 ? $tasks->first() : $tasks;
}

it('requires authentication to list tasks', function () {
    $this->getJson(taskRoute('index'))
        ->assertUnauthorized();
});

it('forbids listing tasks without permission', function () {
    actingAsTaskApiUser();

    $this->getJson(taskRoute('index'))
        ->assertForbidden();
});

it('lists tasks for authorized users', function () {
    actingAsTaskApiUser(['view_any_project_task']);

    createTaskRecord(count: 2);

    $this->getJson(taskRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a task with valid payload', function () {
    actingAsTaskApiUser(['create_project_task']);

    $payload = taskPayload();

    $response = $this->postJson(taskRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Task created successfully.')
        ->assertJsonPath('data.title', $payload['title']);

    $this->assertDatabaseHas('projects_tasks', [
        'title' => $payload['title'],
    ]);
});

it('validates required fields when creating a task', function (string $field) {
    actingAsTaskApiUser(['create_project_task']);

    $payload = taskPayload();
    unset($payload[$field]);

    $this->postJson(taskRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(['title', 'state', 'stage_id']);

it('shows a task for authorized users', function () {
    actingAsTaskApiUser(['view_project_task']);

    $task = createTaskRecord();

    $this->getJson(taskRoute('show', $task))
        ->assertOk()
        ->assertJsonPath('data.id', $task->id);
});

it('updates a task for authorized users', function () {
    $user = actingAsTaskApiUser(['update_project_task']);

    $task = createTaskRecord([
        'creator_id' => $user->id,
    ]);

    $task->users()->sync([$user->id]);

    $updatedTitle = Task::factory()->make()->title;

    $this->patchJson(taskRoute('update', $task), ['title' => $updatedTitle])
        ->assertOk()
        ->assertJsonPath('message', 'Task updated successfully.')
        ->assertJsonPath('data.title', $updatedTitle);

    $this->assertDatabaseHas('projects_tasks', [
        'id'    => $task->id,
        'title' => $updatedTitle,
    ]);
});

it('deletes a task for authorized users', function () {
    $user = actingAsTaskApiUser(['delete_project_task']);

    $task = createTaskRecord([
        'creator_id' => $user->id,
    ]);

    $task->users()->sync([$user->id]);

    $this->deleteJson(taskRoute('destroy', $task))
        ->assertOk()
        ->assertJsonPath('message', 'Task deleted successfully.');

    $this->assertSoftDeleted('projects_tasks', [
        'id' => $task->id,
    ]);
});

it('restores a task for authorized users', function () {
    $user = actingAsTaskApiUser(['restore_project_task']);

    $task = createTaskRecord([
        'creator_id' => $user->id,
    ]);

    $task->users()->sync([$user->id]);
    $task->delete();

    $this->postJson(taskRoute('restore', $task->id))
        ->assertOk()
        ->assertJsonPath('message', 'Task restored successfully.');

    $this->assertDatabaseHas('projects_tasks', [
        'id'         => $task->id,
        'deleted_at' => null,
    ]);
});

it('force deletes a task for authorized users', function () {
    $user = actingAsTaskApiUser(['force_delete_project_task']);

    $task = createTaskRecord([
        'creator_id' => $user->id,
    ]);

    $task->users()->sync([$user->id]);
    $task->delete();

    $this->deleteJson(taskRoute('force-destroy', $task->id))
        ->assertOk()
        ->assertJsonPath('message', 'Task permanently deleted.');

    $this->assertDatabaseMissing('projects_tasks', [
        'id' => $task->id,
    ]);
});
