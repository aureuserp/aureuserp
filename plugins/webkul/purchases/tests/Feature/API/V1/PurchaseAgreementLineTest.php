<?php

use Webkul\Purchase\Models\Requisition;
use Webkul\Purchase\Models\RequisitionLine;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PURCHASE_AGREEMENT_LINE_JSON_STRUCTURE = [
    'id',
    'requisition_id',
    'product_id',
    'qty',
    'price_unit',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsPurchaseAgreementLineApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function purchaseAgreementLineRoute(string $action, mixed $agreement, mixed $line = null): string
{
    $name = "admin.api.v1.purchases.purchase-agreements.lines.{$action}";

    $parameters = ['purchase_agreement' => $agreement];

    if ($line !== null) {
        $parameters['line'] = $line;
    }

    return route($name, $parameters);
}

function createAgreementWithLines(int $lineCount = 2): Requisition
{
    $agreement = Requisition::factory()->create();

    RequisitionLine::factory()->count($lineCount)->create([
        'requisition_id' => $agreement->id,
        'company_id'     => $agreement->company_id,
    ]);

    return $agreement->refresh();
}

it('requires authentication to list purchase agreement lines', function () {
    $agreement = createAgreementWithLines();

    $this->getJson(purchaseAgreementLineRoute('index', $agreement->id))
        ->assertUnauthorized();
});

it('forbids listing purchase agreement lines without permission', function () {
    actingAsPurchaseAgreementLineApiUser();

    $agreement = createAgreementWithLines();

    $this->getJson(purchaseAgreementLineRoute('index', $agreement->id))
        ->assertForbidden();
});

it('lists purchase agreement lines for authorized users', function () {
    actingAsPurchaseAgreementLineApiUser(['view_purchase_purchase::agreement']);

    $agreement = createAgreementWithLines(2);

    $this->getJson(purchaseAgreementLineRoute('index', $agreement->id))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => ['*' => PURCHASE_AGREEMENT_LINE_JSON_STRUCTURE]]);
});

it('shows a purchase agreement line for authorized users', function () {
    actingAsPurchaseAgreementLineApiUser(['view_purchase_purchase::agreement']);

    $agreement = createAgreementWithLines(1);
    $line = $agreement->lines()->firstOrFail();

    $this->getJson(purchaseAgreementLineRoute('show', $agreement->id, $line->id))
        ->assertOk()
        ->assertJsonPath('data.id', $line->id)
        ->assertJsonPath('data.requisition_id', $agreement->id)
        ->assertJsonStructure(['data' => PURCHASE_AGREEMENT_LINE_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent purchase agreement when listing lines', function () {
    actingAsPurchaseAgreementLineApiUser(['view_purchase_purchase::agreement']);

    $this->getJson(purchaseAgreementLineRoute('index', 999999))
        ->assertNotFound();
});

it('returns 404 for a non-existent agreement line', function () {
    actingAsPurchaseAgreementLineApiUser(['view_purchase_purchase::agreement']);

    $agreement = createAgreementWithLines(1);

    $this->getJson(purchaseAgreementLineRoute('show', $agreement->id, 999999))
        ->assertNotFound();
});

it('filters purchase agreement lines by product id', function () {
    actingAsPurchaseAgreementLineApiUser(['view_purchase_purchase::agreement']);

    $agreement = createAgreementWithLines(2);
    $matchingLine = $agreement->lines()->firstOrFail();

    $response = $this->getJson(
        purchaseAgreementLineRoute('index', $agreement->id).'?filter[product_id]='.$matchingLine->product_id
    )->assertOk();

    $productIds = collect($response->json('data'))->pluck('product_id')->unique()->values()->all();

    expect($productIds)->toBe([$matchingLine->product_id]);
});

it('sorts purchase agreement lines by id descending', function () {
    actingAsPurchaseAgreementLineApiUser(['view_purchase_purchase::agreement']);

    $agreement = createAgreementWithLines(2);

    $response = $this->getJson(purchaseAgreementLineRoute('index', $agreement->id).'?sort=-id')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->values()->all();
    $sortedIds = collect($ids)->sortDesc()->values()->all();

    expect($ids)->toBe($sortedIds);
});

it('includes related product for purchase agreement lines', function () {
    actingAsPurchaseAgreementLineApiUser(['view_purchase_purchase::agreement']);

    $agreement = createAgreementWithLines(1);

    $this->getJson(purchaseAgreementLineRoute('index', $agreement->id).'?include=product')
        ->assertOk()
        ->assertJsonPath('data.0.product.id', fn ($id) => is_int($id));
});
