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

const BILL_JSON_STRUCTURE = [
    'id',
    'partner_id',
    'currency_id',
    'journal_id',
    'state',
];

const BILL_REQUIRED_FIELDS = [
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

function actingAsBillApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function billRoute(string $action, mixed $bill = null): string
{
    $name = "admin.api.v1.accounts.bills.{$action}";

    return $bill ? route($name, $bill) : route($name);
}

function makeBillLinePayload(array $overrides = []): array
{
    $product = Product::factory()->withAccounts()->create(['is_configurable' => false]);
    $uom = UOM::factory()->create();

    return array_merge([
        'product_id' => $product->id,
        'quantity'   => 1,
        'uom_id'     => $uom->id,
        'price_unit' => 150.00,
    ], $overrides);
}

function billPayload(int $lineCount = 1, array $overrides = []): array
{
    $currency = Currency::first() ?? Currency::factory()->create();
    $company = Company::factory()->create(['currency_id' => $currency->id]);
    $partner = Partner::factory()->withAccounts()->create();
    $journal = Journal::factory()->purchase()->create(['currency_id' => $currency->id, 'company_id' => $company->id]);

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
            ->map(fn () => makeBillLinePayload())
            ->all(),
    ];

    return array_replace_recursive($payload, $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list bills', function () {
    $this->getJson(billRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a bill', function () {
    $this->postJson(billRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing bills without permission', function () {
    actingAsBillApiUser();

    $this->getJson(billRoute('index'))
        ->assertForbidden();
});

it('forbids creating a bill without permission', function () {
    actingAsBillApiUser();

    $this->postJson(billRoute('store'), billPayload())
        ->assertForbidden();
});

it('forbids updating a bill without permission', function () {
    actingAsBillApiUser();

    $bill = Move::factory()->vendorBill()->create();

    $this->patchJson(billRoute('update', $bill), [])
        ->assertForbidden();
});

it('forbids deleting a bill without permission', function () {
    actingAsBillApiUser();

    $bill = Move::factory()->vendorBill()->create();

    $this->deleteJson(billRoute('destroy', $bill))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists bills for authorized users', function () {
    actingAsBillApiUser(['view_any_account_bill']);

    Move::factory()->vendorBill()->count(3)->create();

    $this->getJson(billRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('only returns IN_INVOICE type records in the bill list', function () {
    actingAsBillApiUser(['view_any_account_bill']);

    Move::factory()->vendorBill()->count(2)->create();
    Invoice::factory()->count(2)->create();

    $response = $this->getJson(billRoute('index'))
        ->assertOk();

    collect($response->json('data'))->each(function ($item) {
        expect($item['move_type'] ?? MoveType::IN_INVOICE->value)->toBe(MoveType::IN_INVOICE->value);
    });
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a bill with lines', function () {
    actingAsBillApiUser(['create_account_bill']);

    $payload = billPayload(lineCount: 2);

    $response = $this->postJson(billRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Bill created successfully.')
        ->assertJsonPath('data.partner_id', $payload['partner_id'])
        ->assertJsonPath('data.state', MoveState::DRAFT->value)
        ->assertJsonStructure(['data' => BILL_JSON_STRUCTURE]);

    $billId = $response->json('data.id');

    $this->assertDatabaseHas('accounts_account_moves', [
        'id'        => $billId,
        'move_type' => MoveType::IN_INVOICE->value,
        'state'     => MoveState::DRAFT->value,
    ]);
});

it('validates required fields when creating a bill', function (string $field) {
    actingAsBillApiUser(['create_account_bill']);

    $payload = billPayload();
    unset($payload[$field]);

    $this->postJson(billRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(BILL_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a bill for authorized users', function () {
    actingAsBillApiUser(['view_account_bill']);

    $bill = Move::factory()->vendorBill()->create();

    $this->getJson(billRoute('show', $bill))
        ->assertOk()
        ->assertJsonPath('data.id', $bill->id)
        ->assertJsonStructure(['data' => BILL_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent bill', function () {
    actingAsBillApiUser(['view_account_bill']);

    $this->getJson(billRoute('show', 999999))
        ->assertNotFound();
});

it('cannot show an invoice via the bills endpoint', function () {
    actingAsBillApiUser(['view_account_bill']);

    $invoice = Invoice::factory()->create();

    $this->getJson(billRoute('show', $invoice))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a draft bill', function () {
    actingAsBillApiUser(['update_account_bill']);

    $bill = Move::factory()->vendorBill()->create();

    $this->patchJson(billRoute('update', $bill), ['reference' => 'BILL-REF-001'])
        ->assertOk()
        ->assertJsonPath('message', 'Bill updated successfully.');

    $this->assertDatabaseHas('accounts_account_moves', [
        'id'        => $bill->id,
        'reference' => 'BILL-REF-001',
    ]);
});

it('cannot update a posted bill', function () {
    actingAsBillApiUser(['update_account_bill']);

    $bill = Move::factory()->vendorBill()->posted()->create();

    $this->patchJson(billRoute('update', $bill), ['reference' => 'REF-001'])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot update a posted bill.');
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes a draft bill', function () {
    actingAsBillApiUser(['delete_account_bill']);

    $bill = Move::factory()->vendorBill()->create();

    $this->deleteJson(billRoute('destroy', $bill))
        ->assertOk()
        ->assertJsonPath('message', 'Bill deleted successfully.');

    $this->assertDatabaseMissing('accounts_account_moves', ['id' => $bill->id]);
});

it('cannot delete a posted bill', function () {
    actingAsBillApiUser(['delete_account_bill']);

    $bill = Move::factory()->vendorBill()->posted()->create();

    $this->deleteJson(billRoute('destroy', $bill))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot delete a posted or cancelled bill.');
});

// ── Confirm ────────────────────────────────────────────────────────────────────

it('rejects confirming a posted bill', function () {
    actingAsBillApiUser(['update_account_bill']);

    $bill = Move::factory()->vendorBill()->posted()->create();

    $this->postJson(route('admin.api.v1.accounts.bills.confirm', ['id' => $bill->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft bills can be confirmed.');
});

it('rejects confirming a cancelled bill', function () {
    actingAsBillApiUser(['update_account_bill']);

    $bill = Move::factory()->vendorBill()->cancelled()->create();

    $this->postJson(route('admin.api.v1.accounts.bills.confirm', ['id' => $bill->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft bills can be confirmed.');
});

// ── Cancel ─────────────────────────────────────────────────────────────────────

it('rejects cancelling a posted bill', function () {
    actingAsBillApiUser(['update_account_bill']);

    $bill = Move::factory()->vendorBill()->posted()->create();

    $this->postJson(route('admin.api.v1.accounts.bills.cancel', ['id' => $bill->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft bills can be cancelled.');
});

it('cancels a draft bill', function () {
    actingAsBillApiUser(['update_account_bill']);

    $bill = Move::factory()->vendorBill()->create();

    $this->postJson(route('admin.api.v1.accounts.bills.cancel', ['id' => $bill->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Bill cancelled successfully.');
});

// ── Reset to Draft ─────────────────────────────────────────────────────────────

it('rejects resetting a draft bill to draft', function () {
    actingAsBillApiUser(['update_account_bill']);

    $bill = Move::factory()->vendorBill()->create();

    $this->postJson(route('admin.api.v1.accounts.bills.reset-to-draft', ['id' => $bill->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only posted or cancelled bills can be reset to draft.');
});

it('resets a cancelled bill to draft', function () {
    actingAsBillApiUser(['update_account_bill']);

    $bill = Move::factory()->vendorBill()->cancelled()->create();

    $this->postJson(route('admin.api.v1.accounts.bills.reset-to-draft', ['id' => $bill->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Bill reset to draft successfully.')
        ->assertJsonPath('data.state', MoveState::DRAFT->value);
});

// ── Set As Checked ─────────────────────────────────────────────────────────────

it('rejects setting a draft bill as checked', function () {
    actingAsBillApiUser(['update_account_bill']);

    $bill = Move::factory()->vendorBill()->create();

    $this->postJson(route('admin.api.v1.accounts.bills.set-as-checked', ['id' => $bill->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-draft and unchecked bills can be marked as checked.');
});

it('rejects setting an already-checked bill as checked', function () {
    actingAsBillApiUser(['update_account_bill']);

    $bill = Move::factory()->vendorBill()->posted()->create(['checked' => true]);

    $this->postJson(route('admin.api.v1.accounts.bills.set-as-checked', ['id' => $bill->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-draft and unchecked bills can be marked as checked.');
});

// ── Reverse ────────────────────────────────────────────────────────────────────

it('rejects reversing a draft bill', function () {
    actingAsBillApiUser(['update_account_bill']);

    $bill = Move::factory()->vendorBill()->create();

    $this->postJson(route('admin.api.v1.accounts.bills.reverse', ['id' => $bill->id]), [
        'reason'     => 'Reversal reason',
        'journal_id' => Journal::factory()->create()->id,
        'date'       => now()->format('Y-m-d'),
    ])->assertUnprocessable()
        ->assertJsonPath('message', 'Only posted bills can be reversed.');
});
