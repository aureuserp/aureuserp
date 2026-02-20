<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Partner\Models\Industry;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

beforeEach(function () {
    if (! Schema::hasTable('partners_industries')) {
        test()->markTestSkipped(
            'Partners plugin tables are missing. Run `php artisan partners:install` before this suite.'
        );
    }

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsIndustryApiUser(array $permissions = []): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function industryRoute(string $action, mixed $industry = null): string
{
    $name = "admin.api.v1.partners.industries.{$action}";

    return $industry ? route($name, $industry) : route($name);
}

it('requires authentication to list industries', function () {
    $this->getJson(industryRoute('index'))->assertUnauthorized();
});

it('forbids listing industries without permission', function () {
    actingAsIndustryApiUser();

    $this->getJson(industryRoute('index'))->assertForbidden();
});

it('lists industries for authorized users', function () {
    actingAsIndustryApiUser(['view_any_partner_industry']);

    Industry::factory()->count(2)->create();

    $this->getJson(industryRoute('index'))
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates an industry with valid payload', function () {
    actingAsIndustryApiUser(['create_partner_industry']);

    $payload = Industry::factory()->make()->toArray();

    $this->postJson(industryRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Industry created successfully.')
        ->assertJsonPath('data.name', $payload['name']);
});

it('shows an industry for authorized users', function () {
    actingAsIndustryApiUser(['view_partner_industry']);

    $industry = Industry::factory()->create();

    $this->getJson(industryRoute('show', $industry))
        ->assertOk()
        ->assertJsonPath('data.id', $industry->id);
});

it('updates an industry for authorized users', function () {
    actingAsIndustryApiUser(['update_partner_industry']);

    $industry = Industry::factory()->create();

    $this->patchJson(industryRoute('update', $industry), ['name' => 'Updated Industry'])
        ->assertOk()
        ->assertJsonPath('message', 'Industry updated successfully.')
        ->assertJsonPath('data.name', 'Updated Industry');
});

it('deletes an industry for authorized users', function () {
    actingAsIndustryApiUser(['delete_partner_industry']);

    $industry = Industry::factory()->create();

    $this->deleteJson(industryRoute('destroy', $industry))
        ->assertOk()
        ->assertJsonPath('message', 'Industry deleted successfully.');

    $this->assertSoftDeleted('partners_industries', ['id' => $industry->id]);
});

it('restores an industry for authorized users', function () {
    actingAsIndustryApiUser(['restore_partner_industry']);

    $industry = Industry::factory()->create();
    $industry->delete();

    $this->postJson(industryRoute('restore', $industry->id))
        ->assertOk()
        ->assertJsonPath('message', 'Industry restored successfully.');
});

it('force deletes an industry for authorized users', function () {
    actingAsIndustryApiUser(['force_delete_partner_industry']);

    $industry = Industry::factory()->create();
    $industry->delete();

    $this->deleteJson(industryRoute('force-destroy', $industry->id))
        ->assertOk()
        ->assertJsonPath('message', 'Industry permanently deleted.');

    $this->assertDatabaseMissing('partners_industries', ['id' => $industry->id]);
});
