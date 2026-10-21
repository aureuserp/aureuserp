<?php

use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Move;
use Webkul\Partner\Models\Partner;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
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
    $this->config->update(['invoice_journal_id' => PosHelper::saleJournal()->id]);
    $this->cash = $this->config->paymentMethods->first();
    $this->product = InventoryHelper::product(['price' => 100.0]);
    $this->partner = Partner::factory()->create(['company_id' => PosHelper::company()->id]);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 50);
});

it('creates a posted customer invoice for an order', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
        ['partner_id' => $this->partner->id],
    ));

    $invoice = PointOfSale::invoiceOrder($order);

    expect($invoice->move_type)->toBe(MoveType::OUT_INVOICE)
        ->and($invoice->state)->toBe(MoveState::POSTED)
        ->and($invoice->journal_id)->toBe($this->config->invoice_journal_id)
        ->and($invoice->partner_id)->toBe($this->partner->id);
});

it('flags the order as invoiced', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
        ['partner_id' => $this->partner->id],
    ));

    PointOfSale::invoiceOrder($order);

    $order->refresh();

    expect($order->is_invoiced)->toBeTrue()
        ->and($order->account_move_id)->not->toBeNull()
        ->and($order->state)->toBe(OrderState::INVOICED);
});

it('links every order line to its invoice line', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 2, 100.0)],
        [PosHelper::payment($this->cash, 200.0)],
        ['partner_id' => $this->partner->id],
    ));

    PointOfSale::invoiceOrder($order);

    expect($order->refresh()->lines->every(fn ($line): bool => $line->account_move_line_id !== null))->toBeTrue();
});

it('refuses to invoice an order without a customer', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    expect(fn () => PointOfSale::invoiceOrder($order))
        ->toThrow(PosConfigurationException::class);
});

it('returns the same invoice when invoiced twice', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
        ['partner_id' => $this->partner->id],
    ));

    $first = PointOfSale::invoiceOrder($order);
    $second = PointOfSale::invoiceOrder($order->refresh());

    expect($second->id)->toBe($first->id)
        ->and(Move::query()->where('invoice_origin', $order->name)->count())->toBe(1);
});

it('keeps invoiced orders out of the closing entry revenue', function () {
    $invoiced = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
        ['partner_id' => $this->partner->id],
    ));

    PointOfSale::invoiceOrder($invoiced);

    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 40.0)],
        [PosHelper::payment($this->cash, 40.0)],
    ));

    $session = PointOfSale::closeSessionWithAccounting($this->session->refresh());

    $move = Move::findOrFail($session->move_id);

    $income = PosHelper::incomeAccount();

    expect((float) $move->lines->sum('credit'))->toBe((float) $move->lines->sum('debit'))
        ->and((float) $move->lines->where('account_id', $income->id)->sum('credit'))->toBe(40.0);
});

it('settles the invoice of an invoiced order when the session closes', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
        ['partner_id' => $this->partner->id],
    ));

    $invoice = PointOfSale::invoiceOrder($order);

    PointOfSale::closeSessionWithAccounting($this->session->refresh(), 100.0);

    $receivable = $invoice->refresh()->lines
        ->first(fn ($line): bool => $line->account?->account_type === AccountType::ASSET_RECEIVABLE);

    expect($invoice->payment_state)->toBeIn([PaymentState::PAID, PaymentState::IN_PAYMENT])
        ->and((float) $receivable->amount_residual)->toBe(0.0);
});

it('settles two invoices of the same customer when the session closes', function () {
    $invoices = collect([100.0, 60.0])->map(function (float $price) {
        $order = PointOfSale::syncOrder(PosHelper::orderPayload(
            $this->config->refresh(),
            $this->session,
            [PosHelper::line($this->product->id, 1, $price)],
            [PosHelper::payment($this->cash, $price)],
            ['partner_id' => $this->partner->id],
        ));

        return PointOfSale::invoiceOrder($order);
    });

    PointOfSale::closeSessionWithAccounting($this->session->refresh(), 160.0);

    $invoices->each(fn ($invoice) => expect($invoice->refresh()->payment_state)
        ->toBeIn([PaymentState::PAID, PaymentState::IN_PAYMENT]));
});

it('invoices a shared draft when the paying till asks for an invoice', function () {
    $line = PosHelper::line($this->product->id, 1, 100.0);

    $draft = PosHelper::orderPayload($this->config->refresh(), $this->session, [$line], [], ['partner_id' => $this->partner->id]);

    unset($draft['payments']);

    PointOfSale::saveDraftOrders([$draft]);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [$line],
        [PosHelper::payment($this->cash, 100.0)],
        [
            'uuid'          => $draft['uuid'],
            'partner_id'    => $this->partner->id,
            'is_to_invoice' => true,
            'email'         => 'guest@example.com',
        ],
    ))->refresh();

    expect($order->is_to_invoice)->toBeTrue()
        ->and($order->is_invoiced)->toBeTrue()
        ->and($order->state)->toBe(OrderState::INVOICED)
        ->and($order->email)->toBe('guest@example.com');
});
