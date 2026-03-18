<?php

use Webkul\Inventory\Models\Tag;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const INVENTORY_TAG_JSON_STRUCTURE = [
    'id',
    'name',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryTagApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function inventoryTagRoute(string $action, mixed $tag = null): string
{
    $name = "admin.api.v1.inventories.tags.{$action}";

    return $tag ? route($name, $tag) : route($name);
}

function inventoryTagPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name' => 'Fragile-'.uniqid(),
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list tags', function () {
    $this->getJson(inventoryTagRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a tag', function () {
    $this->postJson(inventoryTagRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a tag', function () {
    $tag = Tag::factory()->create();

    $this->getJson(inventoryTagRoute('show', $tag))
        ->assertUnauthorized();
});

it('requires authentication to update a tag', function () {
    $tag = Tag::factory()->create();

    $this->patchJson(inventoryTagRoute('update', $tag), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a tag', function () {
    $tag = Tag::factory()->create();

    $this->deleteJson(inventoryTagRoute('destroy', $tag))
        ->assertUnauthorized();
});

it('requires authentication to restore a tag', function () {
    $tag = Tag::factory()->create();
    $tag->delete();

    $this->postJson(inventoryTagRoute('restore', $tag->id))
        ->assertUnauthorized();
});

it('requires authentication to force-delete a tag', function () {
    $tag = Tag::factory()->create();
    $tag->delete();

    $this->deleteJson(inventoryTagRoute('force-destroy', $tag->id))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing tags without permission', function () {
    actingAsInventoryTagApiUser();

    $this->getJson(inventoryTagRoute('index'))
        ->assertForbidden();
});

it('forbids creating a tag without permission', function () {
    actingAsInventoryTagApiUser();

    $this->postJson(inventoryTagRoute('store'), inventoryTagPayload())
        ->assertForbidden();
});

it('forbids showing a tag without permission', function () {
    actingAsInventoryTagApiUser();

    $tag = Tag::factory()->create();

    $this->getJson(inventoryTagRoute('show', $tag))
        ->assertForbidden();
});

it('forbids updating a tag without permission', function () {
    actingAsInventoryTagApiUser();

    $tag = Tag::factory()->create();

    $this->patchJson(inventoryTagRoute('update', $tag), [])
        ->assertForbidden();
});

it('forbids deleting a tag without permission', function () {
    actingAsInventoryTagApiUser();

    $tag = Tag::factory()->create();

    $this->deleteJson(inventoryTagRoute('destroy', $tag))
        ->assertForbidden();
});

it('forbids restoring a tag without permission', function () {
    actingAsInventoryTagApiUser();

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->postJson(inventoryTagRoute('restore', $tag->id))
        ->assertForbidden();
});

it('forbids force-deleting a tag without permission', function () {
    actingAsInventoryTagApiUser();

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->deleteJson(inventoryTagRoute('force-destroy', $tag->id))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists tags for authorized users', function () {
    actingAsInventoryTagApiUser(['view_any_inventory_tag']);

    Tag::factory()->count(3)->create();

    $this->getJson(inventoryTagRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters tags by name', function () {
    actingAsInventoryTagApiUser(['view_any_inventory_tag']);

    $tag = Tag::factory()->create(['name' => 'UniqueTagXYZ']);
    Tag::factory()->count(2)->create();

    $response = $this->getJson(inventoryTagRoute('index').'?filter[name]=UniqueTagXYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($tag->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates a tag', function () {
    actingAsInventoryTagApiUser(['create_inventory_tag']);

    $payload = inventoryTagPayload();

    $this->postJson(inventoryTagRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Tag created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => INVENTORY_TAG_JSON_STRUCTURE]);

    $this->assertDatabaseHas('inventories_tags', ['name' => $payload['name']]);
});

it('validates required name when creating a tag', function () {
    actingAsInventoryTagApiUser(['create_inventory_tag']);

    $this->postJson(inventoryTagRoute('store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('rejects duplicate tag name on creation', function () {
    actingAsInventoryTagApiUser(['create_inventory_tag']);

    $tag = Tag::factory()->create();

    $this->postJson(inventoryTagRoute('store'), ['name' => $tag->name])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a tag for authorized users', function () {
    actingAsInventoryTagApiUser(['view_inventory_tag']);

    $tag = Tag::factory()->create();

    $this->getJson(inventoryTagRoute('show', $tag))
        ->assertOk()
        ->assertJsonPath('data.id', $tag->id)
        ->assertJsonPath('data.name', $tag->name)
        ->assertJsonStructure(['data' => INVENTORY_TAG_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent tag', function () {
    actingAsInventoryTagApiUser(['view_inventory_tag']);

    $this->getJson(inventoryTagRoute('show', 999999))
        ->assertNotFound();
});

it('returns 404 for a soft-deleted tag', function () {
    actingAsInventoryTagApiUser(['view_inventory_tag']);

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->getJson(inventoryTagRoute('show', $tag))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a tag', function () {
    actingAsInventoryTagApiUser(['update_inventory_tag']);

    $tag = Tag::factory()->create();
    $newName = 'Updated-Tag-'.uniqid();

    $this->patchJson(inventoryTagRoute('update', $tag), ['name' => $newName])
        ->assertOk()
        ->assertJsonPath('message', 'Tag updated successfully.')
        ->assertJsonPath('data.name', $newName);

    $this->assertDatabaseHas('inventories_tags', [
        'id'   => $tag->id,
        'name' => $newName,
    ]);
});

it('returns 404 when updating a non-existent tag', function () {
    actingAsInventoryTagApiUser(['update_inventory_tag']);

    $this->patchJson(inventoryTagRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

it('rejects duplicate tag name on update', function () {
    actingAsInventoryTagApiUser(['update_inventory_tag']);

    $existing = Tag::factory()->create();
    $tag = Tag::factory()->create();

    $this->patchJson(inventoryTagRoute('update', $tag), ['name' => $existing->name])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('soft-deletes a tag', function () {
    actingAsInventoryTagApiUser(['delete_inventory_tag']);

    $tag = Tag::factory()->create();

    $this->deleteJson(inventoryTagRoute('destroy', $tag))
        ->assertOk()
        ->assertJsonPath('message', 'Tag deleted successfully.');

    $this->assertSoftDeleted('inventories_tags', ['id' => $tag->id]);
});

it('returns 404 when deleting a non-existent tag', function () {
    actingAsInventoryTagApiUser(['delete_inventory_tag']);

    $this->deleteJson(inventoryTagRoute('destroy', 999999))
        ->assertNotFound();
});

// ── Restore ───────────────────────────────────────────────────────────────────

it('restores a soft-deleted tag', function () {
    actingAsInventoryTagApiUser(['restore_inventory_tag']);

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->postJson(inventoryTagRoute('restore', $tag->id))
        ->assertOk()
        ->assertJsonPath('message', 'Tag restored successfully.');

    $this->assertDatabaseHas('inventories_tags', [
        'id'         => $tag->id,
        'deleted_at' => null,
    ]);
});

it('returns 404 when restoring a non-existent tag', function () {
    actingAsInventoryTagApiUser(['restore_inventory_tag']);

    $this->postJson(inventoryTagRoute('restore', 999999))
        ->assertNotFound();
});

// ── Force Destroy ─────────────────────────────────────────────────────────────

it('permanently deletes a soft-deleted tag', function () {
    actingAsInventoryTagApiUser(['force_delete_inventory_tag']);

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->deleteJson(inventoryTagRoute('force-destroy', $tag->id))
        ->assertOk()
        ->assertJsonPath('message', 'Tag permanently deleted successfully.');

    $this->assertDatabaseMissing('inventories_tags', ['id' => $tag->id]);
});

it('returns 404 when force-deleting a non-existent tag', function () {
    actingAsInventoryTagApiUser(['force_delete_inventory_tag']);

    $this->deleteJson(inventoryTagRoute('force-destroy', 999999))
        ->assertNotFound();
});
