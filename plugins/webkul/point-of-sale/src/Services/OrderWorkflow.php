<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Inventory\Models\Operation;
use Webkul\Partner\Enums\AddressType;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\StockUpdateMode;
use Webkul\PointOfSale\Events\OrderCanceled;
use Webkul\PointOfSale\Events\OrderDone;
use Webkul\PointOfSale\Events\OrderPaid;
use Webkul\PointOfSale\Exceptions\CustomerRequiredException;
use Webkul\PointOfSale\Exceptions\InsufficientPaymentException;
use Webkul\PointOfSale\Exceptions\OrderAlreadyPaidException;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\Payment;
use Webkul\PointOfSale\Models\PaymentMethod;
use Webkul\PointOfSale\Models\Session;
use Webkul\Support\Services\SequenceService;

class OrderWorkflow
{
    public function __construct(
        protected OrderCalculator $calculator,
        protected PickingGenerator $pickings,
        protected FiscalPositionResolver $fiscalPositions,
        protected PreparationRouter $preparation,
        protected SessionWorkflow $sessions,
        protected ShipLaterProcurementRequester $shipLater,
    ) {}

    public function assertCustomer(Order $order): void
    {
        $reason = $this->customerRequirement($order, settlementOnly: true);

        if ($reason === null) {
            return;
        }

        throw new CustomerRequiredException(
            __("point-of-sale::system.order-workflow.customer.{$reason}")
        );
    }

    public function customerRequirement(Order $order, bool $settlementOnly = false): ?string
    {
        if (filled($order->shipped_at)) {
            if (! $order->partner_id) {
                return 'required-to-ship';
            }

            if (! $order->partner?->addresses()->where('sub_type', AddressType::DELIVERY)->exists()) {
                return 'shipping-address-required';
            }
        }

        if ($order->partner_id) {
            return null;
        }

        if ($order->config?->enable_customer_required) {
            return 'required-by-terminal';
        }

        if ($order->is_to_invoice) {
            return 'required-to-invoice';
        }

        if ($settlementOnly) {
            return null;
        }

        $splitPayment = $order->payments->contains(
            fn ($payment): bool => (bool) $payment->paymentMethod?->is_split_transaction
        );

        return $splitPayment ? 'required-by-payment-method' : null;
    }

    public function markPaid(Order $order): Order
    {
        return DB::transaction(function () use ($order): Order {
            if ($order->state !== OrderState::DRAFT) {
                throw new OrderAlreadyPaidException(
                    __('point-of-sale::system.order-workflow.mark-paid.already-settled', ['order' => $order->reference])
                );
            }

            $order->loadMissing(['session', 'config', 'lines', 'payments']);

            $this->sessions->assertOpen($order->session);

            $this->assertCustomer($order);

            $order = $this->fiscalPositions->applyTo($order);

            $order = $this->calculator->recompute($order);

            $paid = $order->paidAmount();

            $total = (float) $order->amount_total;

            $isRefund = float_compare($total, 0, precisionDigits: 2) < 0;

            $covered = $isRefund
                ? float_compare($paid, $total, precisionDigits: 2) <= 0
                : float_compare($paid, $total, precisionDigits: 2) >= 0;

            if (! $covered) {
                throw new InsufficientPaymentException(
                    __('point-of-sale::system.order-workflow.mark-paid.insufficient-payment', [
                        'order' => $order->reference,
                    ])
                );
            }

            $change = $isRefund ? 0.0 : float_round($paid - $total, precisionDigits: 2);

            if (float_compare($change, 0, precisionDigits: 2) > 0) {
                if (! $this->cashMethodFor($order->config)) {
                    throw new PosConfigurationException(
                        __('point-of-sale::system.order-workflow.mark-paid.cash-method-required', [
                            'order' => $order->reference,
                        ])
                    );
                }

                $this->registerChange($order, $change);
            }

            $this->assignName($order);

            $operation = $this->generateStock($order);

            $settled = $order->settledAmount();

            $order->forceFill([
                'state'             => OrderState::PAID,
                'amount_paid'       => $settled,
                'amount_difference' => float_round($settled - $total, precisionDigits: 4),
                'amount_return'     => max($change, 0),
                'operation_id'      => $operation?->id ?? $order->operation_id,
                'confirmed_at'      => now(),
            ])->save();

            $this->refreshSessionTotals($order->session);

            $this->preparation->route($order);

            OrderPaid::dispatch($order);

            return $order->refresh();
        });
    }

    public function markDone(Order $order): Order
    {
        if ($order->state !== OrderState::PAID) {
            return $order;
        }

        $order->forceFill(['state' => OrderState::DONE])->save();

        OrderDone::dispatch($order);

        return $order->refresh();
    }

    public function cancel(Order $order): Order
    {
        return DB::transaction(function () use ($order): Order {
            if ($order->state !== OrderState::DRAFT) {
                throw new OrderAlreadyPaidException(
                    __('point-of-sale::system.order-workflow.cancel.not-draft', ['order' => $order->reference])
                );
            }

            $order->forceFill(['state' => OrderState::CANCELED])->save();

            $this->refreshRefundedQuantities($order);

            OrderCanceled::dispatch($order);

            return $order->refresh();
        });
    }

    protected function refreshRefundedQuantities(Order $order): void
    {
        $order->loadMissing('lines');

        $order->lines->each(fn (OrderLine $line) => $line->syncRefundedOrderLine());
    }

    protected function generateStock(Order $order): ?Operation
    {
        if ($order->shipped_at) {
            $this->shipLater->request($order);

            return null;
        }

        if ($order->session->stock_update_mode === StockUpdateMode::AT_CLOSING) {
            return null;
        }

        return $this->pickings->generateFor($order);
    }

    public function addTip(Order $order, float $amount): Order
    {
        return DB::transaction(function () use ($order, $amount): Order {
            $config = $order->config;

            if (! $config?->enable_tip || ! $config->tip_product_id) {
                throw new InsufficientPaymentException(
                    __('point-of-sale::system.order-workflow.tip.not-enabled')
                );
            }

            $order->loadMissing('lines');

            $line = $order->lines->firstWhere('product_id', $config->tip_product_id);

            if ($line) {
                $line->forceFill(['price_unit' => $amount, 'qty' => 1])->save();
            } else {
                OrderLine::create([
                    'order_id'   => $order->id,
                    'product_id' => $config->tip_product_id,
                    'qty'        => 1,
                    'price_unit' => $amount,
                ]);
            }

            $order->forceFill([
                'is_tipped'  => true,
                'tip_amount' => $amount,
            ])->save();

            return $this->calculator->recompute($order->refresh());
        });
    }

    public function registerChange(Order $order, float $change): ?Payment
    {
        $cashMethod = $this->cashMethodFor($order->config);

        if (! $cashMethod) {
            return null;
        }

        return Payment::create([
            'order_id'          => $order->id,
            'session_id'        => $order->session_id,
            'payment_method_id' => $cashMethod->id,
            'amount'            => -$change,
            'is_change'         => true,
        ]);
    }

    public function refreshSessionTotals(Session $session): Session
    {
        $orders = Order::withoutGlobalScopes()
            ->where('session_id', $session->id)
            ->whereIn('state', [OrderState::PAID, OrderState::DONE, OrderState::INVOICED])
            ->get();

        $cashMethodIds = PaymentMethod::withoutGlobalScopes()
            ->where('is_cash_count', true)
            ->pluck('id');

        $payments = Payment::withoutGlobalScopes()
            ->where('session_id', $session->id)
            ->whereIn('order_id', $orders->pluck('id'))
            ->get();

        $session->forceFill([
            'order_count'            => $orders->count(),
            'total_payments_amount'  => (float) $payments->where('is_change', false)->sum('amount'),
            'cash_transaction_total' => (float) $payments
                ->filter(fn (Payment $payment): bool => $cashMethodIds->contains($payment->payment_method_id))
                ->sum('amount'),
        ])->save();

        return $session->refresh();
    }

    protected function cashMethodFor(?Config $config): ?PaymentMethod
    {
        if (! $config) {
            return null;
        }

        return $config->paymentMethods->firstWhere('is_cash_count', true);
    }

    protected function assignName(Order $order): void
    {
        if (filled($order->name) || ! Schema::hasTable('sequences')) {
            return;
        }

        $config = $order->config;

        if (! $config) {
            return;
        }

        $order->name = $order->isRefund() && $order->refundedOrder?->name
            ? $order->refundedOrder->name.' REFUND'
            : SequenceService::nextFor($config, '', $order->company_id, $config->sequenceDefaults());
    }
}
