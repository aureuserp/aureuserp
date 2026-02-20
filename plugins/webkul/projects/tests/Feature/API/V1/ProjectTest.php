<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Project\Models\Project;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

beforeEach(function () {
    if (! Schema::hasTable('projects_projects')) {
        $this->artisan('projects:install', ['--no-interaction' => true])->assertSuccessful();
    }

    TestBootstrapHelper::ensureBaseCurrencies();

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsProjectApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function projectRoute(string $action, mixed $project = null): string
{
    $name = "admin.api.v1.projects.projects.{$action}";

    return $project ? route($name, $project) : route($name);
}

it('requires authentication to list projects', function () {
    $this->getJson(projectRoute('index'))
        ->assertUnauthorized();
});

it('forbids listing projects without permission', function () {
    actingAsProjectApiUser();

    $this->getJson(projectRoute('index'))
        ->assertForbidden();
});

it('lists projects for authorized users', function () {
    actingAsProjectApiUser(['view_any_project_project']);

    Project::factory()->count(2)->create();

    $this->getJson(projectRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a project with valid payload', function () {
    actingAsProjectApiUser(['create_project_project']);

    $payload = Project::factory()->make()->toArray();
    $payload['start_date'] = '2026-01-01';
    $payload['end_date'] = '2026-01-31';

    $response = $this->postJson(projectRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Project created successfully.')
        ->assertJsonPath('data.name', $payload['name']);

    $this->assertDatabaseHas('projects_projects', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a project', function (string $field) {
    actingAsProjectApiUser(['create_project_project']);

    $payload = Project::factory()->make()->toArray();
    $payload['start_date'] = '2026-01-01';
    $payload['end_date'] = '2026-01-31';
    unset($payload[$field]);

    $this->postJson(projectRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(['name', 'visibility', 'stage_id']);

it('shows a project for authorized users', function () {
    actingAsProjectApiUser(['view_project_project']);

    $project = Project::factory()->create();

    $this->getJson(projectRoute('show', $project))
        ->assertOk()
        ->assertJsonPath('data.id', $project->id);
});

it('updates a project for authorized users', function () {
    $user = actingAsProjectApiUser(['update_project_project']);

    $project = Project::factory()->create([
        'user_id'    => $user->id,
        'creator_id' => $user->id,
    ]);

    $updatedName = Project::factory()->make()->name;

    $this->patchJson(projectRoute('update', $project), ['name' => $updatedName])
        ->assertOk()
        ->assertJsonPath('message', 'Project updated successfully.')
        ->assertJsonPath('data.name', $updatedName);

    $this->assertDatabaseHas('projects_projects', [
        'id'   => $project->id,
        'name' => $updatedName,
    ]);
});

it('deletes a project for authorized users', function () {
    $user = actingAsProjectApiUser(['delete_project_project']);

    $project = Project::factory()->create([
        'user_id'    => $user->id,
        'creator_id' => $user->id,
    ]);

    $this->deleteJson(projectRoute('destroy', $project))
        ->assertOk()
        ->assertJsonPath('message', 'Project deleted successfully.');

    $this->assertSoftDeleted('projects_projects', [
        'id' => $project->id,
    ]);
});

it('restores a project for authorized users', function () {
    $user = actingAsProjectApiUser(['restore_project_project']);

    $project = Project::factory()->create([
        'user_id'    => $user->id,
        'creator_id' => $user->id,
    ]);

    $project->delete();

    $this->postJson(projectRoute('restore', $project->id))
        ->assertOk()
        ->assertJsonPath('message', 'Project restored successfully.');

    $this->assertDatabaseHas('projects_projects', [
        'id'         => $project->id,
        'deleted_at' => null,
    ]);
});

it('force deletes a project for authorized users', function () {
    $user = actingAsProjectApiUser(['force_delete_project_project']);

    $project = Project::factory()->create([
        'user_id'    => $user->id,
        'creator_id' => $user->id,
    ]);

    $project->delete();

    $this->deleteJson(projectRoute('force-destroy', $project->id))
        ->assertOk()
        ->assertJsonPath('message', 'Project permanently deleted.');

    $this->assertDatabaseMissing('projects_projects', [
        'id' => $project->id,
    ]);
});
