<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;

class PosInvoicer
{
    public function __construct(
        protected PosInvoicePayer $payer,
    ) {}

    public function invoice(Order $order): Move
    {
        return DB::transaction(function () use ($order): Move {
            if ($order->accountMove) {
                return $order->accountMove;
            }

            if (! $order->partner_id) {
                throw new PosConfigurationException(
                    __('point-of-sale::system.invoicer.customer-required', ['order' => $order->reference])
                );
            }

            if ($order->session?->state === SessionState::CLOSED) {
                throw new PosConfigurationException(
                    __('point-of-sale::system.invoicer.session-closed', ['order' => $order->reference])
                );
            }

            $config = $order->config;

            if (! $config->invoice_journal_id) {
                throw new PosConfigurationException(
                    __('point-of-sale::system.invoicer.journal-missing')
                );
            }

            $invoice = Move::create([
                'move_type'               => (float) $order->amount_total < 0 ? MoveType::OUT_REFUND : MoveType::OUT_INVOICE,
                'state'                   => MoveState::DRAFT,
                'journal_id'              => $config->invoice_journal_id,
                'company_id'              => $order->company_id,
                'currency_id'             => $order->currency_id,
                'partner_id'              => $order->partner_id,
                'invoice_origin'          => $order->name ?? $order->reference,
                'date'                    => $order->ordered_at,
                'invoice_date'            => $order->ordered_at,
                'fiscal_position_id'      => $order->fiscal_position_id,
                'invoice_payment_term_id' => $order->partner?->property_payment_term_id,
                'invoice_date_due'        => $order->partner?->property_payment_term_id ? null : $order->ordered_at,
            ]);

            $order->lines->each(function (OrderLine $line) use ($invoice): void {
                $moveLine = $invoice->lines()->create([
                    'name'        => $line->full_product_name ?? $line->product?->name,
                    'date'        => $invoice->date,
                    'quantity'    => abs((float) $line->qty),
                    'price_unit'  => (float) $line->price_unit,
                    'discount'    => (float) $line->discount,
                    'product_id'  => $line->product_id,
                    'uom_id'      => $line->uom_id,
                    'currency_id' => $invoice->currency_id,
                    'company_id'  => $invoice->company_id,
                ]);

                $moveLine->taxes()->sync($line->taxes->pluck('id')->all());

                $line->forceFill(['account_move_line_id' => $moveLine->id])->save();
            });

            AccountFacade::computeAccountMove($invoice);

            AccountFacade::confirmMove($invoice->refresh());

            $order->forceFill([
                'account_move_id' => $invoice->id,
                'is_invoiced'     => true,
                'is_to_invoice'   => true,
                'state'           => $order->state === OrderState::DRAFT ? $order->state : OrderState::INVOICED,
            ])->save();

            $this->payer->pay($invoice->refresh(), $order->refresh());

            return $invoice->refresh();
        });
    }
}
