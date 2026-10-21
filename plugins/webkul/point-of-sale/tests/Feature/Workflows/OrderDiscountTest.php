<?php

use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\FiscalPositionTax;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Order;

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
    $this->cash = $this->config->paymentMethods->first();
    $this->product = InventoryHelper::product(['price' => 100.0]);
    $this->discountProduct = InventoryHelper::product(['price' => 0.0, 'name' => 'Discount']);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 40);
});

function posDraftOrder($session, $config, $product, float $qty, float $price): Order
{
    $order = Order::create([
        'session_id' => $session->id,
        'config_id'  => $config->id,
    ]);

    $order->lines()->create([
        'product_id' => $product->id,
        'qty'        => $qty,
        'price_unit' => $price,
    ]);

    return $order->refresh();
}

it('adds a negative discount line for the percentage', function () {
    $this->config->update([
        'enable_global_discount' => true,
        'discount_product_id'    => $this->discountProduct->id,
    ]);

    $order = posDraftOrder($this->session, $this->config->refresh(), $this->product, 2, 100.0);

    $order = PointOfSale::applyGlobalDiscount($order, 10);

    $discountLine = $order->lines()->where('product_id', $this->discountProduct->id)->first();

    expect($discountLine)->not->toBeNull()
        ->and((float) $discountLine->price_unit)->toBe(-20.0)
        ->and((float) $order->amount_total)->toBe(180.0);
});

it('replaces the discount line when applied twice', function () {
    $this->config->update([
        'enable_global_discount' => true,
        'discount_product_id'    => $this->discountProduct->id,
    ]);

    $order = posDraftOrder($this->session, $this->config->refresh(), $this->product, 1, 100.0);

    PointOfSale::applyGlobalDiscount($order, 10);

    $order = PointOfSale::applyGlobalDiscount($order->refresh(), 25);

    expect($order->lines()->where('product_id', $this->discountProduct->id)->count())->toBe(1)
        ->and((float) $order->amount_total)->toBe(75.0);
});

it('removes the discount line', function () {
    $this->config->update([
        'enable_global_discount' => true,
        'discount_product_id'    => $this->discountProduct->id,
    ]);

    $order = posDraftOrder($this->session, $this->config->refresh(), $this->product, 1, 100.0);

    PointOfSale::applyGlobalDiscount($order, 20);

    $order = PointOfSale::removeGlobalDiscount($order->refresh());

    expect($order->lines()->where('product_id', $this->discountProduct->id)->count())->toBe(0)
        ->and((float) $order->amount_total)->toBe(100.0);
});

it('refuses a global discount when the terminal has none configured', function () {
    $order = posDraftOrder($this->session, $this->config, $this->product, 1, 100.0);

    expect(fn () => PointOfSale::applyGlobalDiscount($order, 10))
        ->toThrow(PosConfigurationException::class);
});

it('maps a line tax through the fiscal position', function () {
    $source = PosHelper::taxWithAccounts(10.0);
    $destination = PosHelper::taxWithAccounts(5.0);

    $fiscalPosition = FiscalPosition::create([
        'name'       => 'Export',
        'company_id' => PosHelper::company()->id,
    ]);

    FiscalPositionTax::create([
        'fiscal_position_id' => $fiscalPosition->id,
        'tax_source_id'      => $source->id,
        'tax_destination_id' => $destination->id,
        'company_id'         => PosHelper::company()->id,
    ]);

    $this->config->update([
        'enable_fiscal_position' => true,
        'fiscal_position_id'     => $fiscalPosition->id,
    ]);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0, ['tax_ids' => [$source->id]])],
        [PosHelper::payment($this->cash, 105.0)],
    ));

    expect($order->fiscal_position_id)->toBe($fiscalPosition->id)
        ->and($order->lines()->first()->taxes->pluck('id')->all())->toBe([$source->id])
        ->and((float) $order->amount_tax)->toBe(5.0);
});

it('prefers the takeaway fiscal position on a takeaway order', function () {
    $eatIn = FiscalPosition::create(['name' => 'Eat in', 'company_id' => PosHelper::company()->id]);
    $takeaway = FiscalPosition::create(['name' => 'Takeaway', 'company_id' => PosHelper::company()->id]);

    $this->config->update([
        'is_restaurant'               => true,
        'enable_takeaway'             => true,
        'enable_fiscal_position'      => true,
        'fiscal_position_id'          => $eatIn->id,
        'takeaway_fiscal_position_id' => $takeaway->id,
    ]);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
        ['is_takeaway' => true],
    ));

    expect($order->fiscal_position_id)->toBe($takeaway->id);
});

it('keeps a synced global discount line with its negative price', function () {
    $this->config->update([
        'enable_global_discount' => true,
        'discount_product_id'    => $this->discountProduct->id,
    ]);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [
            PosHelper::line($this->product->id, 1, 100.0, ['tax_ids' => []]),
            PosHelper::line($this->discountProduct->id, 1, -10.0, ['tax_ids' => []]),
        ],
        [PosHelper::payment($this->cash, 90.0)],
    ));

    $line = $order->refresh()->lines->firstWhere('product_id', $this->discountProduct->id);

    expect((float) $line->price_unit)->toBe(-10.0)
        ->and((float) $order->amount_total)->toBe(90.0);
});

it('refuses to open a session while global discounts have no product', function () {
    $this->config->update([
        'enable_global_discount' => true,
        'discount_product_id'    => null,
    ]);

    expect(fn () => PointOfSale::assertSessionCanOpen($this->config->refresh()))
        ->toThrow(PosConfigurationException::class, __('point-of-sale::system.session-preflight.global-discount.product-missing'));
});
