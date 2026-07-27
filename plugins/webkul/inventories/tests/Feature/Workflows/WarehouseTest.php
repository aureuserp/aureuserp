<?php

use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\OperationType;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/InventoryHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    InventoryHelper::actingAsAdmin();
});

it('one step reception keeps only the receipt type active and archives storage and quality', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::ONE_STEP);

    expect(InventoryHelper::opTypeArchived($wh->in_type_id))->toBeFalse()
        ->and(InventoryHelper::opTypeArchived($wh->store_type_id))->toBeTrue()
        ->and(InventoryHelper::opTypeArchived($wh->qc_type_id))->toBeTrue()
        ->and(InventoryHelper::locationArchived($wh->input_stock_location_id))->toBeTrue()
        ->and(InventoryHelper::locationArchived($wh->qc_stock_location_id))->toBeTrue();

    expect(OperationType::find($wh->in_type_id)->destination_location_id)->toBe($wh->lot_stock_location_id);
});

it('two step reception activates storage, restores input, and routes receipt into input', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::TWO_STEPS);

    expect(InventoryHelper::opTypeArchived($wh->in_type_id))->toBeFalse()
        ->and(InventoryHelper::opTypeArchived($wh->store_type_id))->toBeFalse()
        ->and(InventoryHelper::opTypeArchived($wh->qc_type_id))->toBeTrue()
        ->and(InventoryHelper::locationArchived($wh->input_stock_location_id))->toBeFalse()
        ->and(InventoryHelper::locationArchived($wh->qc_stock_location_id))->toBeTrue();

    $in = OperationType::find($wh->in_type_id);
    $store = OperationType::find($wh->store_type_id);

    expect($in->destination_location_id)->toBe($wh->input_stock_location_id)
        ->and($store->source_location_id)->toBe($wh->input_stock_location_id)
        ->and($store->destination_location_id)->toBe($wh->lot_stock_location_id);
});

it('three step reception activates quality and routes receipt through input then quality then stock', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::THREE_STEPS);

    expect(InventoryHelper::opTypeArchived($wh->in_type_id))->toBeFalse()
        ->and(InventoryHelper::opTypeArchived($wh->qc_type_id))->toBeFalse()
        ->and(InventoryHelper::opTypeArchived($wh->store_type_id))->toBeFalse()
        ->and(InventoryHelper::locationArchived($wh->input_stock_location_id))->toBeFalse()
        ->and(InventoryHelper::locationArchived($wh->qc_stock_location_id))->toBeFalse();

    $in = OperationType::find($wh->in_type_id);
    $qc = OperationType::find($wh->qc_type_id);
    $store = OperationType::find($wh->store_type_id);

    expect($in->destination_location_id)->toBe($wh->input_stock_location_id)
        ->and($qc->source_location_id)->toBe($wh->input_stock_location_id)
        ->and($qc->destination_location_id)->toBe($wh->qc_stock_location_id)
        ->and($store->source_location_id)->toBe($wh->qc_stock_location_id)
        ->and($store->destination_location_id)->toBe($wh->lot_stock_location_id);
});

it('downgrading reception from three to one archives quality, storage, input and quality locations', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::THREE_STEPS);

    $wh->update(['reception_steps' => ReceptionStep::ONE_STEP]);
    $wh->refresh();

    expect(InventoryHelper::opTypeArchived($wh->in_type_id))->toBeFalse()
        ->and(InventoryHelper::opTypeArchived($wh->qc_type_id))->toBeTrue()
        ->and(InventoryHelper::opTypeArchived($wh->store_type_id))->toBeTrue()
        ->and(InventoryHelper::locationArchived($wh->input_stock_location_id))->toBeTrue()
        ->and(InventoryHelper::locationArchived($wh->qc_stock_location_id))->toBeTrue();

    expect(OperationType::find($wh->in_type_id)->destination_location_id)->toBe($wh->lot_stock_location_id);
});

it('upgrading reception from one to three restores quality and storage', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::ONE_STEP);

    expect(InventoryHelper::opTypeArchived($wh->qc_type_id))->toBeTrue();

    $wh->update(['reception_steps' => ReceptionStep::THREE_STEPS]);
    $wh->refresh();

    expect(InventoryHelper::opTypeArchived($wh->qc_type_id))->toBeFalse()
        ->and(InventoryHelper::opTypeArchived($wh->store_type_id))->toBeFalse()
        ->and(InventoryHelper::locationArchived($wh->input_stock_location_id))->toBeFalse()
        ->and(InventoryHelper::locationArchived($wh->qc_stock_location_id))->toBeFalse();
});

it('keeps the storage rule active only when reception has two or three steps', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::TWO_STEPS);

    expect(InventoryHelper::activeRuleExists($wh->input_stock_location_id, $wh->lot_stock_location_id, $wh->store_type_id))->toBeTrue();

    $wh->update(['reception_steps' => ReceptionStep::ONE_STEP]);
    $wh->refresh();

    expect(InventoryHelper::activeRuleExists($wh->input_stock_location_id, $wh->lot_stock_location_id, $wh->store_type_id))->toBeFalse();
});
it('one step delivery keeps only the delivery type active and archives pick and pack', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::ONE_STEP, DeliveryStep::ONE_STEP);

    expect(InventoryHelper::opTypeArchived($wh->out_type_id))->toBeFalse()
        ->and(InventoryHelper::opTypeArchived($wh->pick_type_id))->toBeTrue()
        ->and(InventoryHelper::opTypeArchived($wh->pack_type_id))->toBeTrue()
        ->and(InventoryHelper::locationArchived($wh->output_stock_location_id))->toBeTrue()
        ->and(InventoryHelper::locationArchived($wh->pack_stock_location_id))->toBeTrue();

    expect(OperationType::find($wh->out_type_id)->source_location_id)->toBe($wh->lot_stock_location_id);
});

it('two step delivery activates pick, restores output, and routes stock through output', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::ONE_STEP, DeliveryStep::TWO_STEPS);

    expect(InventoryHelper::opTypeArchived($wh->pick_type_id))->toBeFalse()
        ->and(InventoryHelper::opTypeArchived($wh->out_type_id))->toBeFalse()
        ->and(InventoryHelper::opTypeArchived($wh->pack_type_id))->toBeTrue()
        ->and(InventoryHelper::locationArchived($wh->output_stock_location_id))->toBeFalse()
        ->and(InventoryHelper::locationArchived($wh->pack_stock_location_id))->toBeTrue();

    $pick = OperationType::find($wh->pick_type_id);
    $out = OperationType::find($wh->out_type_id);

    expect($pick->source_location_id)->toBe($wh->lot_stock_location_id)
        ->and($pick->destination_location_id)->toBe($wh->output_stock_location_id)
        ->and($out->source_location_id)->toBe($wh->output_stock_location_id);
});

it('three step delivery activates pack and routes stock through pack then output then customer', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::ONE_STEP, DeliveryStep::THREE_STEPS);

    expect(InventoryHelper::opTypeArchived($wh->pick_type_id))->toBeFalse()
        ->and(InventoryHelper::opTypeArchived($wh->pack_type_id))->toBeFalse()
        ->and(InventoryHelper::opTypeArchived($wh->out_type_id))->toBeFalse()
        ->and(InventoryHelper::locationArchived($wh->output_stock_location_id))->toBeFalse()
        ->and(InventoryHelper::locationArchived($wh->pack_stock_location_id))->toBeFalse();

    $pick = OperationType::find($wh->pick_type_id);
    $pack = OperationType::find($wh->pack_type_id);
    $out = OperationType::find($wh->out_type_id);

    expect($pick->source_location_id)->toBe($wh->lot_stock_location_id)
        ->and($pick->destination_location_id)->toBe($wh->pack_stock_location_id)
        ->and($pack->source_location_id)->toBe($wh->pack_stock_location_id)
        ->and($pack->destination_location_id)->toBe($wh->output_stock_location_id)
        ->and($out->source_location_id)->toBe($wh->output_stock_location_id);
});

it('downgrading delivery from three to one archives pick, pack and their locations', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::ONE_STEP, DeliveryStep::THREE_STEPS);

    $wh->update(['delivery_steps' => DeliveryStep::ONE_STEP]);
    $wh->refresh();

    expect(InventoryHelper::opTypeArchived($wh->pick_type_id))->toBeTrue()
        ->and(InventoryHelper::opTypeArchived($wh->pack_type_id))->toBeTrue()
        ->and(InventoryHelper::locationArchived($wh->output_stock_location_id))->toBeTrue()
        ->and(InventoryHelper::locationArchived($wh->pack_stock_location_id))->toBeTrue();

    expect(OperationType::find($wh->out_type_id)->source_location_id)->toBe($wh->lot_stock_location_id);
});

it('upgrading delivery from one to three restores pick and pack', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::ONE_STEP, DeliveryStep::ONE_STEP);

    expect(InventoryHelper::opTypeArchived($wh->pick_type_id))->toBeTrue();

    $wh->update(['delivery_steps' => DeliveryStep::THREE_STEPS]);
    $wh->refresh();

    expect(InventoryHelper::opTypeArchived($wh->pick_type_id))->toBeFalse()
        ->and(InventoryHelper::opTypeArchived($wh->pack_type_id))->toBeFalse()
        ->and(InventoryHelper::locationArchived($wh->output_stock_location_id))->toBeFalse()
        ->and(InventoryHelper::locationArchived($wh->pack_stock_location_id))->toBeFalse();
});
it('activates cross dock only when both reception and delivery are multi step', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::TWO_STEPS, DeliveryStep::TWO_STEPS);

    expect(InventoryHelper::opTypeArchived($wh->xdock_type_id))->toBeFalse()
        ->and(InventoryHelper::routeArchived($wh->crossdock_route_id))->toBeFalse();
});

it('archives cross dock when reception is single step', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::ONE_STEP, DeliveryStep::TWO_STEPS);

    expect(InventoryHelper::opTypeArchived($wh->xdock_type_id))->toBeTrue()
        ->and(InventoryHelper::routeArchived($wh->crossdock_route_id))->toBeTrue();
});

it('archives cross dock when delivery is single step', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::TWO_STEPS, DeliveryStep::ONE_STEP);

    expect(InventoryHelper::opTypeArchived($wh->xdock_type_id))->toBeTrue()
        ->and(InventoryHelper::routeArchived($wh->crossdock_route_id))->toBeTrue();
});

it('archives cross dock when a multi step warehouse drops reception back to one step', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::TWO_STEPS, DeliveryStep::TWO_STEPS);

    expect(InventoryHelper::opTypeArchived($wh->xdock_type_id))->toBeFalse();

    $wh->update(['reception_steps' => ReceptionStep::ONE_STEP]);
    $wh->refresh();

    expect(InventoryHelper::opTypeArchived($wh->xdock_type_id))->toBeTrue()
        ->and(InventoryHelper::routeArchived($wh->crossdock_route_id))->toBeTrue();
});

it('restores cross dock when a single step warehouse becomes multi step on both sides', function () {
    $wh = InventoryHelper::warehouse(ReceptionStep::ONE_STEP, DeliveryStep::ONE_STEP);

    expect(InventoryHelper::opTypeArchived($wh->xdock_type_id))->toBeTrue();

    $wh->update([
        'reception_steps' => ReceptionStep::TWO_STEPS,
        'delivery_steps'  => DeliveryStep::TWO_STEPS,
    ]);
    $wh->refresh();

    expect(InventoryHelper::opTypeArchived($wh->xdock_type_id))->toBeFalse()
        ->and(InventoryHelper::routeArchived($wh->crossdock_route_id))->toBeFalse();
});

it('throws when converting a stock location that holds products into a view', function () {
    $wh = InventoryHelper::warehouse();
    $product = InventoryHelper::product();

    InventoryHelper::stockUp($product, $wh->lotStockLocation, 10);

    expect(fn () => $wh->lotStockLocation->update(['type' => LocationType::VIEW]))
        ->toThrow(Exception::class, 'type can not be changed to view');
});

it('throws when converting an internal location that holds stock', function () {
    $wh = InventoryHelper::warehouse();
    $product = InventoryHelper::product();
    $shelf = InventoryHelper::sublocation($wh->lotStockLocation, 'Shelf A');

    InventoryHelper::stockUp($product, $shelf, 10);

    expect(fn () => $shelf->update(['is_scrap' => true]))
        ->toThrow(Exception::class, "Internal locations having stock can't be converted");
});

it('throws when changing the company of a location', function () {
    $wh = InventoryHelper::warehouse();
    $otherCompany = Company::factory()->create();

    expect(fn () => $wh->lotStockLocation->update(['company_id' => $otherCompany->id]))
        ->toThrow(Exception::class, 'Changing the company of this record is forbidden');
});

it('throws when archiving a location that is used by a warehouse', function () {
    $wh = InventoryHelper::warehouse();

    expect(fn () => $wh->lotStockLocation->delete())
        ->toThrow(Exception::class, 'is used by warehouse');
});

it('throws when archiving a warehouse that has an ongoing operation', function () {
    $wh = InventoryHelper::warehouse();
    $product = InventoryHelper::product();

    $operation = InventoryHelper::receipt($wh, [[$product, 10]]);

    Inventory::confirmTransfer($operation);

    expect(fn () => $wh->delete())
        ->toThrow(Exception::class, 'ongoing operations');
});
