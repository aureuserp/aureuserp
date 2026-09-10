<?php

namespace Webkul\Purchase\Services;

use Webkul\Account\Enums as AccountEnums;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Inventory\Enums as InventoryEnums;
use Webkul\PluginManager\Package;
use Webkul\Purchase\Enums as PurchaseEnums;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Models\OrderLine;

class OrderCalculator
{
    protected const AMOUNT_PRECISION = 4;

    public function recompute(Order $record): Order
    {
        $record->untaxed_amount = 0;
        $record->tax_amount = 0;
        $record->total_amount = 0;
        $record->total_cc_amount = 0;
        $record->invoice_count = 0;

        foreach ($record->lines as $line) {
            $line->state = $record->state;

            $line = $this->recomputeLine($line);

            $record->untaxed_amount += $line->price_subtotal;
            $record->tax_amount += $line->price_tax;
            $record->total_amount += $line->price_total;
            $record->total_cc_amount += (float) $record->currency_rate
                ? $line->price_total / $record->currency_rate
                : $line->price_total;
        }

        $record = $this->refreshReceiptStatus($record);

        $record = $this->refreshInvoiceStatus($record);

        $record->save();

        return $record;
    }

    public function recomputeLine(OrderLine $line): OrderLine
    {
        $line = $this->refreshQtyBilled($line);

        $line = $this->refreshQtyReceived($line);

        if ($line->qty_received_method == PurchaseEnums\QtyReceivedMethod::MANUAL) {
            $line->qty_received_manual = $line->qty_received ?? 0;
        }

        $line->qty_to_invoice = $line->qty_received - $line->qty_invoiced;

        $this->applyLineTotals($line);

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

        $taxResult = TaxFacade::computeAll(
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

    public function refreshInvoiceStatus(Order $order): Order
    {
        if (! in_array($order->state, [PurchaseEnums\OrderState::PURCHASE, PurchaseEnums\OrderState::DONE])) {
            $order->invoice_status = PurchaseEnums\OrderInvoiceStatus::NO;

            return $order;
        }

        $isSettled = fn ($line) => abs($line->qty_to_invoice) < pow(10, -static::AMOUNT_PRECISION);

        $order->invoice_status = match (true) {
            $order->lines->contains(fn ($line) => ! $isSettled($line))           => PurchaseEnums\OrderInvoiceStatus::TO_INVOICED,
            $order->lines->every($isSettled) && $order->accountMoves()->exists() => PurchaseEnums\OrderInvoiceStatus::INVOICED,
            default                                                              => PurchaseEnums\OrderInvoiceStatus::NO,
        };

        return $order;
    }

    public function refreshReceiptStatus(Order $order): Order
    {
        if (! Package::isPluginInstalled('inventories')) {
            $order->receipt_status = PurchaseEnums\OrderReceiptStatus::NO;

            return $order;
        }

        $operations = $order->operations;

        $isCanceled = fn ($receipt) => $receipt->state == InventoryEnums\OperationState::CANCELED;

        $isSettled = fn ($receipt) => in_array($receipt->state, [
            InventoryEnums\OperationState::DONE,
            InventoryEnums\OperationState::CANCELED,
        ]);

        $order->receipt_status = match (true) {
            $operations->isEmpty() || $operations->every($isCanceled)                                      => PurchaseEnums\OrderReceiptStatus::NO,
            $operations->every($isSettled)                                                                 => PurchaseEnums\OrderReceiptStatus::FULL,
            $operations->contains(fn ($receipt) => $receipt->state == InventoryEnums\OperationState::DONE) => PurchaseEnums\OrderReceiptStatus::PARTIAL,
            default                                                                                        => PurchaseEnums\OrderReceiptStatus::PENDING,
        };

        return $order;
    }

    public function refreshQtyBilled(OrderLine $line): OrderLine
    {
        $qty = 0.0;

        foreach ($line->accountMoveLines as $accountMoveLine) {
            $counts = $accountMoveLine->move->state != AccountEnums\MoveState::CANCEL
                || $accountMoveLine->move->payment_state == AccountEnums\PaymentState::INVOICING_LEGACY;

            if (! $counts) {
                continue;
            }

            if ($accountMoveLine->move->move_type == AccountEnums\MoveType::IN_INVOICE) {
                $qty += $accountMoveLine->uom->computeQuantity($accountMoveLine->quantity, $line->uom);
            } elseif ($accountMoveLine->move->move_type == AccountEnums\MoveType::IN_REFUND) {
                $qty -= $accountMoveLine->uom->computeQuantity($accountMoveLine->quantity, $line->uom);
            }
        }

        $line->qty_invoiced = $qty;

        if (! in_array($line->order->state, [PurchaseEnums\OrderState::PURCHASE, PurchaseEnums\OrderState::DONE])) {
            $line->qty_to_invoice = 0;

            return $line;
        }

        $line->qty_to_invoice = $line->product->purchase_method == 'purchase'
            ? $line->product_qty - $line->qty_invoiced
            : $line->qty_received - $line->qty_invoiced;

        return $line;
    }

    public function refreshQtyReceived(OrderLine $line): OrderLine
    {
        $line->qty_received = 0.0;

        if ($line->qty_received_method == PurchaseEnums\QtyReceivedMethod::MANUAL) {
            $line->qty_received = $line->qty_received_manual ?? 0.0;
        }

        if ($line->qty_received_method != PurchaseEnums\QtyReceivedMethod::STOCK_MOVE) {
            return $line;
        }

        $total = 0.0;

        foreach ($line->inventoryMoves as $move) {
            if ($move->state !== InventoryEnums\MoveState::DONE) {
                continue;
            }

            $inLineUom = fn () => $move->uom->computeQuantity($move->quantity, $line->uom, true, 'HALF-UP');

            if ($move->isPurchaseReturn()) {
                if (! $move->originReturnedMove || $move->is_refund) {
                    $total -= $inLineUom();
                }
            } elseif ($this->isReturnedToStock($move) || $this->isReturnOfPurchaseReturn($move)) {
                continue;
            } else {
                $total += $inLineUom();
            }

            $line->qty_received = $total;
        }

        return $line;
    }

    protected function isReturnedToStock($move): bool
    {
        // The dropship is returned to stock, not to the supplier: the received quantity is already
        // counted on the purchase order even though nothing physically entered our stock.
        return $move->originReturnedMove
            && $move->originReturnedMove->isDropshipped()
            && ! $move->isDropshippedReturned();
    }

    protected function isReturnOfPurchaseReturn($move): bool
    {
        return $move->originReturnedMove
            && $move->originReturnedMove->isPurchaseReturn()
            && ! $move->is_refund;
    }
}
