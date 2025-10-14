<?php

namespace Webkul\Sale;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums as AccountEnums;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Facades\Tax;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Inventory\Enums as InventoryEnums;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Move as InventoryMove;
use Webkul\Inventory\Models\Operation as InventoryOperation;
use Webkul\Inventory\Models\Product as InventoryProduct;
use Webkul\Inventory\Models\Rule;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Invoice\Enums as InvoiceEnums;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderDeliveryStatus;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Enums\QtyDeliveredMethod;
use Webkul\Sale\Mail\SaleOrderCancelQuotation;
use Webkul\Sale\Mail\SaleOrderQuotation;
use Webkul\Sale\Models\AdvancedPaymentInvoice;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Settings\InvoiceSettings;
use Webkul\Sale\Settings\QuotationAndOrderSettings;
use Webkul\Support\Package;
use Webkul\Support\Services\EmailService;

class SaleManager
{
    public function __construct(
        protected QuotationAndOrderSettings $quotationAndOrderSettings,
        protected InvoiceSettings $invoiceSettings,
    ) {}

    public function sendQuotationOrOrderByEmail(Order $record, array $data = []): array
    {
        $result = $this->sendByEmail($record, $data);

        if (! empty($result['sent'])) {
            $record = $this->computeSaleOrder($record);
        }

        return $result;
    }

    public function lockAndUnlock(Order $record): Order
    {
        $record->update(['locked' => ! $record->locked]);

        $record = $this->computeSaleOrder($record);

        return $record;
    }

    public function confirmSaleOrder(Order $record): Order
    {
        $this->applyPullRules($record);

        $record->update([
            'state'          => OrderState::SALE,
            'invoice_status' => InvoiceStatus::TO_INVOICE,
            'locked'         => $this->quotationAndOrderSettings->enable_lock_confirm_sales,
        ]);

        $record = $this->computeSaleOrder($record);

        return $record;
    }

    public function backToQuotation(Order $record): Order
    {
        $record->update([
            'state'          => OrderState::DRAFT,
            'invoice_status' => InvoiceStatus::NO,
        ]);

        $record = $this->computeSaleOrder($record);

        return $record;
    }

    public function cancelSaleOrder(Order $record, array $data = []): Order
    {
        $record->update([
            'state'          => OrderState::CANCEL,
            'invoice_status' => InvoiceStatus::NO,
        ]);

        if (! empty($data)) {
            $this->cancelAndSendEmail($record, $data);
        }

        $record = $this->computeSaleOrder($record);

        $this->cancelInventoryOperation($record);

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

        $record = $this->computeWarehouseId($record);

        $record = $this->computeDeliveryStatus($record);

        $record = $this->computeInvoiceStatus($record);

        $record->save();

        $record->refresh();

        return $record;
    }

    /**
     * Compute the sale order line.
     */
    public function computeSaleOrderLine(OrderLine $line): OrderLine
    {
        $line = $this->computeQtyInvoiced($line);

        $line = $this->computeQtyDelivered($line);

        $line->qty_to_invoice = $line->qty_delivered - $line->qty_invoiced;

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

        $line->price_reduce_taxinc = round($line->price_reduce_taxexcl + ($line->price_reduce_taxexcl * ($line->taxes->sum('amount') / 100)), 2); // Todo:: This calculation is wrong

        $line->state = $line->order->state;

        $line = $this->computeOrderLineDeliveryMethod($line);

        $line = $this->computeOrderLineInvoiceStatus($line);

        $line = $this->computeQtyInvoiced($line);

        $line = $this->computeOrderLineUntaxedAmountToInvoice($line);

        $line = $this->untaxedOrderLineAmountToInvoiced($line);

        $line->save();

        return $line;
    }

    public function computeQtyInvoiced(OrderLine $line): OrderLine
    {
        $qtyInvoiced = 0.000;

        foreach ($line->accountMoveLines as $accountMoveLine) {
            if (
                $accountMoveLine->move->state !== AccountEnums\MoveState::CANCEL
                || $accountMoveLine->move->payment_state === AccountEnums\PaymentState::INVOICING_LEGACY->value
            ) {
                $convertedQty = $accountMoveLine->uom->computeQuantity($accountMoveLine->quantity, $line->uom);

                if ($accountMoveLine->move->move_type === AccountEnums\MoveType::OUT_INVOICE) {
                    $qtyInvoiced += $convertedQty;
                } elseif ($accountMoveLine->move->move_type === AccountEnums\MoveType::OUT_REFUND) {
                    $qtyInvoiced -= $convertedQty;
                }
            }
        }

        $line->qty_invoiced = $qtyInvoiced;

        return $line;
    }

    public function computeQtyDelivered(OrderLine $line): OrderLine
    {
        if ($line->qty_delivered_method == QtyDeliveredMethod::MANUAL) {
            $line->qty_delivered = $line->qty_delivered ?? 0.0;
        }

        if ($line->qty_delivered_method == QtyDeliveredMethod::STOCK_MOVE) {
            $qty = 0.0;

            [$outgoingMoves, $incomingMoves] = $this->getOutgoingIncomingMoves($line);

            foreach ($outgoingMoves as $move) {
                if ($move->state != InventoryEnums\MoveState::DONE) {
                    continue;
                }

                $qty += $move->uom->computeQuantity($move->quantity, $line->uom, true, 'HALF-UP');
            }

            foreach ($incomingMoves as $move) {
                if ($move->state != InventoryEnums\MoveState::DONE) {
                    continue;
                }

                $qty -= $move->uom->computeQuantity($move->quantity, $line->uom, true, 'HALF-UP');
            }

            $line->qty_delivered = $qty;
        }

        return $line;
    }

    public function computeWarehouseId(Order $order): Order
    {
        if (! Package::isPluginInstalled('inventories')) {
            return $order;
        }

        $order->warehouse_id = Warehouse::where('company_id', $order->company_id)->first()?->id;

        optional($order->lines)->each(function ($line) use ($order) {
            $line->warehouse_id = $order->warehouse_id;
            $line->save();
        });

        return $order;
    }

    public function computeDeliveryStatus(Order $order): Order
    {
        if (! Package::isPluginInstalled('inventories')) {
            $order->delivery_status = OrderDeliveryStatus::NO;

            return $order;
        }

        if ($order->operations->isEmpty() || $order->operations->every(function ($receipt) {
            return $receipt->state == InventoryEnums\OperationState::CANCELED;
        })) {
            $order->delivery_status = OrderDeliveryStatus::NO;
        } elseif ($order->operations->every(function ($receipt) {
            return in_array($receipt->state, [InventoryEnums\OperationState::DONE, InventoryEnums\OperationState::CANCELED]);
        })) {
            $order->delivery_status = OrderDeliveryStatus::FULL;
        } elseif ($order->operations->contains(function ($receipt) {
            return $receipt->state == InventoryEnums\OperationState::DONE;
        })) {
            $order->delivery_status = OrderDeliveryStatus::PARTIAL;
        } else {
            $order->delivery_status = OrderDeliveryStatus::PENDING;
        }

        return $order;
    }

    public function computeInvoiceStatus(Order $order): Order
    {
        if ($order->state != OrderState::SALE) {
            $order->invoice_status = InvoiceStatus::NO;

            return $order;
        }

        if ($order->lines->contains(function ($line) {
            return $line->invoice_status == InvoiceStatus::TO_INVOICE;
        })) {
            $order->invoice_status = InvoiceStatus::TO_INVOICE;
        } elseif ($order->lines->contains(function ($line) {
            return $line->invoice_status == InvoiceStatus::INVOICED;
        })) {
            $order->invoice_status = InvoiceStatus::INVOICED;
        } elseif ($order->lines->contains(function ($line) {
            return in_array($line->invoice_status, [InvoiceStatus::INVOICED, InvoiceStatus::UP_SELLING]);
        })) {
            $order->invoice_status = InvoiceStatus::UP_SELLING;
        } else {
            $order->invoice_status = InvoiceStatus::NO;
        }

        return $order;
    }

    public function computeOrderLineDeliveryMethod(OrderLine $line): OrderLine
    {
        if ($line->is_expense) {
            $line->qty_delivered_method = 'analytic';
        } else {
            $line->qty_delivered_method = 'stock_move';
        }

        return $line;
    }

    public function computeOrderLineInvoiceStatus(OrderLine $line): OrderLine
    {
        if ($line->state !== OrderState::SALE) {
            $line->invoice_status = InvoiceStatus::NO;

            return $line;
        }

        $policy = $line->product?->invoice_policy ?? $this->invoiceSettings->invoice_policy->value;

        if (
            $line->is_downpayment
            && $line->untaxed_amount_to_invoice == 0
        ) {
            $line->invoice_status = InvoiceStatus::INVOICED;
        } elseif ($policy === InvoiceEnums\InvoicePolicy::ORDER->value) {
            if ($line->qty_invoiced >= $line->product_uom_qty) {
                $line->invoice_status = InvoiceStatus::INVOICED;
            } elseif ($line->qty_delivered > $line->product_uom_qty) {
                $line->invoice_status = InvoiceStatus::UP_SELLING;
            } else {
                $line->invoice_status = InvoiceStatus::TO_INVOICE;
            }
        } elseif ($policy === InvoiceEnums\InvoicePolicy::DELIVERY->value) {
            if ($line->qty_invoiced >= $line->product_uom_qty) {
                $line->invoice_status = InvoiceStatus::INVOICED;
            } elseif ($line->qty_to_invoice != 0 || $line->qty_delivered == $line->product_uom_qty) {
                $line->invoice_status = InvoiceStatus::TO_INVOICE;
            } else {
                $line->invoice_status = InvoiceStatus::NO;
            }
        } else {
            $line->invoice_status = InvoiceStatus::NO;
        }
        return $line;
    }

    public function computeOrderLineUntaxedAmountToInvoice(OrderLine $line): OrderLine
    {
        if ($line->state !== OrderState::SALE) {
            $line->untaxed_amount_to_invoice = 0;

            return $line;
        }

        $priceSubtotal = 0;

        if ($line->product->invoice_policy === InvoiceEnums\InvoicePolicy::DELIVERY->value) {
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

    public function untaxedOrderLineAmountToInvoiced(OrderLine $line): OrderLine
    {
        $amountInvoiced = 0.0;

        foreach ($line->accountMoveLines as $accountMoveLine) {
            if (
                $accountMoveLine->move->state === AccountEnums\MoveState::POSTED
                || $accountMoveLine->move->payment_state === AccountEnums\PaymentState::INVOICING_LEGACY
            ) {
                if ($accountMoveLine->move->move_type === AccountEnums\MoveType::OUT_INVOICE) {
                    $amountInvoiced += $line->price_subtotal;
                } elseif ($accountMoveLine->move->move_type === AccountEnums\MoveType::OUT_REFUND) {
                    $amountInvoiced -= $line->price_subtotal;
                }
            }
        }

        $line->untaxed_amount_invoiced = $amountInvoiced;

        return $line;
    }

    public function sendByEmail(Order $record, array $data): array
    {
        $partners = Partner::whereIn('id', $data['partners'])->get();

        $sent = [];
        $failed = [];

        foreach ($partners as $partner) {
            if (empty($partner->email)) {
                $failed[$partner->name] = 'No email address';

                continue;
            }

            try {
                $payload = [
                    'record_name'    => $record->name,
                    'model_name'     => $record->state->getLabel(),
                    'subject'        => $data['subject'],
                    'description'    => $data['description'],
                    'to'             => [
                        'address' => $partner->email,
                        'name'    => $partner->name,
                    ],
                ];

                app(EmailService::class)->send(
                    mailClass: SaleOrderQuotation::class,
                    view: $viewName = 'sales::mails.sale-order-quotation',
                    payload: $payload,
                    attachments: [
                        [
                            'path' => $data['file'],
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

                $sent[] = $partner->name;

            } catch (\Exception $e) {
                $failed[$partner->name] = 'Email service error: '.$e->getMessage();
            }
        }

        if (! empty($sent) && $record->state === OrderState::DRAFT) {
            $record->state = OrderState::SENT;
            $record->save();
        }

        return [
            'sent'   => $sent,
            'failed' => $failed,
        ];
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

    public function getOutgoingIncomingMoves(OrderLine $orderLine, bool $strict = true)
    {
        $outgoingMoveIds = [];

        $incomingMoveIds = [];

        $moves = $orderLine->inventoryMoves->filter(function ($inventoryMove) use ($orderLine) {
            return $inventoryMove->state != InventoryEnums\MoveState::CANCELED
                && ! $inventoryMove->is_scraped
                && $orderLine->product_id == $inventoryMove->product_id;
        });

        $triggeringRuleIds = [];

        if ($moves->isNotEmpty() && ! $strict) {
            $sortedMoves = $moves->sortBy('id');

            $seenWarehouseIds = [];

            foreach ($sortedMoves as $move) {
                if (! in_array($move->warehouse->id, $seenWarehouseIds)) {
                    $triggeringRuleIds[] = $move->rule_id;

                    $seenWarehouseIds[] = $move->warehouse_id;
                }
            }
        }

        foreach ($moves as $move) {
            $isOutgoingStrict = $strict && $move->destinationLocation->type == InventoryEnums\LocationType::CUSTOMER;

            $isOutgoingNonStrict = ! $strict
                && in_array($move->rule_id, $triggeringRuleIds)
                && ($move->finalLocation ?? $move->destinationLocation->type) == InventoryEnums\LocationType::CUSTOMER;

            if ($isOutgoingStrict || $isOutgoingNonStrict) {
                if (
                    ! $move->origin_returned_move_id
                    || (
                        $move->origin_returned_move_id
                        && $move->to_refund
                    )
                ) {
                    $outgoingMoveIds[] = $move->id;
                }
            } elseif ($move->sourceLocation == InventoryEnums\LocationType::CUSTOMER && $move->is_refund) {
                $incomingMoveIds[] = $move->id;
            }
        }

        return [
            $moves->whereIn('id', $outgoingMoveIds),
            $moves->whereIn('id', $incomingMoveIds),
        ];
    }

    private function createAccountMove(Order $record): AccountMove
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
            $this->createAccountMoveLine($accountMove, $line);
        }

        $accountMove = AccountFacade::computeAccountMove($accountMove);

        return $accountMove;
    }

    private function createAccountMoveLine(AccountMove $accountMove, OrderLine $orderLine): void
    {
        $productInvoicePolicy = $orderLine->product?->invoice_policy;
        $invoiceSetting = $this->invoiceSettings->invoice_policy->value;

        $quantity = ($productInvoicePolicy ?? $invoiceSetting) === InvoiceEnums\InvoicePolicy::ORDER->value
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

    /**
     * Apply push rules for the operation.
     */
    public function applyPullRules(Order $record): void
    {
        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        $rulesToRun = [];

        foreach ($record->lines as $line) {
            $rule = $this->getPullRule($line);

            if (! $rule) {
                throw new Exception("No pull rule has been found to replenish \"{$line->name}\".\nVerify the routes configuration on the product.");
            }

            $rulesToRun[$line->id] = $rule;
        }

        $rules = [];

        foreach ($record->lines as $line) {
            $rule = $rulesToRun[$line->id];

            $pulledMove = $this->runPullRule($rule, $line);

            if (! isset($rules[$rule->id])) {
                $rules[$rule->id] = [
                    'rule'  => $rule,
                    'moves' => [$pulledMove],
                ];
            } else {
                $rules[$rule->id]['moves'][] = $pulledMove;
            }
        }

        foreach ($rules as $ruleData) {
            $this->createPullOperation($record, $ruleData['rule'], $ruleData['moves']);
        }
    }

    protected function cancelInventoryOperation(Order $record): void
    {
        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        if (! $record->operation) {
            return;
        }

        foreach ($record->operation->moves as $move) {
            $move->update([
                'state'    => InventoryEnums\MoveState::CANCELED,
                'quantity' => 0,
            ]);

            $move->lines()->delete();
        }

        InventoryFacade::computeTransferState($record->operation);
    }

    /**
     * Create a new operation based on a push rule and assign moves to it.
     */
    private function createPullOperation(Order $record, Rule $rule, array $moves): void
    {
        $newOperation = InventoryOperation::create([
            'state'                   => InventoryEnums\OperationState::DRAFT,
            'origin'                  => $record->name,
            'operation_type_id'       => $rule->operation_type_id,
            'source_location_id'      => $rule->source_location_id,
            'destination_location_id' => $rule->destination_location_id,
            'scheduled_at'            => now()->addDays($rule->delay),
            'company_id'              => $rule->company_id,
            'sale_order_id'           => $record->id,
            'user_id'                 => Auth::id(),
            'creator_id'              => Auth::id(),
        ]);

        foreach ($moves as $move) {
            $move->update([
                'operation_id' => $newOperation->id,
                'reference'    => $newOperation->name,
                'scheduled_at' => $newOperation->scheduled_at,
            ]);
        }

        $newOperation->refresh();

        InventoryFacade::computeTransfer($newOperation);
    }

    /**
     * Run a pull rule on a line.
     */
    public function runPullRule(Rule $rule, OrderLine $line)
    {
        if ($rule->auto !== InventoryEnums\RuleAuto::MANUAL) {
            return;
        }

        $newMove = InventoryMove::create([
            'state'                   => InventoryEnums\MoveState::DRAFT,
            'reference'               => null,
            'name'                    => $line->name,
            'product_id'              => $line->product_id,
            'product_qty'             => $line->product_qty,
            'product_uom_qty'         => $line->product_uom_qty,
            'quantity'                => $line->product_qty,
            'uom_id'                  => $line->product_uom_id,
            'origin'                  => $line->origin,
            'scheduled_at'            => now()->addDays($rule->delay),
            'source_location_id'      => $rule->source_location_id,
            'destination_location_id' => $rule->destination_location_id,
            'final_location_id'       => $rule->destination_location_id,
            'product_packaging_id'    => $line->product_packaging_id,
            'rule_id'                 => $rule->id,
            'company_id'              => $rule->company_id,
            'operation_type_id'       => $rule->operation_type_id,
            'propagate_cancel'        => $rule->propagate_cancel,
            'warehouse_id'            => $rule->warehouse_id,
            'procure_method'          => InventoryEnums\ProcureMethod::MAKE_TO_ORDER,
            'sale_order_line_id'      => $line->id,
        ]);

        $newMove->save();

        if ($newMove->shouldBypassReservation()) {
            $newMove->update([
                'procure_method' => InventoryEnums\ProcureMethod::MAKE_TO_STOCK,
            ]);
        }

        return $newMove;
    }

    /**
     * Traverse up the location tree to find a matching pull rule.
     */
    public function getPullRule(OrderLine $line, array $filters = [])
    {
        $foundRule = null;

        $location = Location::where('type', InventoryEnums\LocationType::CUSTOMER)->first();

        $filters['action'] = [InventoryEnums\RuleAction::PULL, InventoryEnums\RuleAction::PULL_PUSH];

        while (! $foundRule && $location) {
            $filters['destination_location_id'] = $location->id;

            $foundRule = $this->searchPullRule(
                $line->productPackaging,
                InventoryProduct::find($line->product_id),
                $line->warehouse,
                $filters
            );

            $location = $location->parent;
        }

        return $foundRule;
    }

    /**
     * Search for a pull rule based on the provided filters.
     */
    public function searchPullRule($productPackaging, $product, $warehouse, array $filters)
    {
        if ($warehouse) {
            $filters['warehouse_id'] = $warehouse->id;
        }

        $routeSources = [
            [$productPackaging, 'routes'],
            [$product, 'routes'],
            [$product?->category, 'routes'],
            [$warehouse, 'routes'],
        ];

        foreach ($routeSources as [$source, $relationName]) {
            if (! $source || ! $source->{$relationName}) {
                continue;
            }

            $routeIds = $source->{$relationName}->pluck('id');

            if ($routeIds->isEmpty()) {
                continue;
            }

            $foundRule = Rule::whereIn('route_id', $routeIds)
                ->where($filters)
                ->orderBy('route_sort', 'asc')
                ->orderBy('sort', 'asc')
                ->first();

            if ($foundRule) {
                return $foundRule;
            }
        }

        return null;
    }
}
