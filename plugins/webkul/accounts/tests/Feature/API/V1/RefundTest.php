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

const REFUND_JSON_STRUCTURE = [
    'id',
    'partner_id',
    'currency_id',
    'journal_id',
    'state',
];

const REFUND_REQUIRED_FIELDS = [
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

function actingAsRefundApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function refundRoute(string $action, mixed $refund = null): string
{
    $name = "admin.api.v1.accounts.refunds.{$action}";

    return $refund ? route($name, $refund) : route($name);
}

function makeRefundLinePayload(array $overrides = []): array
{
    $product = Product::factory()->withAccounts()->create(['is_configurable' => false]);
    $uom = UOM::factory()->create();

    return array_merge([
        'product_id' => $product->id,
        'quantity'   => 1,
        'uom_id'     => $uom->id,
        'price_unit' => 90.00,
    ], $overrides);
}

function refundPayload(int $lineCount = 1, array $overrides = []): array
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
            ->map(fn () => makeRefundLinePayload())
            ->all(),
    ];

    return array_replace_recursive($payload, $overrides);
}

function makeVendorRefund(array $attributes = []): Move
{
    return Move::factory()->state(array_merge(['move_type' => MoveType::IN_REFUND], $attributes))->create();
}

function makePostedVendorRefund(array $attributes = []): Move
{
    return Move::factory()->posted()->state(array_merge(['move_type' => MoveType::IN_REFUND], $attributes))->create();
}

function makeCancelledVendorRefund(array $attributes = []): Move
{
    return Move::factory()->cancelled()->state(array_merge(['move_type' => MoveType::IN_REFUND], $attributes))->create();
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list refunds', function () {
    $this->getJson(refundRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a refund', function () {
    $this->postJson(refundRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing refunds without permission', function () {
    actingAsRefundApiUser();

    $this->getJson(refundRoute('index'))
        ->assertForbidden();
});

it('forbids creating a refund without permission', function () {
    actingAsRefundApiUser();

    $this->postJson(refundRoute('store'), refundPayload())
        ->assertForbidden();
});

it('forbids updating a refund without permission', function () {
    actingAsRefundApiUser();

    $refund = makeVendorRefund();

    $this->patchJson(refundRoute('update', $refund), [])
        ->assertForbidden();
});

it('forbids deleting a refund without permission', function () {
    actingAsRefundApiUser();

    $refund = makeVendorRefund();

    $this->deleteJson(refundRoute('destroy', $refund))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists refunds for authorized users', function () {
    actingAsRefundApiUser(['view_any_account_refund']);

    makeVendorRefund();
    makeVendorRefund();
    makeVendorRefund();

    $this->getJson(refundRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('only returns IN_REFUND type records in the refund list', function () {
    actingAsRefundApiUser(['view_any_account_refund']);

    makeVendorRefund();
    makeVendorRefund();
    Move::factory()->refund()->count(2)->create();

    $response = $this->getJson(refundRoute('index'))
        ->assertOk();

    collect($response->json('data'))->each(function ($item) {
        expect($item['move_type'] ?? MoveType::IN_REFUND->value)->toBe(MoveType::IN_REFUND->value);
    });
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a refund with lines', function () {
    actingAsRefundApiUser(['create_account_refund']);

    $payload = refundPayload(lineCount: 2);

    $response = $this->postJson(refundRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Refund created successfully.')
        ->assertJsonPath('data.partner_id', $payload['partner_id'])
        ->assertJsonPath('data.state', MoveState::DRAFT->value)
        ->assertJsonStructure(['data' => REFUND_JSON_STRUCTURE]);

    $refundId = $response->json('data.id');

    $this->assertDatabaseHas('accounts_account_moves', [
        'id'        => $refundId,
        'move_type' => MoveType::IN_REFUND->value,
        'state'     => MoveState::DRAFT->value,
    ]);
});

it('validates required fields when creating a refund', function (string $field) {
    actingAsRefundApiUser(['create_account_refund']);

    $payload = refundPayload();
    unset($payload[$field]);

    $this->postJson(refundRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(REFUND_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a refund for authorized users', function () {
    actingAsRefundApiUser(['view_account_refund']);

    $refund = makeVendorRefund();

    $this->getJson(refundRoute('show', $refund))
        ->assertOk()
        ->assertJsonPath('data.id', $refund->id)
        ->assertJsonStructure(['data' => REFUND_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent refund', function () {
    actingAsRefundApiUser(['view_account_refund']);

    $this->getJson(refundRoute('show', 999999))
        ->assertNotFound();
});

it('cannot show a customer credit note via the refunds endpoint', function () {
    actingAsRefundApiUser(['view_account_refund']);

    $creditNote = Move::factory()->refund()->create();

    $this->getJson(refundRoute('show', $creditNote))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a draft refund', function () {
    actingAsRefundApiUser(['update_account_refund']);

    $refund = makeVendorRefund();

    $this->patchJson(refundRoute('update', $refund), ['reference' => 'VR-REF-001'])
        ->assertOk()
        ->assertJsonPath('message', 'Refund updated successfully.');

    $this->assertDatabaseHas('accounts_account_moves', [
        'id'        => $refund->id,
        'reference' => 'VR-REF-001',
    ]);
});

it('cannot update a posted refund', function () {
    actingAsRefundApiUser(['update_account_refund']);

    $refund = makePostedVendorRefund();

    $this->patchJson(refundRoute('update', $refund), ['reference' => 'REF-001'])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot update a posted refund.');
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes a draft refund', function () {
    actingAsRefundApiUser(['delete_account_refund']);

    $refund = makeVendorRefund();

    $this->deleteJson(refundRoute('destroy', $refund))
        ->assertOk()
        ->assertJsonPath('message', 'Refund deleted successfully.');

    $this->assertDatabaseMissing('accounts_account_moves', ['id' => $refund->id]);
});

it('cannot delete a posted refund', function () {
    actingAsRefundApiUser(['delete_account_refund']);

    $refund = makePostedVendorRefund();

    $this->deleteJson(refundRoute('destroy', $refund))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot delete a posted or cancelled refund.');
});

// ── Confirm ────────────────────────────────────────────────────────────────────

it('rejects confirming a posted refund', function () {
    actingAsRefundApiUser(['update_account_refund']);

    $refund = makePostedVendorRefund();

    $this->postJson(route('admin.api.v1.accounts.refunds.confirm', ['id' => $refund->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft refunds can be confirmed.');
});

it('rejects confirming a cancelled refund', function () {
    actingAsRefundApiUser(['update_account_refund']);

    $refund = makeCancelledVendorRefund();

    $this->postJson(route('admin.api.v1.accounts.refunds.confirm', ['id' => $refund->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft refunds can be confirmed.');
});

// ── Cancel ─────────────────────────────────────────────────────────────────────

it('rejects cancelling a posted refund', function () {
    actingAsRefundApiUser(['update_account_refund']);

    $refund = makePostedVendorRefund();

    $this->postJson(route('admin.api.v1.accounts.refunds.cancel', ['id' => $refund->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft refunds can be cancelled.');
});

it('cancels a draft refund', function () {
    actingAsRefundApiUser(['update_account_refund']);

    $refund = makeVendorRefund();

    $this->postJson(route('admin.api.v1.accounts.refunds.cancel', ['id' => $refund->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Refund cancelled successfully.');
});

// ── Reset to Draft ─────────────────────────────────────────────────────────────

it('rejects resetting a draft refund to draft', function () {
    actingAsRefundApiUser(['update_account_refund']);

    $refund = makeVendorRefund();

    $this->postJson(route('admin.api.v1.accounts.refunds.reset-to-draft', ['id' => $refund->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only posted or cancelled refunds can be reset to draft.');
});

it('resets a cancelled refund to draft', function () {
    actingAsRefundApiUser(['update_account_refund']);

    $refund = makeCancelledVendorRefund();

    $this->postJson(route('admin.api.v1.accounts.refunds.reset-to-draft', ['id' => $refund->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Refund reset to draft successfully.')
        ->assertJsonPath('data.state', MoveState::DRAFT->value);
});

// ── Set As Checked ─────────────────────────────────────────────────────────────

it('rejects setting a draft refund as checked', function () {
    actingAsRefundApiUser(['update_account_refund']);

    $refund = makeVendorRefund();

    $this->postJson(route('admin.api.v1.accounts.refunds.set-as-checked', ['id' => $refund->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-draft and unchecked refunds can be marked as checked.');
});

it('rejects setting an already-checked refund as checked', function () {
    actingAsRefundApiUser(['update_account_refund']);

    $refund = makePostedVendorRefund(['checked' => true]);

    $this->postJson(route('admin.api.v1.accounts.refunds.set-as-checked', ['id' => $refund->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-draft and unchecked refunds can be marked as checked.');
});
