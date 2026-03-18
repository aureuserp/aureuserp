<?php

use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Product;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const CREDIT_NOTE_JSON_STRUCTURE = [
    'id',
    'partner_id',
    'currency_id',
    'journal_id',
    'state',
];

const CREDIT_NOTE_REQUIRED_FIELDS = [
    'partner_id',
    'currency_id',
    'journal_id',
    'invoice_date',
    'invoice_lines',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsCreditNoteApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function creditNoteRoute(string $action, mixed $creditNote = null): string
{
    $name = "admin.api.v1.accounts.credit-notes.{$action}";

    return $creditNote ? route($name, $creditNote) : route($name);
}

function makeCreditNoteLinePayload(array $overrides = []): array
{
    $product = Product::factory()->withAccounts()->create(['is_configurable' => false]);
    $uom = UOM::factory()->create();

    return array_merge([
        'product_id' => $product->id,
        'quantity'   => 1,
        'uom_id'     => $uom->id,
        'price_unit' => 80.00,
    ], $overrides);
}

function creditNotePayload(int $lineCount = 1, array $overrides = []): array
{
    $currency = Currency::first() ?? Currency::factory()->create();
    $company = Company::factory()->create(['currency_id' => $currency->id]);
    $partner = Partner::factory()->withAccounts()->create();
    $journal = Journal::factory()->sale()->create(['currency_id' => $currency->id, 'company_id' => $company->id]);

    if (auth()->check()) {
        auth()->user()->forceFill(['default_company_id' => $company->id])->saveQuietly();
    }

    $payload = [
        'partner_id'       => $partner->id,
        'currency_id'      => $currency->id,
        'journal_id'       => $journal->id,
        'invoice_date'     => now()->format('Y-m-d'),
        'invoice_date_due' => now()->addDays(30)->format('Y-m-d'),
        'invoice_lines'    => collect(range(1, $lineCount))
            ->map(fn () => makeCreditNoteLinePayload())
            ->all(),
    ];

    return array_replace_recursive($payload, $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list credit notes', function () {
    $this->getJson(creditNoteRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a credit note', function () {
    $this->postJson(creditNoteRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing credit notes without permission', function () {
    actingAsCreditNoteApiUser();

    $this->getJson(creditNoteRoute('index'))
        ->assertForbidden();
});

it('forbids creating a credit note without permission', function () {
    actingAsCreditNoteApiUser();

    $this->postJson(creditNoteRoute('store'), creditNotePayload())
        ->assertForbidden();
});

it('forbids updating a credit note without permission', function () {
    actingAsCreditNoteApiUser();

    $creditNote = Move::factory()->refund()->create();

    $this->patchJson(creditNoteRoute('update', $creditNote), [])
        ->assertForbidden();
});

it('forbids deleting a credit note without permission', function () {
    actingAsCreditNoteApiUser();

    $creditNote = Move::factory()->refund()->create();

    $this->deleteJson(creditNoteRoute('destroy', $creditNote))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists credit notes for authorized users', function () {
    actingAsCreditNoteApiUser(['view_any_account_refund']);

    Move::factory()->refund()->count(3)->create();

    $this->getJson(creditNoteRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('only returns OUT_REFUND type records in the credit note list', function () {
    actingAsCreditNoteApiUser(['view_any_account_refund']);

    Move::factory()->refund()->count(2)->create();
    Move::factory()->state(['move_type' => MoveType::IN_REFUND])->count(2)->create();

    $response = $this->getJson(creditNoteRoute('index'))
        ->assertOk();

    collect($response->json('data'))->each(function ($item) {
        expect($item['move_type'] ?? MoveType::OUT_REFUND->value)->toBe(MoveType::OUT_REFUND->value);
    });
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a credit note with lines', function () {
    actingAsCreditNoteApiUser(['create_account_refund']);

    $payload = creditNotePayload(lineCount: 2);

    $response = $this->postJson(creditNoteRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Credit note created successfully.')
        ->assertJsonPath('data.partner_id', $payload['partner_id'])
        ->assertJsonPath('data.state', MoveState::DRAFT->value)
        ->assertJsonStructure(['data' => CREDIT_NOTE_JSON_STRUCTURE]);

    $creditNoteId = $response->json('data.id');

    $this->assertDatabaseHas('accounts_account_moves', [
        'id'        => $creditNoteId,
        'move_type' => MoveType::OUT_REFUND->value,
        'state'     => MoveState::DRAFT->value,
    ]);
});

it('validates required fields when creating a credit note', function (string $field) {
    actingAsCreditNoteApiUser(['create_account_refund']);

    $payload = creditNotePayload();
    unset($payload[$field]);

    $this->postJson(creditNoteRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(CREDIT_NOTE_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a credit note for authorized users', function () {
    actingAsCreditNoteApiUser(['view_account_refund']);

    $creditNote = Move::factory()->refund()->create();

    $this->getJson(creditNoteRoute('show', $creditNote))
        ->assertOk()
        ->assertJsonPath('data.id', $creditNote->id)
        ->assertJsonStructure(['data' => CREDIT_NOTE_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent credit note', function () {
    actingAsCreditNoteApiUser(['view_account_refund']);

    $this->getJson(creditNoteRoute('show', 999999))
        ->assertNotFound();
});

it('cannot show a vendor refund via the credit notes endpoint', function () {
    actingAsCreditNoteApiUser(['view_account_refund']);

    $refund = Move::factory()->state(['move_type' => MoveType::IN_REFUND])->create();

    $this->getJson(creditNoteRoute('show', $refund))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a draft credit note', function () {
    actingAsCreditNoteApiUser(['update_account_refund']);

    $creditNote = Move::factory()->refund()->create();

    $this->patchJson(creditNoteRoute('update', $creditNote), ['reference' => 'CN-REF-001'])
        ->assertOk()
        ->assertJsonPath('message', 'Credit note updated successfully.');

    $this->assertDatabaseHas('accounts_account_moves', [
        'id'        => $creditNote->id,
        'reference' => 'CN-REF-001',
    ]);
});

it('cannot update a posted credit note', function () {
    actingAsCreditNoteApiUser(['update_account_refund']);

    $creditNote = Move::factory()->refund()->posted()->create();

    $this->patchJson(creditNoteRoute('update', $creditNote), ['reference' => 'CN-REF-001'])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot update a posted credit note.');
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes a draft credit note', function () {
    actingAsCreditNoteApiUser(['delete_account_refund']);

    $creditNote = Move::factory()->refund()->create();

    $this->deleteJson(creditNoteRoute('destroy', $creditNote))
        ->assertOk()
        ->assertJsonPath('message', 'Credit note deleted successfully.');

    $this->assertDatabaseMissing('accounts_account_moves', ['id' => $creditNote->id]);
});

it('cannot delete a posted credit note', function () {
    actingAsCreditNoteApiUser(['delete_account_refund']);

    $creditNote = Move::factory()->refund()->posted()->create();

    $this->deleteJson(creditNoteRoute('destroy', $creditNote))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot delete a posted or cancelled credit note.');
});

// ── Confirm ────────────────────────────────────────────────────────────────────

it('rejects confirming a posted credit note', function () {
    actingAsCreditNoteApiUser(['update_account_refund']);

    $creditNote = Move::factory()->refund()->posted()->create();

    $this->postJson(route('admin.api.v1.accounts.credit-notes.confirm', ['id' => $creditNote->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft credit notes can be confirmed.');
});

it('rejects confirming a cancelled credit note', function () {
    actingAsCreditNoteApiUser(['update_account_refund']);

    $creditNote = Move::factory()->refund()->cancelled()->create();

    $this->postJson(route('admin.api.v1.accounts.credit-notes.confirm', ['id' => $creditNote->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft credit notes can be confirmed.');
});

// ── Cancel ─────────────────────────────────────────────────────────────────────

it('rejects cancelling a posted credit note', function () {
    actingAsCreditNoteApiUser(['update_account_refund']);

    $creditNote = Move::factory()->refund()->posted()->create();

    $this->postJson(route('admin.api.v1.accounts.credit-notes.cancel', ['id' => $creditNote->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft credit notes can be cancelled.');
});

it('cancels a draft credit note', function () {
    actingAsCreditNoteApiUser(['update_account_refund']);

    $creditNote = Move::factory()->refund()->create();

    $this->postJson(route('admin.api.v1.accounts.credit-notes.cancel', ['id' => $creditNote->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Credit note cancelled successfully.');
});

// ── Reset to Draft ─────────────────────────────────────────────────────────────

it('rejects resetting a draft credit note to draft', function () {
    actingAsCreditNoteApiUser(['update_account_refund']);

    $creditNote = Move::factory()->refund()->create();

    $this->postJson(route('admin.api.v1.accounts.credit-notes.reset-to-draft', ['id' => $creditNote->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only posted or cancelled credit notes can be reset to draft.');
});

it('resets a cancelled credit note to draft', function () {
    actingAsCreditNoteApiUser(['update_account_refund']);

    $creditNote = Move::factory()->refund()->cancelled()->create();

    $this->postJson(route('admin.api.v1.accounts.credit-notes.reset-to-draft', ['id' => $creditNote->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Credit note reset to draft successfully.')
        ->assertJsonPath('data.state', MoveState::DRAFT->value);
});

// ── Set As Checked ─────────────────────────────────────────────────────────────

it('rejects setting a draft credit note as checked', function () {
    actingAsCreditNoteApiUser(['update_account_refund']);

    $creditNote = Move::factory()->refund()->create();

    $this->postJson(route('admin.api.v1.accounts.credit-notes.set-as-checked', ['id' => $creditNote->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-draft and unchecked credit notes can be marked as checked.');
});

it('rejects setting an already-checked credit note as checked', function () {
    actingAsCreditNoteApiUser(['update_account_refund']);

    $creditNote = Move::factory()->refund()->posted()->create(['checked' => true]);

    $this->postJson(route('admin.api.v1.accounts.credit-notes.set-as-checked', ['id' => $creditNote->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-draft and unchecked credit notes can be marked as checked.');
});
