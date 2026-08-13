<?php

namespace Webkul\Purchase\Services;

use Webkul\Account\Enums as AccountEnums;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Purchase\Models\AccountMove;
use Webkul\Purchase\Models\Order;

class Biller
{
    public function createBill(Order $record): void
    {
        $accountMove = AccountMove::create([
            'move_type'               => $record->qty_to_invoice >= 0
                ? AccountEnums\MoveType::IN_INVOICE
                : AccountEnums\MoveType::IN_REFUND,
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
            $this->createBillLine($accountMove, $line);
        }

        AccountFacade::computeAccountMove($accountMove);
    }

    public function createBillLine($accountMove, $orderLine): void
    {
        $accountMoveLine = $accountMove->lines()->create([
            'state'                  => $accountMove->state,
            'name'                   => $orderLine->name,
            'date'                   => $accountMove->date,
            'parent_state'           => $accountMove->state,
            'quantity'               => abs($orderLine->qty_to_invoice),
            'price_unit'             => $orderLine->price_unit,
            'discount'               => $orderLine->discount,
            'company_id'             => $accountMove->company_id,
            'currency_id'            => $accountMove->currency_id,
            'company_currency_id'    => $accountMove->currency_id,
            'partner_id'             => $accountMove->partner_id,
            'product_id'             => $orderLine->product_id,
            'uom_id'                 => $orderLine->uom_id,
            'purchase_order_line_id' => $orderLine->id,
        ]);

        $accountMoveLine->taxes()->sync($orderLine->taxes->pluck('id'));
    }
}
