<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Partner\Models\Tag;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

beforeEach(function () {
    if (! Schema::hasTable('partners_tags')) {
        test()->markTestSkipped(
            'Partners plugin tables are missing. Run `php artisan partners:install` before this suite.'
        );
    }

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function partnersTagRoute(string $action, mixed $tag = null): string
{
    $name = "admin.api.v1.partners.tags.{$action}";

    return $tag ? route($name, $tag) : route($name);
}

it('requires authentication to list tags', function () {
    $this->getJson(partnersTagRoute('index'))->assertUnauthorized();
});

it('forbids listing tags without permission', function () {
    SecurityHelper::actingAsTagApiUser();

    $this->getJson(partnersTagRoute('index'))->assertForbidden();
});

it('lists tags for authorized users', function () {
    SecurityHelper::actingAsTagApiUser(['view_any_partner_tag']);

    Tag::factory()->count(2)->create();

    $this->getJson(partnersTagRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a tag with valid payload', function () {
    SecurityHelper::actingAsTagApiUser(['create_partner_tag']);

    $payload = Tag::factory()->make()->toArray();

    $this->postJson(partnersTagRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Tag created successfully.')
        ->assertJsonPath('data.name', $payload['name']);
});

it('validates required fields when creating a tag', function () {
    SecurityHelper::actingAsTagApiUser(['create_partner_tag']);

    $payload = Tag::factory()->make()->toArray();
    unset($payload['name']);

    $this->postJson(partnersTagRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('shows a tag for authorized users', function () {
    SecurityHelper::actingAsTagApiUser(['view_partner_tag']);

    $tag = Tag::factory()->create();

    $this->getJson(partnersTagRoute('show', $tag))
        ->assertOk()
        ->assertJsonPath('data.id', $tag->id);
});

it('updates a tag for authorized users', function () {
    SecurityHelper::actingAsTagApiUser(['update_partner_tag']);

    $tag = Tag::factory()->create();

    $this->patchJson(partnersTagRoute('update', $tag), ['name' => 'Updated Tag'])
        ->assertOk()
        ->assertJsonPath('message', 'Tag updated successfully.')
        ->assertJsonPath('data.name', 'Updated Tag');
});

it('deletes a tag for authorized users', function () {
    SecurityHelper::actingAsTagApiUser(['delete_partner_tag']);

    $tag = Tag::factory()->create();

    $this->deleteJson(partnersTagRoute('destroy', $tag))
        ->assertOk()
        ->assertJsonPath('message', 'Tag deleted successfully.');

    $this->assertSoftDeleted('partners_tags', ['id' => $tag->id]);
});

it('restores a tag for authorized users', function () {
    SecurityHelper::actingAsTagApiUser(['restore_partner_tag']);

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->postJson(partnersTagRoute('restore', $tag->id))
        ->assertOk()
        ->assertJsonPath('message', 'Tag restored successfully.');
});

it('force deletes a tag for authorized users', function () {
    SecurityHelper::actingAsTagApiUser(['force_delete_partner_tag']);

    $tag = Tag::factory()->create();
    $tag->delete();

    $this->deleteJson(partnersTagRoute('force-destroy', $tag->id))
        ->assertOk()
        ->assertJsonPath('message', 'Tag permanently deleted.');

    $this->assertDatabaseMissing('partners_tags', ['id' => $tag->id]);
});
