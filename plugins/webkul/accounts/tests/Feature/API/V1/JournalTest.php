<?php

use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Journal;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const JOURNAL_JSON_STRUCTURE = [
    'id',
    'name',
    'code',
    'type',
];

const JOURNAL_REQUIRED_FIELDS = [
    'name',
    'code',
    'type',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsJournalApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function journalRoute(string $action, mixed $journal = null): string
{
    $name = "admin.api.v1.accounts.journals.{$action}";

    return $journal ? route($name, $journal) : route($name);
}

function journalPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name' => 'Test Sales Journal',
        'code' => 'TSJ',
        'type' => JournalType::SALE->value,
    ], $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list journals', function () {
    $this->getJson(journalRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a journal', function () {
    $this->postJson(journalRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing journals without permission', function () {
    actingAsJournalApiUser();

    $this->getJson(journalRoute('index'))
        ->assertForbidden();
});

it('forbids creating a journal without permission', function () {
    actingAsJournalApiUser();

    $this->postJson(journalRoute('store'), journalPayload())
        ->assertForbidden();
});

it('forbids updating a journal without permission', function () {
    actingAsJournalApiUser();

    $journal = Journal::factory()->create();

    $this->patchJson(journalRoute('update', $journal), [])
        ->assertForbidden();
});

it('forbids deleting a journal without permission', function () {
    actingAsJournalApiUser();

    $journal = Journal::factory()->create();

    $this->deleteJson(journalRoute('destroy', $journal))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists journals for authorized users', function () {
    actingAsJournalApiUser(['view_any_account_journal']);

    Journal::factory()->count(3)->create();

    $this->getJson(journalRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a journal', function () {
    actingAsJournalApiUser(['create_account_journal']);

    $payload = journalPayload(['name' => 'Customer Sales', 'code' => 'CSJ']);

    $this->postJson(journalRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Journal created successfully.')
        ->assertJsonPath('data.name', 'Customer Sales')
        ->assertJsonPath('data.code', 'CSJ')
        ->assertJsonStructure(['data' => JOURNAL_JSON_STRUCTURE]);

    $this->assertDatabaseHas('accounts_journals', [
        'name' => 'Customer Sales',
        'code' => 'CSJ',
        'type' => JournalType::SALE->value,
    ]);
});

it('validates required fields when creating a journal', function (string $field) {
    actingAsJournalApiUser(['create_account_journal']);

    $payload = journalPayload();
    unset($payload[$field]);

    $this->postJson(journalRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(JOURNAL_REQUIRED_FIELDS);

it('validates journal type must be a valid enum value', function () {
    actingAsJournalApiUser(['create_account_journal']);

    $payload = journalPayload(['type' => 'invalid_type']);

    $this->postJson(journalRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

it('validates journal code must be at most 5 characters', function () {
    actingAsJournalApiUser(['create_account_journal']);

    $payload = journalPayload(['code' => 'TOOLONGCODE']);

    $this->postJson(journalRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['code']);
});

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a journal for authorized users', function () {
    actingAsJournalApiUser(['view_account_journal']);

    $journal = Journal::factory()->create();

    $this->getJson(journalRoute('show', $journal))
        ->assertOk()
        ->assertJsonPath('data.id', $journal->id)
        ->assertJsonStructure(['data' => JOURNAL_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent journal', function () {
    actingAsJournalApiUser(['view_account_journal']);

    $this->getJson(journalRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a journal', function () {
    actingAsJournalApiUser(['update_account_journal']);

    $journal = Journal::factory()->create();

    $this->patchJson(journalRoute('update', $journal), ['name' => 'Updated Journal Name'])
        ->assertOk()
        ->assertJsonPath('message', 'Journal updated successfully.')
        ->assertJsonPath('data.name', 'Updated Journal Name');

    $this->assertDatabaseHas('accounts_journals', [
        'id'   => $journal->id,
        'name' => 'Updated Journal Name',
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes a journal', function () {
    actingAsJournalApiUser(['delete_account_journal']);

    $journal = Journal::factory()->create();

    $this->deleteJson(journalRoute('destroy', $journal))
        ->assertOk()
        ->assertJsonPath('message', 'Journal deleted successfully.');

    $this->assertDatabaseMissing('accounts_journals', ['id' => $journal->id]);
});
