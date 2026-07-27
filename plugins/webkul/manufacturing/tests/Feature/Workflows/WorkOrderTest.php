<?php

use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Enums\OperationTimeMode;
use Webkul\Manufacturing\Enums\WorkCenterWorkingState;
use Webkul\Manufacturing\Enums\WorkOrderState;
use Webkul\Manufacturing\Models\Product as ManufacturingProduct;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/ManufacturingHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('manufacturing');

    foreach (['inventories', 'manufacturing'] as $plugin) {
        Illuminate\Support\Facades\DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Webkul\PluginManager\Package::$plugins = Webkul\PluginManager\Models\Plugin::all()->keyBy('name');

    Illuminate\Support\Facades\URL::resolveMissingNamedRoutesUsing(fn () => '#');

    ManufacturingHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->stock = $this->warehouse->lotStockLocation;

    $this->finished = ManufacturingHelper::product();
    $this->mfgFinished = ManufacturingProduct::findOrFail($this->finished->id);
    $this->component = ManufacturingHelper::product();

    $this->workCenter = ManufacturingHelper::workCenter([
        'setup_time'       => 10,
        'cleanup_time'     => 5,
        'time_efficiency'  => 100,
        'default_capacity' => 1,
        'costs_per_hour'   => 120,
    ]);
});

it('computes the expected duration from setup, cleanup, cycle and efficiency', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->component, 1]]);
    $operation = ManufacturingHelper::operation($bom, $this->workCenter, [
        'time_mode'         => OperationTimeMode::MANUAL,
        'manual_cycle_time' => 60,
    ]);

    expect($operation->getExpectedDuration($this->mfgFinished, 2))->toBe(135.0);
});

it('halves the effective cycle time when efficiency is doubled', function () {
    $workCenter = ManufacturingHelper::workCenter([
        'setup_time'       => 0,
        'cleanup_time'     => 0,
        'time_efficiency'  => 200,
        'default_capacity' => 1,
    ]);

    $bom = ManufacturingHelper::bom($this->finished, [[$this->component, 1]]);
    $operation = ManufacturingHelper::operation($bom, $workCenter, [
        'time_mode'         => OperationTimeMode::MANUAL,
        'manual_cycle_time' => 60,
    ]);

    expect($operation->getExpectedDuration($this->mfgFinished, 1))->toBe(30.0);
});

it('computes the expected cost from duration and hourly cost', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->component, 1]]);
    $operation = ManufacturingHelper::operation($bom, $this->workCenter, [
        'time_mode'         => OperationTimeMode::MANUAL,
        'manual_cycle_time' => 60,
    ]);

    expect($operation->getExpectedCost($this->mfgFinished, 2))->toBe(270.0);
});

it('computes the duration percentage against the expected duration', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->component, 1]]);
    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    $workOrder = ManufacturingHelper::workOrder($order, $this->workCenter, null, [
        'expected_duration' => 60,
        'quantity_produced' => 1,
    ]);

    ManufacturingHelper::logProductivity($workOrder, 45, 'productive');

    $workOrder->load('productivityLogs');
    $workOrder->computeDuration();

    expect((float) $workOrder->duration)->toBe(45.0)
        ->and((int) $workOrder->duration_percent)->toBe(25);
});

it('moves the work order and its manufacturing order into progress when started', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->component, 2]]);
    $operation = ManufacturingHelper::operation($bom, $this->workCenter);

    InventoryHelper::stockUp($this->component, $this->stock, 10);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);
    $workOrder = ManufacturingHelper::workOrder($order, $this->workCenter, $operation);

    ManufacturingHelper::confirm($order);
    ManufacturingHelper::startWorkOrder($workOrder);

    expect($workOrder->refresh()->state)->toBe(WorkOrderState::PROGRESS)
        ->and($order->refresh()->state)->toBe(ManufacturingOrderState::PROGRESS)
        ->and($workOrder->productivityLogs()->whereNull('finished_at')->count())->toBe(1);
});

it('closes the running timer when the work order is paused', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->component, 2]]);
    $operation = ManufacturingHelper::operation($bom, $this->workCenter);

    InventoryHelper::stockUp($this->component, $this->stock, 10);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);
    $workOrder = ManufacturingHelper::workOrder($order, $this->workCenter, $operation);

    ManufacturingHelper::confirm($order);
    ManufacturingHelper::startWorkOrder($workOrder);
    ManufacturingHelper::pauseWorkOrder($workOrder);

    expect($workOrder->refresh()->productivityLogs()->whereNull('finished_at')->count())->toBe(0);
});

it('finishes the work order, recording produced quantity and hourly cost', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->component, 2]]);
    $operation = ManufacturingHelper::operation($bom, $this->workCenter);

    InventoryHelper::stockUp($this->component, $this->stock, 10);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);
    $workOrder = ManufacturingHelper::workOrder($order, $this->workCenter, $operation);

    ManufacturingHelper::confirm($order);
    ManufacturingHelper::startWorkOrder($workOrder);
    ManufacturingHelper::finishWorkOrder($workOrder);

    expect($workOrder->refresh()->state)->toBe(WorkOrderState::DONE)
        ->and((float) $workOrder->quantity_produced)->toBe(5.0)
        ->and((float) $workOrder->costs_per_hour)->toBe(120.0);
});

it('finishes the work order when the manufacturing order is completed without starting it', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->component, 2]]);
    $operation = ManufacturingHelper::operation($bom, $this->workCenter);

    InventoryHelper::stockUp($this->component, $this->stock, 10);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);
    $workOrder = ManufacturingHelper::workOrder($order, $this->workCenter, $operation);

    ManufacturingHelper::confirm($order);
    ManufacturingHelper::produce($order, 5);

    expect($workOrder->refresh()->state)->toBe(WorkOrderState::DONE)
        ->and((float) $workOrder->quantity_produced)->toBe(5.0)
        ->and($order->refresh()->state)->toBe(ManufacturingOrderState::DONE);
});

it('creates and links a work order per bill-of-materials operation', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->component, 2]]);
    $firstOperation = ManufacturingHelper::operation($bom, $this->workCenter);
    $secondOperation = ManufacturingHelper::operation($bom, $this->workCenter);

    InventoryHelper::stockUp($this->component, $this->stock, 10);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);
    ManufacturingHelper::workOrder($order, $this->workCenter, $firstOperation);
    ManufacturingHelper::workOrder($order, $this->workCenter, $secondOperation);

    ManufacturingHelper::confirm($order);

    expect($order->refresh()->workOrders)->toHaveCount(2)
        ->and($order->workOrders->pluck('operation_id')->sort()->values()->all())
        ->toBe([$firstOperation->id, $secondOperation->id]);
});

it('refuses to start a work order on a blocked work center', function () {
    $blocked = ManufacturingHelper::workCenter(['working_state' => WorkCenterWorkingState::BLOCKED]);

    $bom = ManufacturingHelper::bom($this->finished, [[$this->component, 2]]);
    $operation = ManufacturingHelper::operation($bom, $blocked);

    InventoryHelper::stockUp($this->component, $this->stock, 10);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);
    $workOrder = ManufacturingHelper::workOrder($order, $blocked, $operation);

    ManufacturingHelper::confirm($order);

    expect(fn () => $workOrder->refresh()->start())
        ->toThrow(Exception::class, __('manufacturing::system.work-order.unblock-work-center'));
});

it('accumulates duration across multiple productivity logs', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->component, 1]]);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    $workOrder = ManufacturingHelper::workOrder($order, $this->workCenter, null, [
        'expected_duration' => 100,
        'quantity_produced' => 1,
    ]);

    ManufacturingHelper::logProductivity($workOrder, 30, 'productive');
    ManufacturingHelper::logProductivity($workOrder, 20, 'performance');

    $workOrder->load('productivityLogs');
    $workOrder->computeDuration();

    expect((float) $workOrder->duration)->toBe(50.0)
        ->and((int) $workOrder->duration_percent)->toBe(50);
});
