<?php

use Webkul\Account\Models\Incoterm;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const INCOTERM_JSON_STRUCTURE = [
    'id',
    'code',
    'name',
];

const INCOTERM_REQUIRED_FIELDS = [
    'code',
    'name',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsIncotermApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function incotermRoute(string $action, mixed $incoterm = null): string
{
    $name = "admin.api.v1.accounts.incoterms.{$action}";

    return $incoterm ? route($name, $incoterm) : route($name);
}

function incotermPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'code' => 'EXW',
        'name' => 'Ex Works',
    ], $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list incoterms', function () {
    $this->getJson(incotermRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create an incoterm', function () {
    $this->postJson(incotermRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing incoterms without permission', function () {
    actingAsIncotermApiUser();

    $this->getJson(incotermRoute('index'))
        ->assertForbidden();
});

it('forbids creating an incoterm without permission', function () {
    actingAsIncotermApiUser();

    $this->postJson(incotermRoute('store'), incotermPayload())
        ->assertForbidden();
});

it('forbids updating an incoterm without permission', function () {
    actingAsIncotermApiUser();

    $incoterm = Incoterm::factory()->create();

    $this->patchJson(incotermRoute('update', $incoterm), [])
        ->assertForbidden();
});

it('forbids deleting an incoterm without permission', function () {
    actingAsIncotermApiUser();

    $incoterm = Incoterm::factory()->create();

    $this->deleteJson(incotermRoute('destroy', $incoterm))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists incoterms for authorized users', function () {
    actingAsIncotermApiUser(['view_any_account_incoterm']);

    Incoterm::factory()->count(3)->create();

    $this->getJson(incotermRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters incoterms by code', function () {
    actingAsIncotermApiUser(['view_any_account_incoterm']);

    $incoterm = Incoterm::factory()->create(['code' => 'FOB']);
    Incoterm::factory()->count(2)->create();

    $response = $this->getJson(incotermRoute('index').'?filter[code]=FOB')
        ->assertOk();

    collect($response->json('data'))->each(function ($item) {
        expect($item['code'])->toContain('FOB');
    });

    expect(collect($response->json('data'))->firstWhere('id', $incoterm->id))->not->toBeNull();
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates an incoterm', function () {
    actingAsIncotermApiUser(['create_account_incoterm']);

    $payload = incotermPayload();

    $this->postJson(incotermRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Incoterm created successfully.')
        ->assertJsonPath('data.code', $payload['code'])
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => INCOTERM_JSON_STRUCTURE]);

    $this->assertDatabaseHas('accounts_incoterms', [
        'code' => $payload['code'],
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating an incoterm', function (string $field) {
    actingAsIncotermApiUser(['create_account_incoterm']);

    $payload = incotermPayload();
    unset($payload[$field]);

    $this->postJson(incotermRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(INCOTERM_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows an incoterm for authorized users', function () {
    actingAsIncotermApiUser(['view_account_incoterm']);

    $incoterm = Incoterm::factory()->create();

    $this->getJson(incotermRoute('show', $incoterm))
        ->assertOk()
        ->assertJsonPath('data.id', $incoterm->id)
        ->assertJsonStructure(['data' => INCOTERM_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent incoterm', function () {
    actingAsIncotermApiUser(['view_account_incoterm']);

    $this->getJson(incotermRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates an incoterm', function () {
    actingAsIncotermApiUser(['update_account_incoterm']);

    $incoterm = Incoterm::factory()->create();

    $this->patchJson(incotermRoute('update', $incoterm), ['name' => 'Free On Board'])
        ->assertOk()
        ->assertJsonPath('message', 'Incoterm updated successfully.')
        ->assertJsonPath('data.name', 'Free On Board');

    $this->assertDatabaseHas('accounts_incoterms', [
        'id'   => $incoterm->id,
        'name' => 'Free On Board',
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('soft deletes an incoterm', function () {
    actingAsIncotermApiUser(['delete_account_incoterm']);

    $incoterm = Incoterm::factory()->create();

    $this->deleteJson(incotermRoute('destroy', $incoterm))
        ->assertOk()
        ->assertJsonPath('message', 'Incoterm deleted successfully.');

    $this->assertSoftDeleted('accounts_incoterms', ['id' => $incoterm->id]);
});

// ── Restore ────────────────────────────────────────────────────────────────────

it('restores a soft-deleted incoterm', function () {
    actingAsIncotermApiUser(['restore_account_incoterm']);

    $incoterm = Incoterm::factory()->create();
    $incoterm->delete();

    $this->postJson(incotermRoute('restore', $incoterm))
        ->assertOk()
        ->assertJsonPath('message', 'Incoterm restored successfully.');

    $this->assertDatabaseHas('accounts_incoterms', [
        'id'         => $incoterm->id,
        'deleted_at' => null,
    ]);
});

// ── Force Delete ───────────────────────────────────────────────────────────────

it('permanently deletes an incoterm', function () {
    actingAsIncotermApiUser(['force_delete_account_incoterm']);

    $incoterm = Incoterm::factory()->create();
    $incoterm->delete();

    $this->deleteJson(incotermRoute('force-destroy', $incoterm))
        ->assertOk()
        ->assertJsonPath('message', 'Incoterm permanently deleted.');

    $this->assertDatabaseMissing('accounts_incoterms', ['id' => $incoterm->id]);
});
