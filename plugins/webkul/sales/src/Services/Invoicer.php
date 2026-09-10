<?php

namespace Webkul\Sale\Services;

use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums as AccountEnums;
use Webkul\Account\Enums\InvoicePolicy;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Sale\Models\AdvancedPaymentInvoice;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Settings\InvoiceSettings;

class Invoicer
{
    public function __construct(protected InvoiceSettings $invoiceSettings) {}

    public function invoiceOrder(Order $record, array $data = []): AdvancedPaymentInvoice
    {
        if ($data['advance_payment_method'] == AdvancedPayment::DELIVERED->value) {
            $this->createInvoice($record);
        }

        $invoice = AdvancedPaymentInvoice::create([
            ...$data,
            'currency_id'          => $record->currency_id,
            'company_id'           => $record->company_id,
            'creator_id'           => Auth::id(),
            'deduct_down_payments' => true,
            'consolidated_billing' => true,
        ]);

        $invoice->orders()->attach($record->id);

        return $invoice;
    }

    public function createInvoice(Order $record): AccountMove
    {
        $accountMove = AccountMove::create([
            'move_type'               => AccountEnums\MoveType::OUT_INVOICE,
            'invoice_origin'          => $record->name,
            'date'                    => now(),
            'company_id'              => $record->company_id,
            'currency_id'             => $record->currency_id,
            'invoice_payment_term_id' => $record->payment_term_id,
            'partner_id'              => $record->partner_id,
            'fiscal_position_id'      => $record->fiscal_position_id,
        ]);

        $record->accountMoves()->attach($accountMove->id);

        foreach ($record->lines as $line) {
            $this->createInvoiceLine($accountMove, $line);
        }

        return AccountFacade::computeAccountMove($accountMove);
    }

    public function createInvoiceLine(AccountMove $accountMove, OrderLine $orderLine): void
    {
        $policy = $orderLine->product?->invoice_policy ?? $this->invoiceSettings->invoice_policy->value;

        $quantity = $policy === InvoicePolicy::ORDER->value
            ? $orderLine->product_uom_qty
            : $orderLine->qty_to_invoice;

        $accountMoveLine = $accountMove->lines()->create([
            'name'         => $orderLine->name,
            'date'         => $accountMove->date,
            'creator_id'   => $accountMove?->creator_id,
            'parent_state' => $accountMove->state,
            'quantity'     => $quantity,
            'price_unit'   => $orderLine->price_unit,
            'discount'     => $orderLine->discount,
            'currency_id'  => $accountMove->currency_id,
            'product_id'   => $orderLine->product_id,
            'uom_id'       => $orderLine->product_uom_id,
        ]);

        $orderLine->accountMoveLines()->sync($accountMoveLine->id);

        $accountMoveLine->taxes()->sync($orderLine->taxes->pluck('id'));
    }
}
