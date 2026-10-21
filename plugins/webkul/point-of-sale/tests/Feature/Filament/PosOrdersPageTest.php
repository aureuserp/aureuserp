<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Filament\Pos\Pages\Orders;
use Webkul\PointOfSale\Filament\Pos\Pages\Register;
use Webkul\PointOfSale\Filament\Pos\Pages\Terminal;
use Webkul\PointOfSale\Models\Order;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/PosHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    DB::table('plugins')->whereIn('name', ['inventories', 'accounts', 'point-of-sale'])->update([
        'is_installed' => true,
        'is_active'    => true,
        'updated_at'   => now(),
    ]);

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs();

    Filament::setCurrentPanel(Filament::getPanel('pos'));

    Filament::bootCurrentPanel();

    $this->warehouse = InventoryHelper::warehouse();
    $this->config = PosHelper::configWithCashMethod($this->warehouse);
    $this->cash = $this->config->paymentMethods->first();
    $this->product = InventoryHelper::product(['price' => 25.0]);

    DB::table('products_products')->where('id', $this->product->id)->update(['available_in_pos' => true]);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 50);
});

it('cancels a draft order from the orders page', function () {
    $session = PosHelper::openSession($this->warehouse);

    $order = Order::create([
        'config_id'  => $session->config_id,
        'session_id' => $session->id,
    ]);

    Livewire::test(Orders::class, ['session' => $session])
        ->set('selectedOrderId', $order->id)
        ->assertSee(__('point-of-sale::filament/pos/pages/orders.actions.cancel.label'))
        ->callAction('cancelOrder', arguments: ['order' => $order->id])
        ->assertNotified(__('point-of-sale::filament/pos/pages/orders.actions.cancel.notification.title'));

    expect($order->refresh()->state)->toBe(OrderState::CANCELED);
});

it('leaves a paid order alone when cancel is triggered from the orders page', function () {
    $session = PosHelper::openSession($this->warehouse);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $session->config,
        $session,
        [PosHelper::line($this->product->id, 1, 25.0)],
        [PosHelper::payment($session->config->paymentMethods->first(), 25.0)],
    ));

    Livewire::test(Orders::class, ['session' => $session])
        ->set('selectedOrderId', $order->id)
        ->callAction('cancelOrder', arguments: ['order' => $order->id]);

    expect($order->refresh()->state)->not->toBe(OrderState::CANCELED);
});

it('hands a refund to the till instead of saving a draft order', function () {
    $session = PosHelper::openSession($this->warehouse);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $session->config,
        $session,
        [PosHelper::line($this->product->id, 2, 25.0)],
        [PosHelper::payment($session->config->paymentMethods->first(), 50.0)],
    ));

    $line = $order->lines()->first();

    $before = Order::withoutGlobalScopes()->count();

    Livewire::test(Orders::class, ['session' => $session])
        ->set('selectedOrderId', $order->id)
        ->call('refundOrder', [$line->id => 1])
        ->assertRedirect(Register::getUrl(['config' => $session->config_id]));

    $pending = session(Terminal::pendingRefundKey($session));

    expect(Order::withoutGlobalScopes()->count())->toBe($before)
        ->and($pending['refunded_order_id'])->toBe($order->id)
        ->and($pending['lines'][0]['qty'])->toBe(-1.0)
        ->and($pending['lines'][0]['refunded_order_line_id'])->toBe($line->id);
});

it('shows the refund prompt while an order still has something to refund', function () {
    $session = PosHelper::openSession($this->warehouse);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $session->config,
        $session,
        [PosHelper::line($this->product->id, 2, 25.0)],
        [PosHelper::payment($session->config->paymentMethods->first(), 50.0)],
    ));

    PointOfSale::refundOrder($order, [$order->lines()->first()->id => 1]);

    Livewire::test(Orders::class, ['session' => $session])
        ->set('selectedOrderId', $order->id)
        ->assertSee(__('point-of-sale::filament/pos/pages/orders.refund.prompt'))
        ->assertSeeHtml('refundOrder(payload())');
});

it('hides the refund prompt once every line is refunded', function () {
    $session = PosHelper::openSession($this->warehouse);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $session->config,
        $session,
        [PosHelper::line($this->product->id, 2, 25.0)],
        [PosHelper::payment($session->config->paymentMethods->first(), 50.0)],
    ));

    PointOfSale::refundOrder($order, [$order->lines()->first()->id => 2]);

    Livewire::test(Orders::class, ['session' => $session])
        ->set('selectedOrderId', $order->id)
        ->assertDontSee(__('point-of-sale::filament/pos/pages/orders.refund.prompt'))
        ->assertDontSeeHtml('refundOrder(payload())');
});
