<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Partner\Models\Title;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

beforeEach(function () {
    if (! Schema::hasTable('partners_titles')) {
        test()->markTestSkipped(
            'Partners plugin tables are missing. Run `php artisan partners:install` before this suite.'
        );
    }

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsTitleApiUser(array $permissions = []): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function titleRoute(string $action, mixed $title = null): string
{
    $name = "admin.api.v1.partners.titles.{$action}";

    return $title ? route($name, $title) : route($name);
}

it('requires authentication to list titles', function () {
    $this->getJson(titleRoute('index'))->assertUnauthorized();
});

it('forbids listing titles without permission', function () {
    actingAsTitleApiUser();

    $this->getJson(titleRoute('index'))->assertForbidden();
});

it('lists titles for authorized users', function () {
    actingAsTitleApiUser(['view_any_partner_title']);

    Title::factory()->count(2)->create();

    $this->getJson(titleRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a title with valid payload', function () {
    actingAsTitleApiUser(['create_partner_title']);

    $payload = Title::factory()->make()->toArray();

    $this->postJson(titleRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Title created successfully.')
        ->assertJsonPath('data.name', $payload['name']);
});

it('forbids showing title because policy has no view ability', function () {
    actingAsTitleApiUser(['view_any_partner_title']);

    $title = Title::factory()->create();

    $this->getJson(titleRoute('show', $title))->assertForbidden();
});

it('updates a title for authorized users', function () {
    actingAsTitleApiUser(['update_partner_title']);

    $title = Title::factory()->create();

    $this->patchJson(titleRoute('update', $title), ['name' => 'Updated Title'])
        ->assertOk()
        ->assertJsonPath('message', 'Title updated successfully.')
        ->assertJsonPath('data.name', 'Updated Title');
});

it('deletes a title for authorized users', function () {
    actingAsTitleApiUser(['delete_partner_title']);

    $title = Title::factory()->create();

    $this->deleteJson(titleRoute('destroy', $title))
        ->assertOk()
        ->assertJsonPath('message', 'Title deleted successfully.');

    $this->assertDatabaseMissing('partners_titles', ['id' => $title->id]);
});
