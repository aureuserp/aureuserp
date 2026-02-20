<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Partner\Models\Tag;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

beforeEach(function () {
    if (! Schema::hasTable('partners_tags')) {
        $this->artisan('migrate')->assertSuccessful();
    }

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsTagApiUser(array $permissions = []): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function tagRoute(string $action, mixed $tag = null): string
{
    $name = "admin.api.v1.partners.tags.{$action}";

    return $tag ? route($name, $tag) : route($name);
}

it('requires authentication to list tags', function () {
    $this->getJson(tagRoute('index'))->assertUnauthorized();
});

it('forbids listing tags without permission', function () {
    actingAsTagApiUser();

    $this->getJson(tagRoute('index'))->assertForbidden();
});

it('lists tags for authorized users', function () {
    actingAsTagApiUser(['view_any_partner_tag']);

    Tag::factory()->count(2)->create();

    $this->getJson(tagRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a tag with valid payload', function () {
    actingAsTagApiUser(['create_partner_tag']);

    $payload = Tag::factory()->make()->toArray();

    $this->postJson(tagRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Tag created successfully.')
        ->assertJsonPath('data.name', $payload['name']);
});

it('validates required fields when creating a tag', function () {
    actingAsTagApiUser(['create_partner_tag']);

    $payload = Tag::factory()->make()->toArray();
    unset($payload['name']);

    $this->postJson(tagRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('shows a tag for authorized users', function () {
    actingAsTagApiUser(['view_partner_tag']);

    $tag = Tag::factory()->create();

    $this->getJson(tagRoute('show', $tag))
        ->assertOk()
        ->assertJsonPath('data.id', $tag->id);
});

it('updates a tag for authorized users', function () {
    actingAsTagApiUser(['update_partner_tag']);

    $tag = Tag::factory()->create();

    $this->patchJson(tagRoute('update', $tag), ['name' => 'Updated Tag'])
        ->assertOk()
        ->assertJsonPath('message', 'Tag updated successfully.')
        ->assertJsonPath('data.name', 'Updated Tag');
});

it('deletes a tag for authorized users', function () {
    actingAsTagApiUser(['delete_partner_tag']);

    $tag = Tag::factory()->create();

    $this->deleteJson(tagRoute('destroy', $tag))
        ->assertOk()
        ->assertJsonPath('message', 'Tag deleted successfully.');

    $this->assertSoftDeleted('partners_tags', ['id' => $tag->id]);
});

it('restores a tag for authorized users', function () {
    actingAsTagApiUser(['restore_partner_tag']);

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->postJson(tagRoute('restore', $tag->id))
        ->assertOk()
        ->assertJsonPath('message', 'Tag restored successfully.');
});

it('force deletes a tag for authorized users', function () {
    actingAsTagApiUser(['force_delete_partner_tag']);

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->deleteJson(tagRoute('force-destroy', $tag->id))
        ->assertOk()
        ->assertJsonPath('message', 'Tag permanently deleted.');

    $this->assertDatabaseMissing('partners_tags', ['id' => $tag->id]);
});
