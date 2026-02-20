<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Project\Models\Tag;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('projects');
    TestBootstrapHelper::ensureBaseCurrencies();

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function projectsTagRoute(string $action, mixed $tag = null): string
{
    $name = "admin.api.v1.projects.tags.{$action}";

    return $tag ? route($name, $tag) : route($name);
}

it('requires authentication to list tags', function () {
    $this->getJson(projectsTagRoute('index'))
        ->assertUnauthorized();
});

it('forbids listing tags without permission', function () {
    SecurityHelper::actingAsTagApiUser([], true);

    $this->getJson(projectsTagRoute('index'))
        ->assertForbidden();
});

it('lists tags for authorized users', function () {
    SecurityHelper::actingAsTagApiUser(['view_any_project_tag'], true);

    Tag::factory()->count(2)->create();

    $this->getJson(projectsTagRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a tag with valid payload', function () {
    SecurityHelper::actingAsTagApiUser(['create_project_tag'], true);

    $payload = Tag::factory()->make()->toArray();

    $response = $this->postJson(projectsTagRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Tag created successfully.')
        ->assertJsonPath('data.name', $payload['name']);

    $this->assertDatabaseHas('projects_tags', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a tag', function () {
    SecurityHelper::actingAsTagApiUser(['create_project_tag'], true);

    $this->postJson(projectsTagRoute('store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('shows a tag for authorized users', function () {
    SecurityHelper::actingAsTagApiUser(['view_project_tag'], true);

    $tag = Tag::factory()->create();

    $this->getJson(projectsTagRoute('show', $tag))
        ->assertOk()
        ->assertJsonPath('data.id', $tag->id);
});

it('updates a tag for authorized users', function () {
    SecurityHelper::actingAsTagApiUser(['update_project_tag'], true);

    $tag = Tag::factory()->create();
    $updatedName = Tag::factory()->make()->name;

    $this->patchJson(projectsTagRoute('update', $tag), ['name' => $updatedName])
        ->assertOk()
        ->assertJsonPath('message', 'Tag updated successfully.')
        ->assertJsonPath('data.name', $updatedName);

    $this->assertDatabaseHas('projects_tags', [
        'id'   => $tag->id,
        'name' => $updatedName,
    ]);
});

it('deletes a tag for authorized users', function () {
    SecurityHelper::actingAsTagApiUser(['delete_project_tag'], true);

    $tag = Tag::factory()->create();

    $this->deleteJson(projectsTagRoute('destroy', $tag))
        ->assertOk()
        ->assertJsonPath('message', 'Tag deleted successfully.');

    $this->assertSoftDeleted('projects_tags', [
        'id' => $tag->id,
    ]);
});

it('restores a tag for authorized users', function () {
    SecurityHelper::actingAsTagApiUser(['restore_project_tag'], true);

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->postJson(projectsTagRoute('restore', $tag->id))
        ->assertOk()
        ->assertJsonPath('message', 'Tag restored successfully.');

    $this->assertDatabaseHas('projects_tags', [
        'id'         => $tag->id,
        'deleted_at' => null,
    ]);
});

it('force deletes a tag for authorized users', function () {
    SecurityHelper::actingAsTagApiUser(['force_delete_project_tag'], true);

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->deleteJson(projectsTagRoute('force-destroy', $tag->id))
        ->assertOk()
        ->assertJsonPath('message', 'Tag permanently deleted.');

    $this->assertDatabaseMissing('projects_tags', [
        'id' => $tag->id,
    ]);
});
