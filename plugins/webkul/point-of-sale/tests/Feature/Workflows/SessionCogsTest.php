<?php

use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Models\Move;
use Webkul\PointOfSale\Facades\PointOfSale;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/PosHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    InventoryHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->session = PosHelper::openSession($this->warehouse);
    $this->config = $this->session->config;
    $this->config->update([
        'enable_cogs'             => true,
        'stock_output_account_id' => PosHelper::expenseAccount()->id,
    ]);
    $this->cash = $this->config->paymentMethods->first();
    $this->product = InventoryHelper::product(['price' => 100.0, 'cost' => 35.0]);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 40);
});

it('posts a balanced cost of goods sold entry', function () {
    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 3, 100.0)],
        [PosHelper::payment($this->cash, 300.0)],
    ));

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    $move = Move::findOrFail($session->cogs_move_id);

    expect((float) $move->lines->sum('debit'))->toBe(105.0)
        ->and((float) $move->lines->sum('credit'))->toBe(105.0);
});

it('marks the expense side with the cogs display type', function () {
    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    $cogsLines = Move::findOrFail($session->cogs_move_id)
        ->lines
        ->where('display_type', DisplayType::COGS);

    expect($cogsLines)->toHaveCount(1)
        ->and((float) $cogsLines->first()->debit)->toBe(35.0)
        ->and($cogsLines->first()->currency_id)->not->toBeNull();
});

it('uses the snapshotted cost and not the current product cost', function () {
    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 2, 100.0)],
        [PosHelper::payment($this->cash, 200.0)],
    ));

    $this->product->update(['cost' => 900.0]);

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    expect((float) Move::findOrFail($session->cogs_move_id)->lines->sum('debit'))->toBe(70.0);
});

it('skips zero cost products', function () {
    $free = InventoryHelper::product(['price' => 10.0, 'cost' => 0.0]);

    InventoryHelper::stockUp($free, $this->warehouse->lotStockLocation, 5);

    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($free->id, 1, 10.0)],
        [PosHelper::payment($this->cash, 10.0)],
    ));

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    expect($session->cogs_move_id)->toBeNull();
});

it('records the margin on the order', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 2, 100.0)],
        [PosHelper::payment($this->cash, 200.0)],
    ));

    expect((float) $order->total_cost)->toBe(70.0)
        ->and((float) $order->margin)->toBe(130.0)
        ->and((float) $order->margin_percent)->toBe(0.65);
});
