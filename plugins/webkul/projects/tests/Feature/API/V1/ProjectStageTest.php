<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Project\Models\ProjectStage;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

beforeEach(function () {
    if (! Schema::hasTable('projects_projects')) {
        $this->artisan('projects:install')->assertSuccessful();
    }

    TestBootstrapHelper::ensureBaseCurrencies();

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsProjectStageApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function projectStageRoute(string $action, mixed $stage = null): string
{
    $name = "admin.api.v1.projects.project-stages.{$action}";

    return $stage ? route($name, $stage) : route($name);
}

it('requires authentication to list project stages', function () {
    $this->getJson(projectStageRoute('index'))
        ->assertUnauthorized();
});

it('forbids listing project stages without permission', function () {
    actingAsProjectStageApiUser();

    $this->getJson(projectStageRoute('index'))
        ->assertForbidden();
});

it('lists project stages for authorized users', function () {
    actingAsProjectStageApiUser(['view_any_project_project::stage']);

    ProjectStage::factory()->count(2)->create();

    $this->getJson(projectStageRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a project stage with valid payload', function () {
    actingAsProjectStageApiUser(['create_project_project::stage']);

    $payload = ProjectStage::factory()->make()->toArray();

    $response = $this->postJson(projectStageRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Project stage created successfully.')
        ->assertJsonPath('data.name', $payload['name']);

    $this->assertDatabaseHas('projects_project_stages', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a project stage', function () {
    actingAsProjectStageApiUser(['create_project_project::stage']);

    $this->postJson(projectStageRoute('store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('shows a project stage for authorized users', function () {
    actingAsProjectStageApiUser(['view_project_project::stage']);

    $stage = ProjectStage::factory()->create();

    $this->getJson(projectStageRoute('show', $stage))
        ->assertOk()
        ->assertJsonPath('data.id', $stage->id);
});

it('updates a project stage for authorized users', function () {
    actingAsProjectStageApiUser(['update_project_project::stage']);

    $stage = ProjectStage::factory()->create();
    $updatedName = ProjectStage::factory()->make()->name;

    $this->patchJson(projectStageRoute('update', $stage), ['name' => $updatedName])
        ->assertOk()
        ->assertJsonPath('message', 'Project stage updated successfully.')
        ->assertJsonPath('data.name', $updatedName);

    $this->assertDatabaseHas('projects_project_stages', [
        'id'   => $stage->id,
        'name' => $updatedName,
    ]);
});

it('deletes a project stage for authorized users', function () {
    actingAsProjectStageApiUser(['delete_project_project::stage']);

    $stage = ProjectStage::factory()->create();

    $this->deleteJson(projectStageRoute('destroy', $stage))
        ->assertOk()
        ->assertJsonPath('message', 'Project stage deleted successfully.');

    $this->assertSoftDeleted('projects_project_stages', [
        'id' => $stage->id,
    ]);
});

it('restores a project stage for authorized users', function () {
    actingAsProjectStageApiUser(['restore_project_project::stage']);

    $stage = ProjectStage::factory()->create();
    $stage->delete();

    $this->postJson(projectStageRoute('restore', $stage->id))
        ->assertOk()
        ->assertJsonPath('message', 'Project stage restored successfully.');

    $this->assertDatabaseHas('projects_project_stages', [
        'id'         => $stage->id,
        'deleted_at' => null,
    ]);
});

it('force deletes a project stage for authorized users', function () {
    actingAsProjectStageApiUser(['force_delete_project_project::stage']);

    $stage = ProjectStage::factory()->create();
    $stage->delete();

    $this->deleteJson(projectStageRoute('force-destroy', $stage->id))
        ->assertOk()
        ->assertJsonPath('message', 'Project stage permanently deleted.');

    $this->assertDatabaseMissing('projects_project_stages', [
        'id' => $stage->id,
    ]);
});
