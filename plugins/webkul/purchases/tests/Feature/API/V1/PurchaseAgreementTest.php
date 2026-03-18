<?php

use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Enums\RequisitionType;
use Webkul\Purchase\Models\Requisition;
use Webkul\Purchase\Models\RequisitionLine;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PURCHASE_AGREEMENT_JSON_STRUCTURE = [
    'id',
    'partner_id',
    'company_id',
    'currency_id',
    'type',
    'state',
];

const PURCHASE_AGREEMENT_REQUIRED_FIELDS = [
    'partner_id',
    'type',
    'currency_id',
    'company_id',
    'lines',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsPurchaseAgreementApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function purchaseAgreementRoute(string $action, mixed $agreement = null): string
{
    $name = "admin.api.v1.purchases.purchase-agreements.{$action}";

    return $agreement ? route($name, $agreement) : route($name);
}

function makeAgreementLinePayload(array $overrides = []): array
{
    $product = Product::factory()->create(['is_configurable' => false]);

    return array_merge([
        'product_id' => $product->id,
        'qty'        => 10,
        'price_unit' => 50.00,
    ], $overrides);
}

function purchaseAgreementPayload(int $lineCount = 1, array $overrides = []): array
{
    $currency = Currency::first() ?? Currency::factory()->create();
    $company = Company::factory()->create(['currency_id' => $currency->id]);
    $partner = Partner::factory()->create();

    $payload = [
        'partner_id'  => $partner->id,
        'type'        => RequisitionType::BLANKET_ORDER->value,
        'currency_id' => $currency->id,
        'company_id'  => $company->id,
        'starts_at'   => now()->addDay()->format('Y-m-d'),
        'lines'       => collect(range(1, $lineCount))
            ->map(fn () => makeAgreementLinePayload())
            ->all(),
    ];

    return array_replace_recursive($payload, $overrides);
}

// ── Authentication ─────────────────────────────────────────────────────────────

it('requires authentication to list purchase agreements', function () {
    $this->getJson(purchaseAgreementRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a purchase agreement', function () {
    $this->postJson(purchaseAgreementRoute('store'), [])
        ->assertUnauthorized();
});

// ── Authorization ──────────────────────────────────────────────────────────────

it('forbids listing purchase agreements without permission', function () {
    actingAsPurchaseAgreementApiUser();

    $this->getJson(purchaseAgreementRoute('index'))
        ->assertForbidden();
});

it('forbids creating a purchase agreement without permission', function () {
    actingAsPurchaseAgreementApiUser();

    $this->postJson(purchaseAgreementRoute('store'), purchaseAgreementPayload())
        ->assertForbidden();
});

it('forbids updating a purchase agreement without permission', function () {
    actingAsPurchaseAgreementApiUser();

    $agreement = Requisition::factory()->create();

    $this->patchJson(purchaseAgreementRoute('update', $agreement), [])
        ->assertForbidden();
});

it('forbids deleting a purchase agreement without permission', function () {
    actingAsPurchaseAgreementApiUser();

    $agreement = Requisition::factory()->create();

    $this->deleteJson(purchaseAgreementRoute('destroy', $agreement))
        ->assertForbidden();
});

// ── Index ──────────────────────────────────────────────────────────────────────

it('lists purchase agreements for authorized users', function () {
    actingAsPurchaseAgreementApiUser(['view_any_purchase_purchase::agreement']);

    Requisition::factory()->count(3)->create();

    $this->getJson(purchaseAgreementRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

// ── Store ──────────────────────────────────────────────────────────────────────

it('creates a purchase agreement with lines', function () {
    actingAsPurchaseAgreementApiUser(['create_purchase_purchase::agreement']);

    $payload = purchaseAgreementPayload(lineCount: 2);
    $response = $this->postJson(purchaseAgreementRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Purchase agreement created successfully.')
        ->assertJsonPath('data.partner_id', $payload['partner_id'])
        ->assertJsonCount(2, 'data.lines')
        ->assertJsonStructure(['data' => PURCHASE_AGREEMENT_JSON_STRUCTURE]);

    $this->assertDatabaseHas('purchases_requisitions', [
        'id'         => $response->json('data.id'),
        'partner_id' => $payload['partner_id'],
        'company_id' => $payload['company_id'],
    ]);
});

it('validates required fields when creating a purchase agreement', function (string $field) {
    actingAsPurchaseAgreementApiUser(['create_purchase_purchase::agreement']);

    $payload = purchaseAgreementPayload();
    unset($payload[$field]);

    $this->postJson(purchaseAgreementRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(PURCHASE_AGREEMENT_REQUIRED_FIELDS);

// ── Show ───────────────────────────────────────────────────────────────────────

it('shows a purchase agreement for authorized users', function () {
    actingAsPurchaseAgreementApiUser(['view_purchase_purchase::agreement']);

    $agreement = Requisition::factory()->create();

    $this->getJson(purchaseAgreementRoute('show', $agreement).'?include=lines')
        ->assertOk()
        ->assertJsonPath('data.id', $agreement->id)
        ->assertJsonStructure(['data' => PURCHASE_AGREEMENT_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent purchase agreement', function () {
    actingAsPurchaseAgreementApiUser(['view_purchase_purchase::agreement']);

    $this->getJson(purchaseAgreementRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ─────────────────────────────────────────────────────────────────────

it('updates a purchase agreement and syncs lines', function () {
    actingAsPurchaseAgreementApiUser(['create_purchase_purchase::agreement', 'update_purchase_purchase::agreement']);

    $agreement = Requisition::factory()->create();

    [$lineToKeepId, $lineToDeleteId] = RequisitionLine::factory()->count(2)->create([
        'requisition_id' => $agreement->id,
        'company_id'     => $agreement->company_id,
    ])->pluck('id')->values()->all();

    $updatePayload = [
        'lines' => [
            [
                'id'         => $lineToKeepId,
                'product_id' => RequisitionLine::query()->find($lineToKeepId)->product_id,
                'qty'        => 20,
                'price_unit' => 75,
            ],
        ],
    ];

    $this->patchJson(purchaseAgreementRoute('update', $agreement), $updatePayload)
        ->assertOk()
        ->assertJsonPath('message', 'Purchase agreement updated successfully.')
        ->assertJsonCount(1, 'data.lines');

    $this->assertDatabaseHas('purchases_requisition_lines', [
        'id'         => $lineToKeepId,
        'qty'        => 20,
        'price_unit' => 75,
    ]);

    $this->assertDatabaseMissing('purchases_requisition_lines', ['id' => $lineToDeleteId]);
});

// ── Destroy ────────────────────────────────────────────────────────────────────

it('soft deletes a purchase agreement for authorized users', function () {
    actingAsPurchaseAgreementApiUser(['delete_purchase_purchase::agreement']);

    $agreement = Requisition::factory()->create();

    $this->deleteJson(purchaseAgreementRoute('destroy', $agreement))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase agreement deleted successfully.');

    $this->assertSoftDeleted('purchases_requisitions', ['id' => $agreement->id]);
});

// ── Restore ────────────────────────────────────────────────────────────────────

it('restores a soft-deleted purchase agreement', function () {
    actingAsPurchaseAgreementApiUser(['restore_purchase_purchase::agreement']);

    $agreement = Requisition::factory()->create();
    $agreement->delete();

    $this->postJson(route('admin.api.v1.purchases.purchase-agreements.restore', ['id' => $agreement->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase agreement restored successfully.');

    $this->assertNotSoftDeleted('purchases_requisitions', ['id' => $agreement->id]);
});

// ── Force Delete ───────────────────────────────────────────────────────────────

it('permanently deletes a purchase agreement', function () {
    actingAsPurchaseAgreementApiUser(['force_delete_purchase_purchase::agreement']);

    $agreement = Requisition::factory()->create();
    $agreement->delete();

    $this->deleteJson(route('admin.api.v1.purchases.purchase-agreements.force-destroy', ['id' => $agreement->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase agreement permanently deleted.');

    $this->assertDatabaseMissing('purchases_requisitions', ['id' => $agreement->id]);
});

// ── Confirm ────────────────────────────────────────────────────────────────────

it('confirms a draft purchase agreement', function () {
    actingAsPurchaseAgreementApiUser(['update_purchase_purchase::agreement']);

    $agreement = Requisition::factory()->draft()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-agreements.confirm', ['id' => $agreement->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase agreement confirmed successfully.')
        ->assertJsonPath('data.state', RequisitionState::CONFIRMED->value);
});

it('rejects confirming a non-draft purchase agreement', function (RequisitionState $state) {
    actingAsPurchaseAgreementApiUser(['update_purchase_purchase::agreement']);

    $agreement = Requisition::factory()->create(['state' => $state]);

    $this->postJson(route('admin.api.v1.purchases.purchase-agreements.confirm', ['id' => $agreement->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft purchase agreements can be confirmed.');
})->with([
    'confirmed agreement' => [RequisitionState::CONFIRMED],
    'closed agreement'    => [RequisitionState::CLOSED],
    'canceled agreement'  => [RequisitionState::CANCELED],
]);

// ── Close ──────────────────────────────────────────────────────────────────────

it('closes a confirmed purchase agreement', function () {
    actingAsPurchaseAgreementApiUser(['update_purchase_purchase::agreement']);

    $agreement = Requisition::factory()->confirmed()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-agreements.close', ['id' => $agreement->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase agreement closed successfully.')
        ->assertJsonPath('data.state', RequisitionState::CLOSED->value);
});

it('rejects closing a non-confirmed purchase agreement', function (RequisitionState $state) {
    actingAsPurchaseAgreementApiUser(['update_purchase_purchase::agreement']);

    $agreement = Requisition::factory()->create(['state' => $state]);

    $this->postJson(route('admin.api.v1.purchases.purchase-agreements.close', ['id' => $agreement->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only confirmed purchase agreements can be closed.');
})->with([
    'draft agreement'    => [RequisitionState::DRAFT],
    'canceled agreement' => [RequisitionState::CANCELED],
]);

// ── Cancel ─────────────────────────────────────────────────────────────────────

it('cancels a draft purchase agreement', function () {
    actingAsPurchaseAgreementApiUser(['update_purchase_purchase::agreement']);

    $agreement = Requisition::factory()->draft()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-agreements.cancel', ['id' => $agreement->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase agreement canceled successfully.')
        ->assertJsonPath('data.state', RequisitionState::CANCELED->value);
});

it('cancels a confirmed purchase agreement', function () {
    actingAsPurchaseAgreementApiUser(['update_purchase_purchase::agreement']);

    $agreement = Requisition::factory()->confirmed()->create();

    $this->postJson(route('admin.api.v1.purchases.purchase-agreements.cancel', ['id' => $agreement->id]))
        ->assertOk()
        ->assertJsonPath('message', 'Purchase agreement canceled successfully.')
        ->assertJsonPath('data.state', RequisitionState::CANCELED->value);
});

it('rejects canceling a closed or already-canceled purchase agreement', function (RequisitionState $state) {
    actingAsPurchaseAgreementApiUser(['update_purchase_purchase::agreement']);

    $agreement = Requisition::factory()->create(['state' => $state]);

    $this->postJson(route('admin.api.v1.purchases.purchase-agreements.cancel', ['id' => $agreement->id]))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft or confirmed purchase agreements can be canceled.');
})->with([
    'closed agreement'   => [RequisitionState::CLOSED],
    'canceled agreement' => [RequisitionState::CANCELED],
]);
