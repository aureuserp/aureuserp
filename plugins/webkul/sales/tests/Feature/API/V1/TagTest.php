<?php

use Webkul\Sale\Models\Tag;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

const SALES_TAG_JSON_STRUCTURE = [
    'id',
    'name',
    'color',
    'creator_id',
];

const SALES_TAG_REQUIRED_FIELDS = [
    'name',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('sales');

    SecurityHelper::disableUserEvents();
});
afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsSalesTagApiUser(array $permissions = []): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function salesTagRoute(string $action, mixed $tag = null): string
{
    $name = "admin.api.v1.sales.tags.{$action}";

    return $tag ? route($name, $tag) : route($name);
}

it('requires authentication to list sales tags', function () {
    $this->getJson(salesTagRoute('index'))
        ->assertUnauthorized();
});

it('forbids listing sales tags without permission', function () {
    actingAsSalesTagApiUser();

    $this->getJson(salesTagRoute('index'))
        ->assertForbidden();
});

it('lists sales tags for authorized users', function () {
    actingAsSalesTagApiUser(['view_any_sale_tag']);

    $firstTag = Tag::factory()->create();
    $secondTag = Tag::factory()->create();

    $response = $this->getJson(salesTagRoute('index'));

    $response
        ->assertOk()
        ->assertJsonStructure(['data' => ['*' => SALES_TAG_JSON_STRUCTURE]]);

    $returnedIds = collect($response->json('data'))->pluck('id');

    expect($returnedIds)
        ->toContain($firstTag->id)
        ->toContain($secondTag->id);
});

it('creates a sales tag with valid payload', function () {
    actingAsSalesTagApiUser(['create_sale_tag']);

    $payload = Tag::factory()->make()->toArray();

    $this->postJson(salesTagRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Tag created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => SALES_TAG_JSON_STRUCTURE]);
});

it('validates required fields when creating a sales tag', function (string $field) {
    actingAsSalesTagApiUser(['create_sale_tag']);

    $payload = Tag::factory()->make()->toArray();
    unset($payload[$field]);

    $this->postJson(salesTagRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(SALES_TAG_REQUIRED_FIELDS);

it('shows a sales tag for authorized users', function () {
    actingAsSalesTagApiUser(['view_sale_tag']);

    $tag = Tag::factory()->create();

    $this->getJson(salesTagRoute('show', $tag))
        ->assertOk()
        ->assertJsonPath('data.id', $tag->id)
        ->assertJsonStructure(['data' => SALES_TAG_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent sales tag', function () {
    actingAsSalesTagApiUser(['view_sale_tag']);

    $this->getJson(salesTagRoute('show', 999999))
        ->assertNotFound();
});

it('updates a sales tag for authorized users', function () {
    actingAsSalesTagApiUser(['update_sale_tag']);

    $tag = Tag::factory()->create();
    $updatedName = 'Updated Sales Tag '.fake()->unique()->numerify('####');

    $this->patchJson(salesTagRoute('update', $tag), ['name' => $updatedName])
        ->assertOk()
        ->assertJsonPath('message', 'Tag updated successfully.')
        ->assertJsonPath('data.name', $updatedName);
});

it('forbids updating a sales tag without permission', function () {
    actingAsSalesTagApiUser();

    $tag = Tag::factory()->create();

    $this->patchJson(salesTagRoute('update', $tag), ['name' => 'Nope'])
        ->assertForbidden();
});

it('deletes a sales tag for authorized users', function () {
    actingAsSalesTagApiUser(['delete_sale_tag']);

    $tag = Tag::factory()->create();

    $this->deleteJson(salesTagRoute('destroy', $tag))
        ->assertOk()
        ->assertJsonPath('message', 'Tag deleted successfully.');

    $this->assertDatabaseMissing('sales_tags', ['id' => $tag->id]);
});

it('forbids deleting a sales tag without permission', function () {
    actingAsSalesTagApiUser();

    $tag = Tag::factory()->create();

    $this->deleteJson(salesTagRoute('destroy', $tag))
        ->assertForbidden();
});
