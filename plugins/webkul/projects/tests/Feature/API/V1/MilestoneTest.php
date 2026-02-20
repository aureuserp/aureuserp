<?php

use Webkul\Project\Models\Milestone;
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

function actingAsMilestoneApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function milestoneRoute(string $action, mixed $milestone = null): string
{
    $name = "admin.api.v1.projects.milestones.{$action}";

    return $milestone ? route($name, $milestone) : route($name);
}

it('requires authentication to list milestones', function () {
    $this->getJson(milestoneRoute('index'))
        ->assertUnauthorized();
});

it('forbids listing milestones without permission', function () {
    actingAsMilestoneApiUser();

    $this->getJson(milestoneRoute('index'))
        ->assertForbidden();
});

it('lists milestones for authorized users', function () {
    actingAsMilestoneApiUser(['view_any_project_milestone']);

    Milestone::factory()->count(2)->create();

    $this->getJson(milestoneRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a milestone with valid payload', function () {
    actingAsMilestoneApiUser(['create_project_milestone']);

    $payload = Milestone::factory()->make()->toArray();

    $response = $this->postJson(milestoneRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Milestone created successfully.')
        ->assertJsonPath('data.name', $payload['name']);

    $this->assertDatabaseHas('projects_milestones', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a milestone', function (string $field) {
    actingAsMilestoneApiUser(['create_project_milestone']);

    $payload = Milestone::factory()->make()->toArray();
    unset($payload[$field]);

    $this->postJson(milestoneRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(['name', 'project_id']);

it('shows a milestone for authorized users', function () {
    actingAsMilestoneApiUser(['view_project_milestone']);

    $milestone = Milestone::factory()->create();

    $this->getJson(milestoneRoute('show', $milestone))
        ->assertOk()
        ->assertJsonPath('data.id', $milestone->id);
});

it('updates a milestone for authorized users', function () {
    actingAsMilestoneApiUser(['update_project_milestone']);

    $milestone = Milestone::factory()->create();
    $updatedName = Milestone::factory()->make()->name;

    $this->patchJson(milestoneRoute('update', $milestone), ['name' => $updatedName])
        ->assertOk()
        ->assertJsonPath('message', 'Milestone updated successfully.')
        ->assertJsonPath('data.name', $updatedName);

    $this->assertDatabaseHas('projects_milestones', [
        'id'   => $milestone->id,
        'name' => $updatedName,
    ]);
});

it('deletes a milestone for authorized users', function () {
    actingAsMilestoneApiUser(['delete_project_milestone']);

    $milestone = Milestone::factory()->create();

    $this->deleteJson(milestoneRoute('destroy', $milestone))
        ->assertOk()
        ->assertJsonPath('message', 'Milestone deleted successfully.');

    $this->assertDatabaseMissing('projects_milestones', [
        'id' => $milestone->id,
    ]);
});
