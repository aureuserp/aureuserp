<?php

use Webkul\Account\Models\PaymentTerm;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PAYMENT_TERM_JSON_STRUCTURE = [
    'id',
    'name',
];

const PAYMENT_TERM_REQUIRED_FIELDS = [
    'name',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsPaymentTermApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function paymentTermRoute(string $action, mixed $paymentTerm = null): string
{
    $name = "admin.api.v1.accounts.payment-terms.{$action}";

    return $paymentTerm ? route($name, $paymentTerm) : route($name);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list payment terms', function () {
    $this->getJson(paymentTermRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a payment term', function () {
    $this->postJson(paymentTermRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing payment terms without permission', function () {
    actingAsPaymentTermApiUser();

    $this->getJson(paymentTermRoute('index'))
        ->assertForbidden();
});

it('forbids creating a payment term without permission', function () {
    actingAsPaymentTermApiUser();

    $this->postJson(paymentTermRoute('store'), ['name' => 'Net 30'])
        ->assertForbidden();
});

it('forbids updating a payment term without permission', function () {
    actingAsPaymentTermApiUser();

    $paymentTerm = PaymentTerm::factory()->create();

    $this->patchJson(paymentTermRoute('update', $paymentTerm), [])
        ->assertForbidden();
});

it('forbids deleting a payment term without permission', function () {
    actingAsPaymentTermApiUser();

    $paymentTerm = PaymentTerm::factory()->create();

    $this->deleteJson(paymentTermRoute('destroy', $paymentTerm))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists payment terms for authorized users', function () {
    actingAsPaymentTermApiUser(['view_any_account_payment::term']);

    PaymentTerm::factory()->count(3)->create();

    $this->getJson(paymentTermRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a payment term', function () {
    actingAsPaymentTermApiUser(['create_account_payment::term']);

    $payload = ['name' => 'Net 30', 'note' => 'Payment due within 30 days'];

    $this->postJson(paymentTermRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Payment term created successfully.')
        ->assertJsonPath('data.name', 'Net 30')
        ->assertJsonStructure(['data' => PAYMENT_TERM_JSON_STRUCTURE]);

    $this->assertDatabaseHas('accounts_payment_terms', ['name' => 'Net 30']);
});

it('validates required fields when creating a payment term', function (string $field) {
    actingAsPaymentTermApiUser(['create_account_payment::term']);

    $payload = ['name' => 'Net 30'];
    unset($payload[$field]);

    $this->postJson(paymentTermRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(PAYMENT_TERM_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a payment term for authorized users', function () {
    actingAsPaymentTermApiUser(['view_account_payment::term']);

    $paymentTerm = PaymentTerm::factory()->create();

    $this->getJson(paymentTermRoute('show', $paymentTerm))
        ->assertOk()
        ->assertJsonPath('data.id', $paymentTerm->id)
        ->assertJsonStructure(['data' => PAYMENT_TERM_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent payment term', function () {
    actingAsPaymentTermApiUser(['view_account_payment::term']);

    $this->getJson(paymentTermRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a payment term', function () {
    actingAsPaymentTermApiUser(['update_account_payment::term']);

    $paymentTerm = PaymentTerm::factory()->create();

    $this->patchJson(paymentTermRoute('update', $paymentTerm), ['name' => 'Net 60'])
        ->assertOk()
        ->assertJsonPath('message', 'Payment term updated successfully.')
        ->assertJsonPath('data.name', 'Net 60');

    $this->assertDatabaseHas('accounts_payment_terms', [
        'id'   => $paymentTerm->id,
        'name' => 'Net 60',
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('soft-deletes a payment term', function () {
    actingAsPaymentTermApiUser(['delete_account_payment::term']);

    $paymentTerm = PaymentTerm::factory()->create();

    $this->deleteJson(paymentTermRoute('destroy', $paymentTerm))
        ->assertOk()
        ->assertJsonPath('message', 'Payment term deleted successfully.');

    $this->assertSoftDeleted('accounts_payment_terms', ['id' => $paymentTerm->id]);
});

// ── Restore ────────────────────────────────────────────────────────────────────

it('restores a soft-deleted payment term', function () {
    actingAsPaymentTermApiUser(['restore_account_payment::term']);

    $paymentTerm = PaymentTerm::factory()->create();
    $paymentTerm->delete();

    $this->postJson(route('admin.api.v1.accounts.payment-terms.restore', $paymentTerm))
        ->assertOk()
        ->assertJsonPath('message', 'Payment term restored successfully.');

    $this->assertDatabaseHas('accounts_payment_terms', [
        'id'         => $paymentTerm->id,
        'deleted_at' => null,
    ]);
});

// ── Force Delete ───────────────────────────────────────────────────────────────

it('force-deletes a payment term', function () {
    actingAsPaymentTermApiUser(['force_delete_account_payment::term']);

    $paymentTerm = PaymentTerm::factory()->create();
    $paymentTerm->delete();

    $this->deleteJson(route('admin.api.v1.accounts.payment-terms.force-destroy', $paymentTerm))
        ->assertOk()
        ->assertJsonPath('message', 'Payment term permanently deleted.');

    $this->assertDatabaseMissing('accounts_payment_terms', ['id' => $paymentTerm->id]);
});
