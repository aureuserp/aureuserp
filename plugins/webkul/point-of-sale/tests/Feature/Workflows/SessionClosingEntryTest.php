<?php

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\DocumentType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxPartition;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Exceptions\SessionHasDraftOrdersException;
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
    $this->product = InventoryHelper::product(['price' => 100.0, 'cost' => 60.0]);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 100);
});

it('posts one balanced closing entry for the session', function () {
    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    $move = Move::findOrFail($session->move_id);

    expect($move->move_type)->toBe(MoveType::ENTRY)
        ->and($move->state)->toBe(MoveState::POSTED)
        ->and($move->journal_id)->toBe($this->config->journal_id)
        ->and((float) $move->lines->sum('debit'))->toBe((float) $move->lines->sum('credit'));
});

it('credits the income account and debits the cash account', function () {
    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 2, 50.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    $move = Move::findOrFail($session->move_id);

    expect((float) $move->lines->sum('credit'))->toBe(100.0)
        ->and((float) $move->lines->sum('debit'))->toBe(100.0);
});

it('creates a tax line carrying its tax metadata', function () {
    $tax = PosHelper::taxWithAccounts(10.0);

    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0, ['tax_ids' => [$tax->id]])],
        [PosHelper::payment($this->cash, 110.0)],
    ));

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    $taxLines = Move::findOrFail($session->move_id)
        ->lines
        ->where('display_type', DisplayType::TAX);

    expect($taxLines)->toHaveCount(1)
        ->and((float) $taxLines->first()->credit)->toBe(10.0)
        ->and($taxLines->first()->tax_line_id)->toBe($tax->id);
});

it('merges two orders of the same product into one income line', function () {
    foreach ([1, 2] as $qty) {
        PointOfSale::syncOrder(PosHelper::orderPayload(
            $this->config,
            $this->session,
            [PosHelper::line($this->product->id, $qty, 50.0)],
            [PosHelper::payment($this->cash, 50.0 * $qty)],
        ));
    }

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    $creditLines = Move::findOrFail($session->move_id)
        ->lines
        ->where('credit', '>', 0);

    expect($creditLines)->toHaveCount(1)
        ->and((float) $creditLines->first()->credit)->toBe(150.0);
});

it('splits the sales lines by product when the terminal asks for it', function () {
    $this->config->update(['is_closing_entry_by_product' => true]);

    $other = InventoryHelper::product(['name' => 'Counter Mug', 'price' => 40.0, 'cost' => 20.0]);

    InventoryHelper::stockUp($other, $this->warehouse->lotStockLocation, 10);

    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [
            PosHelper::line($this->product->id, 2, 100.0),
            PosHelper::line($other->id, 3, 40.0),
        ],
        [PosHelper::payment($this->cash, 320.0)],
    ));

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    $salesLines = Move::findOrFail($session->move_id)
        ->lines
        ->where('credit', '>', 0)
        ->where('display_type', DisplayType::PRODUCT);

    expect($salesLines)->toHaveCount(2)
        ->and($salesLines->pluck('name')->sort()->values()->all())
        ->toBe(collect([$other->name, $this->product->name])->sort()->values()->all())
        ->and((float) $salesLines->firstWhere('name', $this->product->name)->credit)->toBe(200.0)
        ->and((float) $salesLines->firstWhere('name', $this->product->name)->quantity)->toBe(2.0)
        ->and((float) $salesLines->firstWhere('name', $other->name)->credit)->toBe(120.0)
        ->and((float) $salesLines->firstWhere('name', $other->name)->quantity)->toBe(3.0);
});

it('keeps one sales line per income account when closing by product is off', function () {
    $other = InventoryHelper::product(['name' => 'Counter Mug', 'price' => 40.0, 'cost' => 20.0]);

    InventoryHelper::stockUp($other, $this->warehouse->lotStockLocation, 10);

    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [
            PosHelper::line($this->product->id, 2, 100.0),
            PosHelper::line($other->id, 3, 40.0),
        ],
        [PosHelper::payment($this->cash, 320.0)],
    ));

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    $salesLines = Move::findOrFail($session->move_id)
        ->lines
        ->where('credit', '>', 0)
        ->where('display_type', DisplayType::PRODUCT);

    expect($salesLines)->toHaveCount(1)
        ->and((float) $salesLines->first()->credit)->toBe(320.0)
        ->and($salesLines->first()->quantity)->toBeNull();
});

it('flips the sale line to the debit side for a refund only session', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    PointOfSale::closeSessionWithAccounting($this->session->refresh());

    $refund = PointOfSale::refundOrder($order, [$order->lines()->first()->id => 1]);

    PointOfSale::syncOrder([
        'uuid'       => $refund->uuid,
        'config_id'  => $refund->config_id,
        'session_id' => $refund->session_id,
        'payments'   => [PosHelper::payment($this->cash, -100.0)],
    ]);

    $refundSession = PointOfSale::closeSessionWithAccounting($refund->refresh()->session);

    $move = Move::findOrFail($refundSession->move_id);

    expect((float) $move->lines->sum('debit'))->toBe((float) $move->lines->sum('credit'))
        ->and($move->lines->where('debit', '>', 0)->count())->toBeGreaterThan(0);
});

it('marks every paid order of the session as done', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 20.0)],
        [PosHelper::payment($this->cash, 20.0)],
    ));

    PointOfSale::closeSessionWithAccounting($this->session->refresh());

    expect($order->refresh()->state)->toBe(OrderState::DONE);
});

it('closes the session and stamps the move id', function () {
    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 30.0)],
        [PosHelper::payment($this->cash, 30.0)],
    ));

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    expect($session->state)->toBe(SessionState::CLOSED)
        ->and($session->move_id)->not->toBeNull();
});

it('posts no entry for a session without orders', function () {
    $session = PointOfSale::closeSessionWithAccounting($this->session);

    expect($session->state)->toBe(SessionState::CLOSED)
        ->and($session->move_id)->toBeNull();
});

it('refuses to open a terminal whose tax has no distribution account', function () {
    $tax = Tax::factory()->create([
        'name'       => 'Broken Tax',
        'amount'     => 5,
        'company_id' => PosHelper::company()->id,
    ]);

    TaxPartition::create([
        'tax_id'           => $tax->id,
        'document_type'    => DocumentType::INVOICE,
        'repartition_type' => RepartitionType::TAX,
        'factor_percent'   => 100,
        'company_id'       => PosHelper::company()->id,
    ]);

    $config = PosHelper::configWithCashMethod(InventoryHelper::warehouse());

    expect(fn () => PointOfSale::openSession($config))
        ->toThrow(PosConfigurationException::class);
});

it('posts a cost of goods sold entry when enabled', function () {
    $this->config->update([
        'enable_cogs'             => true,
        'stock_output_account_id' => PosHelper::expenseAccount()->id,
    ]);

    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 2, 100.0)],
        [PosHelper::payment($this->cash, 200.0)],
    ));

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    expect($session->cogs_move_id)->not->toBeNull();

    $cogsMove = Move::findOrFail($session->cogs_move_id);

    expect((float) $cogsMove->lines->sum('debit'))->toBe(120.0)
        ->and((float) $cogsMove->lines->sum('credit'))->toBe(120.0)
        ->and($cogsMove->lines->where('display_type', DisplayType::COGS)->count())->toBe(1);
});

it('posts no cost of goods sold entry when disabled', function () {
    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    expect($session->cogs_move_id)->toBeNull();
});

it('refuses to close a session that still has draft orders', function () {
    Order::create([
        'config_id'  => $this->config->id,
        'session_id' => $this->session->id,
    ]);

    expect(fn () => PointOfSale::closeSessionWithAccounting($this->session))
        ->toThrow(SessionHasDraftOrdersException::class);

    expect($this->session->refresh()->move_id)->toBeNull();
});

it('closes once the draft order has been cancelled', function () {
    $draft = Order::create([
        'config_id'  => $this->config->id,
        'session_id' => $this->session->id,
    ]);

    PointOfSale::cancelOrder($draft);

    $session = PointOfSale::closeSessionWithAccounting($this->session);

    expect($session->state)->toBe(SessionState::CLOSED);
});

it('posts its own balanced entry for a cash movement', function () {
    $movement = PointOfSale::cashIn($this->session, 40.0, 'Float top up');

    $move = Move::find($movement->move_id);

    expect($move)->not->toBeNull()
        ->and($move->move_type)->toBe(MoveType::ENTRY)
        ->and($move->state)->toBe(MoveState::POSTED)
        ->and((float) $move->lines->sum('debit'))->toBe(40.0)
        ->and((float) $move->lines->sum('credit'))->toBe(40.0);
});

it('debits the drawer on a cash in and credits it on a cash out', function () {
    $in = PointOfSale::cashIn($this->session, 40.0);

    $out = PointOfSale::cashOut($this->session, 15.0);

    $liquidityId = $this->session->cashJournal->default_account_id;

    $inLine = Move::find($in->move_id)->lines->firstWhere('account_id', $liquidityId);

    $outLine = Move::find($out->move_id)->lines->firstWhere('account_id', $liquidityId);

    expect((float) $inLine->debit)->toBe(40.0)
        ->and((float) $outLine->credit)->toBe(15.0);
});

it('keeps the closing entry balanced when the drawer took cash in and out', function () {
    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    PointOfSale::cashIn($this->session, 40.0);

    PointOfSale::cashOut($this->session, 15.0);

    $session = PointOfSale::closeSessionWithAccounting($this->session);

    $move = Move::find($session->move_id);

    expect((float) $move->lines->sum('debit'))->toBe((float) $move->lines->sum('credit'));
});

it('leaves cash movements out of the closing entry drawer line', function () {
    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    PointOfSale::cashIn($this->session, 40.0);

    $session = PointOfSale::closeSessionWithAccounting($this->session);

    $liquidityId = $session->cashJournal->default_account_id;

    $drawerLine = Move::find($session->move_id)->lines->firstWhere('account_id', $liquidityId);

    expect((float) $drawerLine->debit)->toBe(100.0);
});

it('refuses a cashier closing over the authorized difference', function () {
    $this->config->forceFill([
        'enable_maximum_difference' => true,
        'amount_authorized_diff'    => 5,
    ])->save();

    Gate::before(fn (): ?bool => false);

    expect(fn () => PointOfSale::closeSessionWithAccounting($this->session->refresh(), 100.0))
        ->toThrow(PosConfigurationException::class, Str::before(__('point-of-sale::system.session-closer.difference-exceeded'), ':'));

    expect($this->session->refresh()->state)->not->toBe(SessionState::CLOSED);
});

it('closes with the cash method journal when the session opened without one', function () {
    $this->session->forceFill([
        'cash_journal_id' => null,
    ])->save();

    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    $move = Move::findOrFail($session->move_id);

    $cashAccountId = $this->cash->journal->default_account_id;

    expect((float) $move->lines->where('account_id', $cashAccountId)->sum('debit'))->toBe(100.0)
        ->and((float) $move->lines->sum('debit'))->toBe((float) $move->lines->sum('credit'));
});
