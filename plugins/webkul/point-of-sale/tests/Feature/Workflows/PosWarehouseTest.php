<?php

use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Models\OperationType;
use Webkul\PointOfSale\Models\Warehouse;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/PosHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    InventoryHelper::actingAsAdmin();
});

it('provisions both point of sale operation types when a warehouse is created', function () {
    $warehouse = PosHelper::warehouse(InventoryHelper::warehouse());

    expect($warehouse->pos_type_id)->not->toBeNull()
        ->and($warehouse->pos_return_type_id)->not->toBeNull();

    $outgoing = OperationType::findOrFail($warehouse->pos_type_id);
    $incoming = OperationType::findOrFail($warehouse->pos_return_type_id);

    expect($outgoing->type)->toBe(OperationTypeEnum::OUTGOING)
        ->and($outgoing->sequence_code)->toBe('POS')
        ->and($outgoing->source_location_id)->toBe($warehouse->lot_stock_location_id)
        ->and($incoming->type)->toBe(OperationTypeEnum::INCOMING)
        ->and($incoming->sequence_code)->toBe('POSRET')
        ->and($incoming->destination_location_id)->toBe($warehouse->lot_stock_location_id);
});

it('points the outgoing type at the customer location and back', function () {
    $warehouse = PosHelper::warehouse(InventoryHelper::warehouse());

    $outgoing = OperationType::findOrFail($warehouse->pos_type_id);
    $incoming = OperationType::findOrFail($warehouse->pos_return_type_id);

    $destination = $outgoing->destinationLocation;

    expect($destination->type)->toBe(LocationType::CUSTOMER)
        ->and($incoming->source_location_id)->toBe($destination->id);
});

it('cross links the outgoing type to the return type', function () {
    $warehouse = PosHelper::warehouse(InventoryHelper::warehouse());

    $outgoing = OperationType::findOrFail($warehouse->pos_type_id);

    expect($outgoing->return_operation_type_id)->toBe($warehouse->pos_return_type_id);
});

it('enables lot capture on both point of sale operation types', function () {
    $warehouse = PosHelper::warehouse(InventoryHelper::warehouse());

    $types = OperationType::whereIn('id', [$warehouse->pos_type_id, $warehouse->pos_return_type_id])->get();

    expect($types)->toHaveCount(2);

    $types->each(function (OperationType $type) {
        expect($type->use_create_lots)->toBeTruthy()
            ->and($type->use_existing_lots)->toBeTruthy()
            ->and($type->create_backorder)->toBe(CreateBackorder::NEVER);
    });
});

it('keeps the point of sale flow single step on a multi step warehouse', function () {
    $warehouse = PosHelper::warehouse(InventoryHelper::warehouse(delivery: DeliveryStep::THREE_STEPS));

    $outgoing = OperationType::findOrFail($warehouse->pos_type_id);

    expect($outgoing->source_location_id)->toBe($warehouse->lot_stock_location_id)
        ->and($outgoing->destinationLocation->type)->toBe(LocationType::CUSTOMER);
});

it('does not provision a second set of operation types for the same warehouse', function () {
    $warehouse = PosHelper::warehouse(InventoryHelper::warehouse());

    $outgoingId = $warehouse->pos_type_id;
    $returnId = $warehouse->pos_return_type_id;

    $warehouse->handlePosWarehouseCreation();

    expect($warehouse->refresh()->pos_type_id)->toBe($outgoingId)
        ->and($warehouse->pos_return_type_id)->toBe($returnId);
});

it('numbers the first point of sale operation from the warehouse sequence', function () {
    $warehouse = PosHelper::warehouse(InventoryHelper::warehouse());

    $outgoing = OperationType::findOrFail($warehouse->pos_type_id);

    expect($outgoing->sequenceDefaults()['prefix'])->toBe($warehouse->code.'/POS/');
});

it('exposes the provisioned types through the warehouse relations', function () {
    $warehouse = PosHelper::warehouse(InventoryHelper::warehouse());

    expect($warehouse->posType)->toBeInstanceOf(OperationType::class)
        ->and($warehouse->posReturnType)->toBeInstanceOf(OperationType::class);
});

it('resolves every warehouse through the point of sale model', function () {
    InventoryHelper::warehouse();

    expect(Warehouse::query()->whereNotNull('pos_type_id')->count())->toBeGreaterThan(0);
});
