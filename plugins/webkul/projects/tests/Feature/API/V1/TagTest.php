<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Project\Models\Tag;
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

function actingAsTagApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function tagRoute(string $action, mixed $tag = null): string
{
    $name = "admin.api.v1.projects.tags.{$action}";

    return $tag ? route($name, $tag) : route($name);
}

it('requires authentication to list tags', function () {
    $this->getJson(tagRoute('index'))
        ->assertUnauthorized();
});

it('forbids listing tags without permission', function () {
    actingAsTagApiUser();

    $this->getJson(tagRoute('index'))
        ->assertForbidden();
});

it('lists tags for authorized users', function () {
    actingAsTagApiUser(['view_any_project_tag']);

    Tag::factory()->count(2)->create();

    $this->getJson(tagRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a tag with valid payload', function () {
    actingAsTagApiUser(['create_project_tag']);

    $payload = Tag::factory()->make()->toArray();

    $response = $this->postJson(tagRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Tag created successfully.')
        ->assertJsonPath('data.name', $payload['name']);

    $this->assertDatabaseHas('projects_tags', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a tag', function () {
    actingAsTagApiUser(['create_project_tag']);

    $this->postJson(tagRoute('store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('shows a tag for authorized users', function () {
    actingAsTagApiUser(['view_project_tag']);

    $tag = Tag::factory()->create();

    $this->getJson(tagRoute('show', $tag))
        ->assertOk()
        ->assertJsonPath('data.id', $tag->id);
});

it('updates a tag for authorized users', function () {
    actingAsTagApiUser(['update_project_tag']);

    $tag = Tag::factory()->create();
    $updatedName = Tag::factory()->make()->name;

    $this->patchJson(tagRoute('update', $tag), ['name' => $updatedName])
        ->assertOk()
        ->assertJsonPath('message', 'Tag updated successfully.')
        ->assertJsonPath('data.name', $updatedName);

    $this->assertDatabaseHas('projects_tags', [
        'id'   => $tag->id,
        'name' => $updatedName,
    ]);
});

it('deletes a tag for authorized users', function () {
    actingAsTagApiUser(['delete_project_tag']);

    $tag = Tag::factory()->create();

    $this->deleteJson(tagRoute('destroy', $tag))
        ->assertOk()
        ->assertJsonPath('message', 'Tag deleted successfully.');

    $this->assertSoftDeleted('projects_tags', [
        'id' => $tag->id,
    ]);
});

it('restores a tag for authorized users', function () {
    actingAsTagApiUser(['restore_project_tag']);

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->postJson(tagRoute('restore', $tag->id))
        ->assertOk()
        ->assertJsonPath('message', 'Tag restored successfully.');

    $this->assertDatabaseHas('projects_tags', [
        'id'         => $tag->id,
        'deleted_at' => null,
    ]);
});

it('force deletes a tag for authorized users', function () {
    actingAsTagApiUser(['force_delete_project_tag']);

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->deleteJson(tagRoute('force-destroy', $tag->id))
        ->assertOk()
        ->assertJsonPath('message', 'Tag permanently deleted.');

    $this->assertDatabaseMissing('projects_tags', [
        'id' => $tag->id,
    ]);
});
