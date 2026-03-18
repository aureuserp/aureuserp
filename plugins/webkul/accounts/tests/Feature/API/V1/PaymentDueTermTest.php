<?php

use Webkul\Account\Enums\DelayType;
use Webkul\Account\Enums\DueTermValue;
use Webkul\Account\Models\PaymentDueTerm;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PAYMENT_DUE_TERM_JSON_STRUCTURE = [
    'id',
    'value',
    'value_amount',
    'delay_type',
    'nb_days',
];

const PAYMENT_DUE_TERM_REQUIRED_FIELDS = [
    'value',
    'value_amount',
    'delay_type',
    'nb_days',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsPaymentDueTermApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function paymentDueTermRoute(string $action, PaymentTerm $paymentTerm, mixed $dueTerm = null): string
{
    $name = "admin.api.v1.accounts.payment-terms.due-terms.{$action}";

    if ($dueTerm) {
        return route($name, [$paymentTerm, $dueTerm]);
    }

    return route($name, $paymentTerm);
}

function paymentDueTermPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'value'        => DueTermValue::PERCENT->value,
        'value_amount' => 100,
        'delay_type'   => DelayType::DAYS_AFTER->value,
        'nb_days'      => 30,
    ], $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list payment due terms', function () {
    $paymentTerm = PaymentTerm::factory()->create();

    $this->getJson(paymentDueTermRoute('index', $paymentTerm))
        ->assertUnauthorized();
});

it('requires authentication to create a payment due term', function () {
    $paymentTerm = PaymentTerm::factory()->create();

    $this->postJson(paymentDueTermRoute('store', $paymentTerm), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing payment due terms without permission', function () {
    actingAsPaymentDueTermApiUser();

    $paymentTerm = PaymentTerm::factory()->create();

    $this->getJson(paymentDueTermRoute('index', $paymentTerm))
        ->assertForbidden();
});

it('forbids creating a payment due term without permission', function () {
    actingAsPaymentDueTermApiUser();

    $paymentTerm = PaymentTerm::factory()->create();

    $this->postJson(paymentDueTermRoute('store', $paymentTerm), paymentDueTermPayload())
        ->assertForbidden();
});

it('forbids updating a payment due term without permission', function () {
    actingAsPaymentDueTermApiUser();

    $paymentTerm = PaymentTerm::factory()->create();
    $dueTerm = PaymentDueTerm::factory()->create(['payment_id' => $paymentTerm->id]);

    $this->patchJson(paymentDueTermRoute('update', $paymentTerm, $dueTerm), [])
        ->assertForbidden();
});

it('forbids deleting a payment due term without permission', function () {
    actingAsPaymentDueTermApiUser();

    $paymentTerm = PaymentTerm::factory()->create();
    $dueTerm = PaymentDueTerm::factory()->create(['payment_id' => $paymentTerm->id]);

    $this->deleteJson(paymentDueTermRoute('destroy', $paymentTerm, $dueTerm))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists payment due terms for authorized users', function () {
    actingAsPaymentDueTermApiUser(['view_account_payment::term']);

    $paymentTerm = PaymentTerm::factory()->create();
    PaymentDueTerm::factory()->count(3)->create(['payment_id' => $paymentTerm->id]);

    $this->getJson(paymentDueTermRoute('index', $paymentTerm))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('only lists due terms for the specified payment term', function () {
    actingAsPaymentDueTermApiUser(['view_account_payment::term']);

    $paymentTerm = PaymentTerm::factory()->create();
    $otherPaymentTerm = PaymentTerm::factory()->create();

    PaymentDueTerm::factory()->count(2)->create(['payment_id' => $paymentTerm->id]);
    PaymentDueTerm::factory()->count(2)->create(['payment_id' => $otherPaymentTerm->id]);

    $response = $this->getJson(paymentDueTermRoute('index', $paymentTerm))
        ->assertOk();

    foreach ($response->json('data') as $item) {
        expect($item['payment_id'])->toBe($paymentTerm->id);
    }
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a payment due term', function () {
    actingAsPaymentDueTermApiUser(['update_account_payment::term']);

    $paymentTerm = PaymentTerm::factory()->create();
    $payload = paymentDueTermPayload();

    $this->postJson(paymentDueTermRoute('store', $paymentTerm), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Payment due term created successfully.')
        ->assertJsonPath('data.value', $payload['value'])
        ->assertJsonPath('data.nb_days', $payload['nb_days'])
        ->assertJsonStructure(['data' => PAYMENT_DUE_TERM_JSON_STRUCTURE]);

    $this->assertDatabaseHas('accounts_payment_due_terms', [
        'payment_id' => $paymentTerm->id,
        'value'      => $payload['value'],
        'nb_days'    => $payload['nb_days'],
    ]);
});

it('validates required fields when creating a payment due term', function (string $field) {
    actingAsPaymentDueTermApiUser(['update_account_payment::term']);

    $paymentTerm = PaymentTerm::factory()->create();
    $payload = paymentDueTermPayload();
    unset($payload[$field]);

    $this->postJson(paymentDueTermRoute('store', $paymentTerm), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(PAYMENT_DUE_TERM_REQUIRED_FIELDS);

it('returns 404 when creating a due term for a non-existent payment term', function () {
    actingAsPaymentDueTermApiUser(['update_account_payment::term']);

    $fakePaymentTerm = PaymentTerm::factory()->make(['id' => 999999]);

    $this->postJson(paymentDueTermRoute('store', $fakePaymentTerm), paymentDueTermPayload())
        ->assertNotFound();
});

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a payment due term for authorized users', function () {
    actingAsPaymentDueTermApiUser(['view_account_payment::term']);

    $paymentTerm = PaymentTerm::factory()->create();
    $dueTerm = PaymentDueTerm::factory()->create(['payment_id' => $paymentTerm->id]);

    $this->getJson(paymentDueTermRoute('show', $paymentTerm, $dueTerm))
        ->assertOk()
        ->assertJsonPath('data.id', $dueTerm->id)
        ->assertJsonStructure(['data' => PAYMENT_DUE_TERM_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent payment due term', function () {
    actingAsPaymentDueTermApiUser(['view_account_payment::term']);

    $paymentTerm = PaymentTerm::factory()->create();
    $fakeDueTerm = PaymentDueTerm::factory()->make(['id' => 999999]);

    $this->getJson(paymentDueTermRoute('show', $paymentTerm, $fakeDueTerm))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a payment due term', function () {
    actingAsPaymentDueTermApiUser(['update_account_payment::term']);

    $paymentTerm = PaymentTerm::factory()->create();
    $dueTerm = PaymentDueTerm::factory()->create(['payment_id' => $paymentTerm->id]);

    $this->patchJson(paymentDueTermRoute('update', $paymentTerm, $dueTerm), ['nb_days' => 60])
        ->assertOk()
        ->assertJsonPath('message', 'Payment due term updated successfully.')
        ->assertJsonPath('data.nb_days', 60);

    $this->assertDatabaseHas('accounts_payment_due_terms', [
        'id'      => $dueTerm->id,
        'nb_days' => 60,
    ]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('deletes a payment due term', function () {
    actingAsPaymentDueTermApiUser(['update_account_payment::term']);

    $paymentTerm = PaymentTerm::factory()->create();
    $dueTerm = PaymentDueTerm::factory()->create(['payment_id' => $paymentTerm->id]);

    $this->deleteJson(paymentDueTermRoute('destroy', $paymentTerm, $dueTerm))
        ->assertOk()
        ->assertJsonPath('message', 'Payment due term deleted successfully.');

    $this->assertDatabaseMissing('accounts_payment_due_terms', ['id' => $dueTerm->id]);
});
