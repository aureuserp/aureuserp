<?php

use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Scrap;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const MOVE_LINE_JSON_STRUCTURE = [
    'id',
    'move_id',
    'operation_id',
    'product_id',
    'uom_id',
    'source_location_id',
    'destination_location_id',
    'state',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryMoveApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function inventoryMoveRoute(): string
{
    return route('admin.api.v1.inventories.moves.index');
}

function createMoveLineRecord(array $moveOverrides = [], array $lineOverrides = []): MoveLine
{
    $move = Move::factory()->create($moveOverrides);

    return MoveLine::factory()->create(array_merge([
        'move_id'                 => $move->id,
        'operation_id'            => $move->operation_id,
        'product_id'              => $move->product_id,
        'uom_id'                  => $move->uom_id,
        'source_location_id'      => $move->source_location_id,
        'destination_location_id' => $move->destination_location_id,
        'company_id'              => $move->company_id,
    ], $lineOverrides));
}

it('requires authentication to list moves', function () {
    $this->getJson(inventoryMoveRoute())
        ->assertUnauthorized();
});

it('forbids listing moves without permission', function () {
    actingAsInventoryMoveApiUser();

    $this->getJson(inventoryMoveRoute())
        ->assertForbidden();
});

it('lists move lines for authorized users', function () {
    actingAsInventoryMoveApiUser(['view_any_inventory_move']);

    createMoveLineRecord();
    createMoveLineRecord();

    $this->getJson(inventoryMoveRoute())
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => ['*' => MOVE_LINE_JSON_STRUCTURE]]);
});

it('filters move lines by product id', function () {
    actingAsInventoryMoveApiUser(['view_any_inventory_move']);

    $matching = createMoveLineRecord();
    createMoveLineRecord();

    $response = $this->getJson(inventoryMoveRoute().'?filter[product_id]='.$matching->product_id)
        ->assertOk();

    $productIds = collect($response->json('data'))
        ->pluck('product_id')
        ->unique()
        ->values()
        ->all();

    expect($productIds)->toBe([$matching->product_id]);
});

it('filters move lines by location id across source and destination', function () {
    actingAsInventoryMoveApiUser(['view_any_inventory_move']);

    $matching = createMoveLineRecord();
    createMoveLineRecord();

    $response = $this->getJson(inventoryMoveRoute().'?filter[location_id]='.$matching->source_location_id)
        ->assertOk();

    $returnedIds = collect($response->json('data'))->pluck('id');

    expect($returnedIds)->toContain($matching->id);
});

it('filters move lines by scrap id through move relation', function () {
    actingAsInventoryMoveApiUser(['view_any_inventory_move']);

    $scrap = Scrap::factory()->create();
    $matching = createMoveLineRecord(['scrap_id' => $scrap->id]);
    createMoveLineRecord();

    $response = $this->getJson(inventoryMoveRoute().'?filter[scrap_id]='.$scrap->id)
        ->assertOk();

    $returnedIds = collect($response->json('data'))->pluck('id');

    expect($returnedIds)->toContain($matching->id);
});

it('filters move lines by warehouse id through move relation', function () {
    actingAsInventoryMoveApiUser(['view_any_inventory_move']);

    $warehouse = Warehouse::query()->first() ?? Warehouse::factory()->create();
    $matching = createMoveLineRecord(['warehouse_id' => $warehouse->id]);
    createMoveLineRecord();

    $response = $this->getJson(inventoryMoveRoute().'?filter[warehouse_id]='.$warehouse->id)
        ->assertOk();

    $returnedIds = collect($response->json('data'))->pluck('id');

    expect($returnedIds)->toContain($matching->id);
});

it('supports includes on move listing', function () {
    actingAsInventoryMoveApiUser(['view_any_inventory_move']);

    createMoveLineRecord();

    $this->getJson(inventoryMoveRoute().'?include=move,operation,product')
        ->assertOk()
        ->assertJsonPath('data.0.move.id', fn ($id) => is_int($id))
        ->assertJsonPath('data.0.operation.id', fn ($id) => is_int($id))
        ->assertJsonPath('data.0.product.id', fn ($id) => is_int($id));
});
