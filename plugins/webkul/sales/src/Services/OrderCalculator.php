<?php

namespace Webkul\Sale\Services;

use Webkul\Account\Enums as AccountEnums;
use Webkul\Account\Enums\InvoicePolicy;
use Webkul\Account\Facades\Tax;
use Webkul\Inventory\Enums as InventoryEnums;
use Webkul\PluginManager\Package;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderDeliveryStatus;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Enums\QtyDeliveredMethod;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Settings\InvoiceSettings;

class OrderCalculator
{
    public function __construct(protected InvoiceSettings $invoiceSettings) {}

    public function recompute(Order $record): Order
    {
        $record->amount_untaxed = 0;
        $record->amount_tax = 0;
        $record->amount_total = 0;

        foreach ($record->lines as $line) {
            $line->state = $record->state;
            $line->salesman_id = $record->user_id;
            $line->order_partner_id = $record->partner_id;
            $line->invoice_status = $record->invoice_status;

            $line = $this->recomputeLine($line);

            $record->amount_untaxed += $line->price_subtotal;
            $record->amount_tax += $line->price_tax;
            $record->amount_total += $line->price_total;
        }

        $record = $this->refreshDeliveryStatus($record);

        $record = $this->refreshInvoiceStatus($record);

        $record->save();

        $record->refresh();

        return $record;
    }

    public function recomputeLine(OrderLine $line): OrderLine
    {
        $line = $this->refreshQtyInvoiced($line);

        $line = $this->refreshQtyDelivered($line);

        $line = $this->refreshQtyToInvoice($line);

        $this->applyLineTotals($line);

        $line->sort ??= OrderLine::max('sort') + 1;

        $line->technical_price_unit = $line->price_unit;

        $line->price_reduce_taxexcl = $line->product_uom_qty ? round($line->price_subtotal / $line->product_uom_qty, 4) : 0.0;

        $line->price_reduce_taxinc = $line->product_uom_qty ? round($line->price_total / $line->product_uom_qty, 4) : 0.0;

        $line->state = $line->order->state;

        $line = $this->refreshDeliveryMethod($line);

        $line = $this->refreshLineInvoiceStatus($line);

        $line = $this->refreshQtyInvoiced($line);

        $line = $this->refreshUntaxedAmountToInvoice($line);

        $line = $this->refreshUntaxedAmountInvoiced($line);

        $line->save();

        return $line;
    }

    protected function applyLineTotals(OrderLine $line): void
    {
        $priceUnit = $line->discount > 0
            ? $line->price_unit * (1 - ($line->discount / 100))
            : $line->price_unit;

        if ($line->taxes->isEmpty()) {
            $subTotal = round($priceUnit * $line->product_qty, 4);

            $line->price_subtotal = $subTotal;
            $line->price_tax = 0;
            $line->price_total = $subTotal;

            return;
        }

        $taxResult = Tax::computeAll(
            $line->taxes,
            $priceUnit,
            $line->order->currency,
            $line->product_qty,
            $line->product,
            $line->order->partner,
        );

        $line->price_subtotal = round($taxResult['total_excluded'], 4);
        $line->price_tax = round($taxResult['total_included'] - $taxResult['total_excluded'], 4);
        $line->price_total = round($taxResult['total_included'], 4);
    }

    public function refreshQtyInvoiced(OrderLine $line): OrderLine
    {
        $qtyInvoiced = 0.000;

        foreach ($line->accountMoveLines as $accountMoveLine) {
            $counts = $accountMoveLine->move->state !== AccountEnums\MoveState::CANCEL
                || $accountMoveLine->move->payment_state === AccountEnums\PaymentState::INVOICING_LEGACY->value;

            if (! $counts) {
                continue;
            }

            $convertedQty = $accountMoveLine->uom->computeQuantity($accountMoveLine->quantity, $line->uom);

            if ($accountMoveLine->move->move_type === AccountEnums\MoveType::OUT_INVOICE) {
                $qtyInvoiced += $convertedQty;
            } elseif ($accountMoveLine->move->move_type === AccountEnums\MoveType::OUT_REFUND) {
                $qtyInvoiced -= $convertedQty;
            }
        }

        $line->qty_invoiced = $qtyInvoiced;

        return $line;
    }

    public function refreshQtyDelivered(OrderLine $line): OrderLine
    {
        if ($line->qty_delivered_method == QtyDeliveredMethod::MANUAL) {
            $line->qty_delivered ??= 0.0;
        }

        if ($line->qty_delivered_method != QtyDeliveredMethod::STOCK_MOVE) {
            return $line;
        }

        [$outgoingMoves, $incomingMoves] = app(ProcurementRequester::class)->outgoingAndIncomingMoves($line);

        $delivered = fn ($moves) => $moves
            ->filter(fn ($move) => $move->state == InventoryEnums\MoveState::DONE)
            ->sum(fn ($move) => $move->uom->computeQuantity($move->quantity, $line->uom, true, 'HALF-UP'));

        $line->qty_delivered = $delivered($outgoingMoves) - $delivered($incomingMoves);

        return $line;
    }

    public function refreshQtyToInvoice(OrderLine $line): OrderLine
    {
        if ($line->state != OrderState::SALE || $line->display_type) {
            $line->qty_to_invoice = 0.0;

            return $line;
        }

        $line->qty_to_invoice = $this->invoicePolicyFor($line) === InvoicePolicy::ORDER->value
            ? $line->product_uom_qty - $line->qty_invoiced
            : $line->qty_delivered - $line->qty_invoiced;

        return $line;
    }

    protected function invoicePolicyFor(OrderLine $line): string
    {
        return $line->product?->invoice_policy
            ?? $line->product?->parent?->invoice_policy
            ?? $this->invoiceSettings->invoice_policy->value;
    }

    public function refreshDeliveryStatus(Order $order): Order
    {
        if (! Package::isPluginInstalled('inventories')) {
            $order->delivery_status = OrderDeliveryStatus::NO;

            return $order;
        }

        $operations = $order->operations;

        $isCanceled = fn ($operation) => $operation->state == InventoryEnums\OperationState::CANCELED;

        $isSettled = fn ($operation) => in_array($operation->state, [
            InventoryEnums\OperationState::DONE,
            InventoryEnums\OperationState::CANCELED,
        ]);

        $hasDone = $operations->contains(fn ($operation) => $operation->state == InventoryEnums\OperationState::DONE);

        $order->delivery_status = match (true) {
            $operations->isEmpty() || $operations->every($isCanceled)                           => OrderDeliveryStatus::NO,
            $operations->every($isSettled)                                                      => OrderDeliveryStatus::FULL,
            $hasDone && $order->lines->contains(fn ($line) => (float) $line->qty_delivered > 0) => OrderDeliveryStatus::PARTIAL,
            $hasDone                                                                            => OrderDeliveryStatus::STARTED,
            default                                                                             => OrderDeliveryStatus::PENDING,
        };

        return $order;
    }

    public function refreshInvoiceStatus(Order $order): Order
    {
        if ($order->state != OrderState::SALE) {
            $order->invoice_status = InvoiceStatus::NO;

            return $order;
        }

        $lineHas = fn (array $statuses) => $order->lines->contains(fn ($line) => in_array($line->invoice_status, $statuses));

        $order->invoice_status = match (true) {
            $lineHas([InvoiceStatus::TO_INVOICE])                          => InvoiceStatus::TO_INVOICE,
            $lineHas([InvoiceStatus::INVOICED])                            => InvoiceStatus::INVOICED,
            $lineHas([InvoiceStatus::INVOICED, InvoiceStatus::UP_SELLING]) => InvoiceStatus::UP_SELLING,
            default                                                        => InvoiceStatus::NO,
        };

        return $order;
    }

    public function refreshDeliveryMethod(OrderLine $line): OrderLine
    {
        if ($line->qty_delivered_method) {
            return $line;
        }

        $line->qty_delivered_method = $line->is_expense ? 'analytic' : 'stock_move';

        return $line;
    }

    public function refreshLineInvoiceStatus(OrderLine $line): OrderLine
    {
        if ($line->state !== OrderState::SALE) {
            $line->invoice_status = InvoiceStatus::NO;

            return $line;
        }

        $policy = $this->invoicePolicyFor($line);

        if ($line->is_downpayment && $line->untaxed_amount_to_invoice == 0) {
            $line->invoice_status = InvoiceStatus::INVOICED;

            return $line;
        }

        $line->invoice_status = match ($policy) {
            InvoicePolicy::ORDER->value    => $this->orderPolicyInvoiceStatus($line),
            InvoicePolicy::DELIVERY->value => $this->deliveryPolicyInvoiceStatus($line),
            default                        => InvoiceStatus::NO,
        };

        return $line;
    }

    protected function orderPolicyInvoiceStatus(OrderLine $line): InvoiceStatus
    {
        return match (true) {
            $line->qty_invoiced >= $line->product_uom_qty  => InvoiceStatus::INVOICED,
            $line->qty_delivered > $line->product_uom_qty  => InvoiceStatus::UP_SELLING,
            default                                        => InvoiceStatus::TO_INVOICE,
        };
    }

    protected function deliveryPolicyInvoiceStatus(OrderLine $line): InvoiceStatus
    {
        return match (true) {
            $line->qty_invoiced >= $line->product_uom_qty => InvoiceStatus::INVOICED,
            $line->qty_to_invoice != 0
                || $line->qty_delivered == $line->product_uom_qty => InvoiceStatus::TO_INVOICE,
            default                                               => InvoiceStatus::NO,
        };
    }

    public function refreshUntaxedAmountToInvoice(OrderLine $line): OrderLine
    {
        if ($line->state !== OrderState::SALE) {
            $line->untaxed_amount_to_invoice = 0;

            return $line;
        }

        $quantity = $line->product->invoice_policy === InvoicePolicy::DELIVERY->value
            ? $line->qty_delivered
            : $line->product_uom_qty;

        $priceReduce = $line->price_unit * (1 - (($line->discount ?? 0.0) / 100.0));

        $line->untaxed_amount_to_invoice = ($priceReduce * $quantity) - $line->untaxed_amount_invoiced;

        return $line;
    }

    public function refreshUntaxedAmountInvoiced(OrderLine $line): OrderLine
    {
        $amountInvoiced = 0.0;

        foreach ($line->accountMoveLines as $accountMoveLine) {
            $counts = $accountMoveLine->move->state === AccountEnums\MoveState::POSTED
                || $accountMoveLine->move->payment_state === AccountEnums\PaymentState::INVOICING_LEGACY;

            if (! $counts) {
                continue;
            }

            if ($accountMoveLine->move->move_type === AccountEnums\MoveType::OUT_INVOICE) {
                $amountInvoiced += $line->price_subtotal;
            } elseif ($accountMoveLine->move->move_type === AccountEnums\MoveType::OUT_REFUND) {
                $amountInvoiced -= $line->price_subtotal;
            }
        }

        $line->untaxed_amount_invoiced = $amountInvoiced;

        return $line;
    }
}
