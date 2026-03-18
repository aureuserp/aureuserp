<?php

use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Invoice;
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

const INVOICE_JSON_STRUCTURE = [
    'id',
    'partner_id',
    'currency_id',
    'journal_id',
    'invoice_date',
    'state',
];

const INVOICE_REQUIRED_FIELDS = [
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

function actingAsInvoiceApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function invoiceRoute(string $action, mixed $invoice = null): string
{
    $name = "admin.api.v1.accounts.invoices.{$action}";

    return $invoice ? route($name, $invoice) : route($name);
}

function makeInvoiceLinePayload(array $overrides = []): array
{
    $product = Product::factory()->withAccounts()->create(['is_configurable' => false]);
    $uom = UOM::factory()->create();

    return array_merge([
        'product_id' => $product->id,
        'quantity'   => 1,
        'uom_id'     => $uom->id,
        'price_unit' => 100.00,
    ], $overrides);
}

function invoicePayload(int $lineCount = 1, array $overrides = []): array
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
            ->map(fn () => makeInvoiceLinePayload())
            ->all(),
    ];

    return array_replace_recursive($payload, $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list invoices', function () {
    $this->getJson(invoiceRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create an invoice', function () {
    $this->postJson(invoiceRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing invoices without permission', function () {
    actingAsInvoiceApiUser();

    $this->getJson(invoiceRoute('index'))
        ->assertForbidden();
});

it('forbids creating an invoice without permission', function () {
    actingAsInvoiceApiUser();

    $this->postJson(invoiceRoute('store'), invoicePayload())
        ->assertForbidden();
});

it('forbids updating an invoice without permission', function () {
    actingAsInvoiceApiUser();

    $invoice = Invoice::factory()->create();

    $this->patchJson(invoiceRoute('update', $invoice), [])
        ->assertForbidden();
});

it('forbids deleting an invoice without permission', function () {
    actingAsInvoiceApiUser();

    $invoice = Invoice::factory()->create();

    $this->deleteJson(invoiceRoute('destroy', $invoice))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists invoices for authorized users', function () {
    actingAsInvoiceApiUser(['view_any_account_invoice']);

    Invoice::factory()->count(3)->create();

    $this->getJson(invoiceRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('only returns OUT_INVOICE type records in the invoice list', function () {
    actingAsInvoiceApiUser(['view_any_account_invoice']);

    Invoice::factory()->count(2)->create();
    Move::factory()->vendorBill()->count(2)->create();

    $response = $this->getJson(invoiceRoute('index'))
        ->assertOk();

    collect($response->json('data'))->each(function ($item) {
        expect($item['move_type'] ?? MoveType::OUT_INVOICE->value)->toBe(MoveType::OUT_INVOICE->value);
    });
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates an invoice with lines', function () {
    actingAsInvoiceApiUser(['create_account_invoice']);

    $payload = invoicePayload(lineCount: 2);

    $response = $this->postJson(invoiceRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Invoice created successfully.')
        ->assertJsonPath('data.partner_id', $payload['partner_id'])
        ->assertJsonPath('data.state', MoveState::DRAFT->value)
        ->assertJsonStructure(['data' => INVOICE_JSON_STRUCTURE]);

    $invoiceId = $response->json('data.id');

    $this->assertDatabaseHas('accounts_account_moves', [
        'id'         => $invoiceId,
        'partner_id' => $payload['partner_id'],
        'move_type'  => MoveType::OUT_INVOICE->value,
        'state'      => MoveState::DRAFT->value,
    ]);
});

it('validates required fields when creating an invoice', function (string $field) {
    actingAsInvoiceApiUser(['create_account_invoice']);

    $payload = invoicePayload();
    unset($payload[$field]);

    $this->postJson(invoiceRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(INVOICE_REQUIRED_FIELDS);

it('validates that either invoice_date_due or invoice_payment_term_id is required', function () {
    actingAsInvoiceApiUser(['create_account_invoice']);

    $payload = invoicePayload();
    unset($payload['invoice_date_due']);

    $this->postJson(invoiceRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['invoice_date_due']);
});

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows an invoice for authorized users', function () {
    actingAsInvoiceApiUser(['view_account_invoice']);

    $invoice = Invoice::factory()->create();

    $this->getJson(invoiceRoute('show', $invoice))
        ->assertOk()
        ->assertJsonPath('data.id', $invoice->id)
        ->assertJsonStructure(['data' => INVOICE_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent invoice', function () {
    actingAsInvoiceApiUser(['view_account_invoice']);

    $this->getJson(invoiceRoute('show', 999999))
        ->assertNotFound();
});

it('cannot show a bill via the invoices endpoint', function () {
    actingAsInvoiceApiUser(['view_account_invoice']);

    $bill = Move::factory()->vendorBill()->create();

    $this->getJson(invoiceRoute('show', $bill))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a draft invoice', function () {
    actingAsInvoiceApiUser(['update_account_invoice']);

    $invoice = Invoice::factory()->create();

    $this->patchJson(invoiceRoute('update', $invoice), ['reference' => 'REF-001'])
        ->assertOk()
        ->assertJsonPath('message', 'Invoice updated successfully.');

    $this->assertDatabaseHas('accounts_account_moves', [
        'id'        => $invoice->id,
        'reference' => 'REF-001',
    ]);
});

it('cannot update a posted invoice', function () {
    actingAsInvoiceApiUser(['update_account_invoice']);

    $invoice = Invoice::factory()->posted()->create();

    $this->patchJson(invoiceRoute('update', $invoice), ['reference' => 'REF-001'])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot update a posted invoice.');
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes a draft invoice', function () {
    actingAsInvoiceApiUser(['delete_account_invoice']);

    $invoice = Invoice::factory()->create();

    $this->deleteJson(invoiceRoute('destroy', $invoice))
        ->assertOk()
        ->assertJsonPath('message', 'Invoice deleted successfully.');

    $this->assertDatabaseMissing('accounts_account_moves', ['id' => $invoice->id]);
});

it('cannot delete a posted invoice', function () {
    actingAsInvoiceApiUser(['delete_account_invoice']);

    $invoice = Invoice::factory()->posted()->create();

    $this->deleteJson(invoiceRoute('destroy', $invoice))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot delete a posted or cancelled invoice.');
});

// ── Confirm ────────────────────────────────────────────────────────────────────

it('rejects confirming a posted invoice', function () {
    actingAsInvoiceApiUser(['update_account_invoice']);

    $invoice = Invoice::factory()->posted()->create();

    $this->postJson(route('admin.api.v1.accounts.invoices.confirm', ['id' => $invoice->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft invoices can be confirmed.');
});

it('rejects confirming a cancelled invoice', function () {
    actingAsInvoiceApiUser(['update_account_invoice']);

    $invoice = Invoice::factory()->cancelled()->create();

    $this->postJson(route('admin.api.v1.accounts.invoices.confirm', ['id' => $invoice->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft invoices can be confirmed.');
});

// ── Cancel ─────────────────────────────────────────────────────────────────────

it('rejects cancelling a posted invoice', function () {
    actingAsInvoiceApiUser(['update_account_invoice']);

    $invoice = Invoice::factory()->posted()->create();

    $this->postJson(route('admin.api.v1.accounts.invoices.cancel', ['id' => $invoice->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft invoices can be cancelled.');
});

it('cancels a draft invoice', function () {
    actingAsInvoiceApiUser(['update_account_invoice']);

    $invoice = Invoice::factory()->create();

    $this->postJson(route('admin.api.v1.accounts.invoices.cancel', ['id' => $invoice->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Invoice cancelled successfully.');
});

// ── Reset to Draft ─────────────────────────────────────────────────────────────

it('rejects resetting a draft invoice to draft', function () {
    actingAsInvoiceApiUser(['update_account_invoice']);

    $invoice = Invoice::factory()->create();

    $this->postJson(route('admin.api.v1.accounts.invoices.reset-to-draft', ['id' => $invoice->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only posted or cancelled invoices can be reset to draft.');
});

it('resets a cancelled invoice to draft', function () {
    actingAsInvoiceApiUser(['update_account_invoice']);

    $invoice = Invoice::factory()->cancelled()->create();

    $this->postJson(route('admin.api.v1.accounts.invoices.reset-to-draft', ['id' => $invoice->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Invoice reset to draft successfully.')
        ->assertJsonPath('data.state', MoveState::DRAFT->value);
});

// ── Set As Checked ─────────────────────────────────────────────────────────────

it('rejects setting a draft invoice as checked', function () {
    actingAsInvoiceApiUser(['update_account_invoice']);

    $invoice = Invoice::factory()->create();

    $this->postJson(route('admin.api.v1.accounts.invoices.set-as-checked', ['id' => $invoice->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-draft and unchecked invoices can be marked as checked.');
});

it('rejects setting an already-checked invoice as checked', function () {
    actingAsInvoiceApiUser(['update_account_invoice']);

    $invoice = Invoice::factory()->posted()->create(['checked' => true]);

    $this->postJson(route('admin.api.v1.accounts.invoices.set-as-checked', ['id' => $invoice->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-draft and unchecked invoices can be marked as checked.');
});

// ── Reverse ────────────────────────────────────────────────────────────────────

it('rejects reversing a draft invoice', function () {
    actingAsInvoiceApiUser(['update_account_invoice']);

    $invoice = Invoice::factory()->create();

    $this->postJson(route('admin.api.v1.accounts.invoices.reverse', ['id' => $invoice->id]), [
        'reason'     => 'Reversal reason',
        'journal_id' => Journal::factory()->create()->id,
        'date'       => now()->format('Y-m-d'),
    ])->assertUnprocessable()
        ->assertJsonPath('message', 'Only posted invoices can be reversed.');
});
