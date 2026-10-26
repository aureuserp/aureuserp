<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\MoveState as AccountMoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Payment;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Purchase\Enums\OrderState;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';
require_once __DIR__.'/../../Helpers/PurchaseHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('purchases');

    foreach (['inventories', 'purchases'] as $plugin) {
        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    PurchaseHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->product = PurchaseHelper::product();

    Model::preventSilentlyDiscardingAttributes();
});

afterEach(fn () => Model::preventSilentlyDiscardingAttributes(false));

it('confirms, receives in part, returns and backorders a purchase with strict model writes', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    $receipt = PurchaseHelper::partialReceive($order, 6);

    $backorder = InventoryHelper::backorderOf($receipt);

    $return = Inventory::createReturn($receipt->refresh(), [$receipt->moves->first()->id => 2]);

    expect($order->refresh()->state)->toBe(OrderState::PURCHASE)
        ->and($receipt->state)->toBe(OperationState::DONE)
        ->and((float) $backorder->moves->sum('product_uom_qty'))->toBe(4.0)
        ->and((float) $return->moves->sum('product_uom_qty'))->toBe(2.0);
});

it('bills and pays a purchase with strict model writes and keeps the payment memo', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    Inventory::completeTransfer($order->operations->first()->refresh());

    $bill = PurchaseHelper::postBill(PurchaseHelper::createBill($order->refresh()));

    $bill = AccountHelper::pay($bill, amount: (float) $bill->amount_total - 5, differenceHandling: 'reconcile');

    $payment = Payment::query()->latest('id')->firstOrFail();

    expect($bill->state)->toBe(AccountMoveState::POSTED)
        ->and($bill->payment_state)->toBe(PaymentState::PAID)
        ->and($payment->memo)->not->toBeEmpty()
        ->and($payment->move->reference)->toBe($payment->memo);
});

it('adds a chatter follower with strict model writes', function () {
    $order = PurchaseHelper::order();

    $partner = Partner::query()->findOrFail($order->partner_id);

    $follower = $order->addFollower($partner);

    expect($follower->partner_id)->toBe($partner->id)
        ->and($follower->followed_at)->not->toBeNull()
        ->and($order->isFollowedBy($partner))->toBeTrue();
});
