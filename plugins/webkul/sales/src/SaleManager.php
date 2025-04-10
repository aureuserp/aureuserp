<?php

namespace Webkul\Sale;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webkul\Account\Enums as AccountEnums;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Facades\Tax;
use Webkul\Account\Models\Journal as AccountJournal;
use Webkul\Account\Models\Move;
use Webkul\Invoice\Enums\InvoicePolicy;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\InvoiceResource;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Mail\SaleOrderCancelQuotation;
use Webkul\Sale\Mail\SaleOrderQuotation;
use Webkul\Sale\Models\AdvancedPaymentInvoice;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Settings\InvoiceSettings;
use Webkul\Sale\Settings\QuotationAndOrderSettings;
use Webkul\Support\Services\EmailService;

class SaleManager
{
    public function __construct(
        protected QuotationAndOrderSettings $quotationAndOrderSettings,
        protected InvoiceSettings $invoiceSettings,
    ) {}

    public function sendQuotationOrOrderByEmail(Order $record, array $data = []): Order
    {
        $record = $this->sendByEmail($record, $data);

        $record = $this->computeSaleOrder($record);

        return $record;
    }

    public function lockAndUnlock(Order $record): Order
    {
        $record->update(['locked' => ! $record->locked]);

        $record = $this->computeSaleOrder($record);

        return $record;
    }

    public function confirmSaleOrder(Order $record): Order
    {
        $record->update([
            'state'          => Enums\OrderState::SALE,
            'invoice_status' => Enums\InvoiceStatus::TO_INVOICE,
            'locked'         => $this->quotationAndOrderSettings->enable_lock_confirm_sales,
        ]);

        $record = $this->computeSaleOrder($record);

        return $record;
    }

    public function backToQuotation(Order $record): Order
    {
        $record->update([
            'state'          => Enums\OrderState::DRAFT,
            'invoice_status' => Enums\InvoiceStatus::NO,
        ]);

        $record = $this->computeSaleOrder($record);

        return $record;
    }

    public function cancelSaleOrder(Order $record, array $data = []): Order
    {
        $record->update([
            'state'          => Enums\OrderState::CANCEL,
            'invoice_status' => Enums\InvoiceStatus::NO,
        ]);

        if (! empty($data)) {
            $this->cancelAndSendEmail($record, $data);
        }

        $record = $this->computeSaleOrder($record);

        return $record;
    }

    public function createInvoice(Order $record, array $data = [])
    {
        DB::transaction(function () use ($record, $data) {
            if ($data['advance_payment_method'] == AdvancedPayment::DELIVERED->value) {
                $this->createAccountMove($record);
            }

            $advancedPaymentInvoice = AdvancedPaymentInvoice::create([
                ...$data,
                'currency_id'          => $record->currency_id,
                'company_id'           => $record->company_id,
                'creator_id'           => Auth::id(),
                'deduct_down_payments' => true,
                'consolidated_billing' => true,
            ]);

            $advancedPaymentInvoice->orders()->attach($record->id);

            return $this->computeSaleOrder($record);
        });
    }

    /**
     * Compute the sale order.
     */
    public function computeSaleOrder(Order $record): Order
    {
        $record->amount_untaxed = 0;
        $record->amount_tax = 0;
        $record->amount_total = 0;

        foreach ($record->lines as $line) {
            $line->state = $record->state;
            $line->salesman_id = $record->user_id;
            $line->order_partner_id = $record->partner_id;
            $line->invoice_status = $record->invoice_status;

            $line = $this->computeSaleOrderLine($line);

            $record->amount_untaxed += $line->price_subtotal;
            $record->amount_tax += $line->price_tax;
            $record->amount_total += $line->price_total;
        }

        $record->save();

        return $record;
    }

    /**
     * Compute the sale order line.
     */
    public function computeSaleOrderLine(OrderLine $line): OrderLine
    {
        $qtyDelivered = $line->qty_delivered ?? 0;

        $line->qty_to_invoice = $qtyDelivered - $line->qty_invoiced;

        $subTotal = $line->price_unit * $line->product_qty;

        $discountAmount = 0;

        if ($line->discount > 0) {
            $discountAmount = $subTotal * ($line->discount / 100);

            $subTotal = $subTotal - $discountAmount;
        }

        $taxIds = $line->taxes->pluck('id')->toArray();

        [$subTotal, $taxAmount] = Tax::collect($taxIds, $subTotal, $line->product_qty);

        $line->price_subtotal = round($subTotal, 4);

        $line->price_tax = $taxAmount;

        $line->price_total = $subTotal + $taxAmount;

        $line->sort = $line->sort ?? OrderLine::max('sort') + 1;

        $line->technical_price_unit = $line->price_unit;

        $line->price_reduce_taxexcl = $line->price_unit - ($line->price_unit * ($line->discount / 100));

        $line->price_reduce_taxinc = round($line->price_reduce_taxexcl + ($line->price_reduce_taxexcl * ($line->taxes->sum('amount') / 100)), 2);

        $line->state = $line->order->state;

        $line = $this->computeDeliveryMethod($line);

        $line = $this->computeInvoiceStatus($line);

        $line = $this->computeQtyInvoiced($line);

        $line = $this->computeUntaxedAmountToInvoice($line);

        $line = $this->untaxedAmountToInvoiced($line);

        $line->save();

        return $line;
    }

    public function computeDeliveryMethod(OrderLine $line): OrderLine
    {
        if ($line->is_expense) {
            $line->qty_delivered_method = 'analytic';
        } else {
            $line->qty_delivered_method = 'manual';
        }

        return $line;
    }

    public function computeQtyInvoiced(OrderLine $line): OrderLine
    {
        $qtyInvoiced = 0.000;

        foreach ($line->invoiceLines as $invoiceLine) {
            $move = $invoiceLine->move;

            if (
                $move->state !== MoveState::CANCEL
                || $move->payment_state === PaymentState::INVOICING_LEGACY->value
            ) {
                $convertedQty = $invoiceLine->uom->computeQuantity($invoiceLine->quantity, $line->uom);

                if ($move->move_type === MoveType::OUT_INVOICE) {
                    $qtyInvoiced += $convertedQty;
                } elseif ($move->move_type === MoveType::OUT_REFUND) {
                    $qtyInvoiced -= $convertedQty;
                }
            }
        }

        $line->qty_invoiced = $qtyInvoiced;

        return $line;
    }

    public function computeInvoiceStatus(OrderLine $line): OrderLine
    {
        if ($line->state !== OrderState::SALE) {
            $line->invoice_status = InvoiceStatus::NO;

            return $line;
        }

        if (
            $line->is_downpayment
            && $line->untaxed_amount_to_invoice == 0
        ) {
            $line->invoice_status = InvoiceStatus::INVOICED;
        } elseif ($line->qty_to_invoice != 0) {
            $line->invoice_status = InvoiceStatus::TO_INVOICE;
        } elseif (
            $line->product->invoice_policy === InvoicePolicy::ORDER->value &&
            $line->product_uom_qty >= 0 &&
            $line->qty_delivered > $line->product_uom_qty
        ) {
            $line->invoice_status = InvoiceStatus::UP_SELLING;
        } elseif ($line->qty_invoiced >= $line->product_uom_qty) {
            $line->invoice_status = InvoiceStatus::INVOICED;
        } else {
            $line->invoice_status = InvoiceStatus::NO;
        }

        return $line;
    }

    public function computeUntaxedAmountToInvoice(OrderLine $line): OrderLine
    {
        if ($line->state !== OrderState::SALE) {
            $line->untaxed_amount_to_invoice = 0;

            return $line;
        }

        $priceSubtotal = 0;

        if ($line->product->invoice_policy === InvoicePolicy::DELIVERY->value) {
            $uomQtyToConsider = $line->qty_delivered;
        } else {
            $uomQtyToConsider = $line->product_uom_qty;
        }

        $discount = $line->discount ?? 0.0;
        $priceReduce = $line->price_unit * (1 - ($discount / 100.0));
        $priceSubtotal = $priceReduce * $uomQtyToConsider;

        $line->untaxed_amount_to_invoice = $priceSubtotal - $line->untaxed_amount_invoiced;

        return $line;
    }

    public function untaxedAmountToInvoiced(OrderLine $line): OrderLine
    {
        $amountInvoiced = 0.0;

        foreach ($line->invoiceLines as $invoiceLine) {
            $move = $invoiceLine->move;

            if (
                $move->state === MoveState::POSTED
                || $move->payment_state === PaymentState::INVOICING_LEGACY
            ) {
                if ($move->move_type === MoveType::OUT_INVOICE) {
                    $amountInvoiced += $line->price_subtotal;
                } elseif ($move->move_type === MoveType::OUT_REFUND) {
                    $amountInvoiced -= $line->price_subtotal;
                }
            }
        }

        $line->untaxed_amount_invoiced = $amountInvoiced;

        return $line;
    }

    public function sendByEmail(Order $record, array $data): Order
    {
        $partners = Partner::whereIn('id', $data['partners'])->get();

        foreach ($partners as $key => $partner) {
            $payload = [
                'record_name'    => $record->name,
                'model_name'     => OrderState::options()[$record->state],
                'subject'        => $data['subject'],
                'description'    => $data['description'],
                'to'             => [
                    'address' => $partner?->email,
                    'name'    => $partner?->name,
                ],
            ];

            app(EmailService::class)->send(
                mailClass: SaleOrderQuotation::class,
                view: $viewName = 'sales::mails.sale-order-quotation',
                payload: $payload,
                attachments: [
                    [
                        'path' => asset(Storage::url($data['file'])),
                        'name' => basename($data['file']),
                    ],
                ]
            );

            $record->addMessage([
                'from' => [
                    'company' => Auth::user()->defaultCompany->toArray(),
                ],
                'body' => view($viewName, compact('payload'))->render(),
                'type' => 'comment',
            ]);
        }

        $record->state = OrderState::SENT;
        $record->save();

        return $record;
    }

    public function cancelAndSendEmail(Order $record, array $data)
    {
        $partners = Partner::whereIn('id', $data['partners'])->get();

        foreach ($partners as $partner) {
            $payload = [
                'record_name'    => $record->name,
                'model_name'     => 'Quotation',
                'subject'        => $data['subject'],
                'description'    => $data['description'],
                'to'             => [
                    'address' => $partner?->email,
                    'name'    => $partner?->name,
                ],
            ];

            app(EmailService::class)->send(
                mailClass: SaleOrderCancelQuotation::class,
                view: $viewName = 'sales::mails.sale-order-cancel-quotation',
                payload: $payload,
            );

            $record->addMessage([
                'from' => [
                    'company' => Auth::user()->defaultCompany->toArray(),
                ],
                'body' => view($viewName, compact('payload'))->render(),
                'type' => 'comment',
            ]);
        }
    }

    private function prepareInvoice(Order $record)
    {
        return [
            'state'                        => AccountEnums\MoveState::DRAFT,
            'move_type'                    => AccountEnums\MoveType::OUT_INVOICE,
            'payment_state'                => AccountEnums\PaymentState::NOT_PAID,
            'invoice_origin'               => $record->name,
            'date'                         => now(),
            'invoice_date_due'             => now(),
            'invoice_currency_rate'        => 1,
            'journal_id'                   => AccountJournal::where('type', AccountEnums\JournalType::SALE->value)->first()?->id,
            'company_id'                   => $record->company_id,
            'currency_id'                  => $record->currency_id,
            'invoice_payment_term_id'      => $record->payment_term_id,
            'partner_id'                   => $record->partner_id,
            'commercial_partner_id'        => $record->partner_id,
            'partner_shipping_id'          => $record->partner->addresses->where('type', 'present')->first()?->id,
            'fiscal_position_id'           => $record->fiscal_position_id,
            'creator_id'                   => Auth::id(),
            'invoice_partner_display_name' => $record->partner->name,
        ];
    }

    private function prepareInvoiceLine(Move $accountMove, OrderLine $orderLine)
    {
        $productInvoicePolicy = $orderLine->product?->invoice_policy;
        $invoiceSetting = $this->invoiceSettings->invoice_policy;

        $quantity = ($productInvoicePolicy ?? $invoiceSetting) === InvoicePolicy::ORDER->value
            ? $orderLine->product_uom_qty
            : $orderLine->qty_to_invoice;

        return [
            'state'                  => AccountEnums\MoveState::DRAFT,
            'name'                   => $orderLine->name,
            'display_type'           => AccountEnums\DisplayType::PRODUCT,
            'date'                   => $accountMove->date,
            'creator_id'             => $accountMove?->creator_id,
            'parent_state'           => $accountMove->state,
            'quantity'               => $quantity,
            'price_unit'             => $orderLine->price_unit,
            'discount'               => $orderLine->discount,
            'journal_id'             => $accountMove->journal_id,
            'company_id'             => $accountMove->company_id,
            'currency_id'            => $accountMove->currency_id,
            'company_currency_id'    => $accountMove->currency_id,
            'partner_id'             => $accountMove->partner_id,
            'product_id'             => $orderLine->product_id,
            'uom_id'                 => $orderLine->product_uom_id,
            'purchase_order_line_id' => $orderLine->id,
            'debit'                  => $orderLine?->price_subtotal,
            'credit'                 => 0.00,
            'balance'                => $orderLine?->price_subtotal,
            'amount_currency'        => $orderLine?->price_subtotal,
        ];
    }

    private function createAccountMove(Order $record): Move
    {
        $accountMove = Move::create($this->prepareInvoice($record));

        $record->accountMoves()->attach($accountMove->id);

        foreach ($record->lines as $line) {
            $this->createAccountMoveLine($accountMove, $line);
        }

        InvoiceResource::collectTotals($accountMove);

        return $accountMove;
    }

    private function createAccountMoveLine(Move $accountMove, OrderLine $orderLine): void
    {
        $accountMoveLine = $accountMove->lines()->create($this->prepareInvoiceLine($accountMove, $orderLine));

        $orderLine->qty_invoiced += $orderLine->qty_to_invoice;

        $orderLine->save();

        $orderLine->invoiceLines()->sync($accountMoveLine->id);

        $accountMoveLine->taxes()->sync($orderLine->taxes->pluck('id'));
    }
}
