<?php

use Webkul\Product\Models\Tag;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PRODUCTS_TAG_JSON_STRUCTURE = [
    'id',
    'name',
];

const PRODUCTS_TAG_REQUIRED_FIELDS = [
    'name',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsProductsTagApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function productsTagRoute(string $action, mixed $tag = null): string
{
    $name = "admin.api.v1.products.tags.{$action}";

    return $tag ? route($name, $tag) : route($name);
}

function productsTagPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name'  => 'Featured',
        'color' => '#FF5733',
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list product tags', function () {
    $this->getJson(productsTagRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a product tag', function () {
    $this->postJson(productsTagRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a product tag', function () {
    $tag = Tag::factory()->create();

    $this->getJson(productsTagRoute('show', $tag))
        ->assertUnauthorized();
});

it('requires authentication to update a product tag', function () {
    $tag = Tag::factory()->create();

    $this->patchJson(productsTagRoute('update', $tag), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a product tag', function () {
    $tag = Tag::factory()->create();

    $this->deleteJson(productsTagRoute('destroy', $tag))
        ->assertUnauthorized();
});

it('requires authentication to restore a product tag', function () {
    $tag = Tag::factory()->create();
    $tag->delete();

    $this->postJson(productsTagRoute('restore', $tag))
        ->assertUnauthorized();
});

it('requires authentication to force-delete a product tag', function () {
    $tag = Tag::factory()->create();
    $tag->delete();

    $this->deleteJson(productsTagRoute('force-destroy', $tag))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing tags without permission', function () {
    actingAsProductsTagApiUser();

    $this->getJson(productsTagRoute('index'))
        ->assertForbidden();
});

it('forbids creating a tag without permission', function () {
    actingAsProductsTagApiUser();

    $this->postJson(productsTagRoute('store'), productsTagPayload())
        ->assertForbidden();
});

it('forbids showing a tag without permission', function () {
    actingAsProductsTagApiUser();

    $tag = Tag::factory()->create();

    $this->getJson(productsTagRoute('show', $tag))
        ->assertForbidden();
});

it('forbids updating a tag without permission', function () {
    actingAsProductsTagApiUser();

    $tag = Tag::factory()->create();

    $this->patchJson(productsTagRoute('update', $tag), [])
        ->assertForbidden();
});

it('forbids deleting a tag without permission', function () {
    actingAsProductsTagApiUser();

    $tag = Tag::factory()->create();

    $this->deleteJson(productsTagRoute('destroy', $tag))
        ->assertForbidden();
});

it('forbids restoring a tag without permission', function () {
    actingAsProductsTagApiUser();

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->postJson(productsTagRoute('restore', $tag))
        ->assertForbidden();
});

it('forbids force-deleting a tag without permission', function () {
    actingAsProductsTagApiUser();

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->deleteJson(productsTagRoute('force-destroy', $tag))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists product tags for authorized users', function () {
    actingAsProductsTagApiUser(['view_any_product_tag']);

    Tag::factory()->count(3)->create();

    $this->getJson(productsTagRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters tags by name', function () {
    actingAsProductsTagApiUser(['view_any_product_tag']);

    $tag = Tag::factory()->create(['name' => 'UniqueTagXYZ']);
    Tag::factory()->count(2)->create();

    $response = $this->getJson(productsTagRoute('index').'?filter[name]=UniqueTagXYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($tag->id);
});

it('excludes soft-deleted tags from default listing', function () {
    actingAsProductsTagApiUser(['view_any_product_tag']);

    $active = Tag::factory()->create();
    $deleted = Tag::factory()->create();
    $deleted->delete();

    $response = $this->getJson(productsTagRoute('index'))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($active->id)
        ->and($ids)->not->toContain($deleted->id);
});

it('includes soft-deleted tags when filter[trashed]=with', function () {
    actingAsProductsTagApiUser(['view_any_product_tag']);

    $deleted = Tag::factory()->create();
    $deleted->delete();

    $response = $this->getJson(productsTagRoute('index').'?filter[trashed]=with')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($deleted->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates a product tag', function () {
    actingAsProductsTagApiUser(['create_product_tag']);

    $payload = productsTagPayload();

    $this->postJson(productsTagRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Tag created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => PRODUCTS_TAG_JSON_STRUCTURE]);

    $this->assertDatabaseHas('products_tags', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a tag', function (string $field) {
    actingAsProductsTagApiUser(['create_product_tag']);

    $payload = productsTagPayload();
    unset($payload[$field]);

    $this->postJson(productsTagRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(PRODUCTS_TAG_REQUIRED_FIELDS);

it('rejects duplicate tag names', function () {
    actingAsProductsTagApiUser(['create_product_tag']);

    $existing = Tag::factory()->create(['name' => 'DuplicateName']);

    $this->postJson(productsTagRoute('store'), productsTagPayload(['name' => $existing->name]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a product tag for authorized users', function () {
    actingAsProductsTagApiUser(['view_product_tag']);

    $tag = Tag::factory()->create();

    $this->getJson(productsTagRoute('show', $tag))
        ->assertOk()
        ->assertJsonPath('data.id', $tag->id)
        ->assertJsonPath('data.name', $tag->name)
        ->assertJsonStructure(['data' => PRODUCTS_TAG_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent product tag', function () {
    actingAsProductsTagApiUser(['view_product_tag']);

    $this->getJson(productsTagRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a product tag', function () {
    actingAsProductsTagApiUser(['update_product_tag']);

    $tag = Tag::factory()->create();

    $this->patchJson(productsTagRoute('update', $tag), ['name' => 'Updated Tag'])
        ->assertOk()
        ->assertJsonPath('message', 'Tag updated successfully.')
        ->assertJsonPath('data.name', 'Updated Tag');

    $this->assertDatabaseHas('products_tags', [
        'id'   => $tag->id,
        'name' => 'Updated Tag',
    ]);
});

it('returns 404 when updating a non-existent tag', function () {
    actingAsProductsTagApiUser(['update_product_tag']);

    $this->patchJson(productsTagRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy (Soft Delete) ─────────────────────────────────────────────────────

it('soft deletes a product tag', function () {
    actingAsProductsTagApiUser(['delete_product_tag']);

    $tag = Tag::factory()->create();

    $this->deleteJson(productsTagRoute('destroy', $tag))
        ->assertOk()
        ->assertJsonPath('message', 'Tag deleted successfully.');

    $this->assertSoftDeleted('products_tags', ['id' => $tag->id]);
});

it('returns 404 when deleting a non-existent tag', function () {
    actingAsProductsTagApiUser(['delete_product_tag']);

    $this->deleteJson(productsTagRoute('destroy', 999999))
        ->assertNotFound();
});

// ── Restore ───────────────────────────────────────────────────────────────────

it('restores a soft-deleted tag', function () {
    actingAsProductsTagApiUser(['restore_product_tag']);

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->postJson(productsTagRoute('restore', $tag))
        ->assertOk()
        ->assertJsonPath('message', 'Tag restored successfully.');

    $this->assertDatabaseHas('products_tags', [
        'id'         => $tag->id,
        'deleted_at' => null,
    ]);
});

it('returns 404 when restoring a non-existent tag', function () {
    actingAsProductsTagApiUser(['restore_product_tag']);

    $this->postJson(productsTagRoute('restore', 999999))
        ->assertNotFound();
});

// ── Force Delete ──────────────────────────────────────────────────────────────

it('permanently deletes a product tag', function () {
    actingAsProductsTagApiUser(['force_delete_product_tag']);

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->deleteJson(productsTagRoute('force-destroy', $tag))
        ->assertOk()
        ->assertJsonPath('message', 'Tag permanently deleted.');

    $this->assertDatabaseMissing('products_tags', ['id' => $tag->id]);
});

it('returns 404 when force-deleting a non-existent tag', function () {
    actingAsProductsTagApiUser(['force_delete_product_tag']);

    $this->deleteJson(productsTagRoute('force-destroy', 999999))
        ->assertNotFound();
});
