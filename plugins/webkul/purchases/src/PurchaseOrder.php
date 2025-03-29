<?php

namespace Webkul\Purchase;

use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Inventory\Enums as InventoryEnums;
use Webkul\Inventory\Models\Receipt;
use Webkul\Purchase\Enums as PurchaseEnums;
use Webkul\Inventory\Models\OperationType;
use Illuminate\Support\Facades\Mail;
use Webkul\Purchase\Mail\VendorPurchaseOrderMail;
use Illuminate\Support\Facades\Schema;
use Webkul\Purchase\Settings\OrderSettings;
use Webkul\Account\Models\Journal as AccountJournal;
use Webkul\Account\Enums as AccountEnums;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Webkul\Account\Services\TaxService;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Models\Move;
use Webkul\Purchase\Models\AccountMove;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource;
use Webkul\Product\Enums\ProductType;
use Webkul\Support\Package;
use Webkul\Account\Models\Partner;

class PurchaseOrder
{
    public function __construct(
        protected TaxService $taxService,
        protected OrderSettings $orderSettings
    )
    {
    }

    public function sendRFQ(Order $record, array $data): Order
    {
        $pdfPath = $this->generateRFQPdf($record);

        foreach ($data['vendors'] as $vendorId) {
            $vendor = Partner::find($vendorId);

            if ($vendor?->email) {
                Mail::to($vendor->email)->send(new VendorPurchaseOrderMail($data['subject'], $data['message'], $pdfPath));
            }
        }

        $record->update([
            'state' => PurchaseEnums\OrderState::SENT,
        ]);

        $record = $this->computePurchaseOrder($record);

        $message = $record->addMessage([
            'body' => $data['message'],
            'type' => 'comment',
        ]);

        $record->addAttachments(
            [$pdfPath],
            ['message_id' => $message->id],
        );

        Storage::delete($pdfPath);

        return $record;
    }

    public function confirmPurchaseOrder(Order $record): Order
    {
        $record->update([
            'state'       => $this->orderSettings->enable_lock_confirmed_orders
                ? PurchaseEnums\OrderState::DONE
                : PurchaseEnums\OrderState::PURCHASE,
            'approved_at' => now(),
        ]);

        $record = $this->computePurchaseOrder($record);

        $this->createInventoryReceipt($record);

        return $record;
    }

    public function sendPurchaseOrder(Order $record, array $data): Order
    {
        $pdfPath = $this->generatePurchaseOrderPdf($record);

        foreach ($data['vendors'] as $vendorId) {
            $vendor = Partner::find($vendorId);

            if ($vendor?->email) {
                Mail::to($vendor->email)->send(new VendorPurchaseOrderMail(
                    $data['subject'],
                    $data['message'],
                    $pdfPath
                ));
            }
        }

        $message = $record->addMessage([
            'body' => $data['message'],
            'type' => 'comment',
        ]);

        $record->addAttachments(
            [$pdfPath],
            ['message_id' => $message->id],
        );

        Storage::delete($pdfPath);

        return $record;
    }

    public function cancelPurchaseOrder(Order $record): Order
    {
        $record->update([
            'state' => PurchaseEnums\OrderState::CANCELED,
        ]);

        $record = $this->computePurchaseOrder($record);

        $this->cancelInventoryOperations($record);

        return $record;
    }

    public function draftPurchaseOrder(Order $record): Order
    {
        $record->update([
            'state' => PurchaseEnums\OrderState::DRAFT,
        ]);

        $record = $this->computePurchaseOrder($record);

        return $record;
    }

    public function lockPurchaseOrder(Order $record): Order
    {
        $record->update([
            'state' => PurchaseEnums\OrderState::DONE,
        ]);

        $record = $this->computePurchaseOrder($record);

        return $record;
    }

    public function unlockPurchaseOrder(Order $record): Order
    {
        $record->update([
            'state' => PurchaseEnums\OrderState::PURCHASE,
        ]);

        $record = $this->computePurchaseOrder($record);

        return $record;
    }

    public function createPurchaseOrderBill(Order $record): Order
    {
        $this->createAccountMove($record);

        $record = $this->computePurchaseOrder($record);

        return $record;
    }

    public function computePurchaseOrder(Order $record): Order
    {
        $record->untaxed_amount = 0;
        $record->tax_amount = 0;
        $record->total_amount = 0;
        $record->total_cc_amount = 0;
        $record->invoice_count = 0;

        foreach ($record->lines as $line) {
            $line->state = $record->state;

            $line = $this->computePurchaseOrderLine($line);

            $record->untaxed_amount += $line->price_subtotal;
            $record->tax_amount += $line->price_tax;
            $record->total_amount += $line->price_total;
            $record->total_cc_amount += $line->price_total;
        }

        $record = $this->computeReceiptStatus($record);

        $record = $this->computeInvoiceStatus($record);

        $record->save();

        return $record;
    }

    public function computePurchaseOrderLine(OrderLine $line): OrderLine
    {
        $line = $this->computeQtyBilled($line);

        $line = $this->computeQtyReceived($line);

        if ($line->qty_received_method == Enums\QtyReceivedMethod::MANUAL) {
            $line->qty_received_manual = $line->qty_received ?? 0;
        }

        $line->qty_to_invoice = $line->qty_received - $line->qty_invoiced;

        $subTotal = $line->price_unit * $line->product_qty;

        $discountAmount = 0;

        if ($line->discount > 0) {
            $discountAmount = $subTotal * ($line->discount / 100);

            $subTotal = $subTotal - $discountAmount;
        }

        $taxIds = $line->taxes->pluck('id')->toArray();

        [$subTotal, $taxAmount] = $this->taxService->collectionTaxes($taxIds, $subTotal, $line->product_qty);

        $line->price_subtotal = round($subTotal, 4);

        $line->price_tax = $taxAmount;

        $line->price_total = $subTotal + $taxAmount;

        $line->save();

        return $line;
    }

    public function computeReceiptStatus(Order $order): Order
    {
        if (! Package::isPluginInstalled('inventories')) {
            $order->receipt_status = PurchaseEnums\OrderReceiptStatus::NO;

            return $order;
        }

        if ($order->operations->isEmpty() || $order->operations->every(function($receipt) {
            return $receipt->state == InventoryEnums\OperationState::CANCELED;
        })) {
            $order->receipt_status = PurchaseEnums\OrderReceiptStatus::NO;
        } elseif ($order->operations->every(function($receipt) {
            return in_array($receipt->state, [InventoryEnums\OperationState::DONE, InventoryEnums\OperationState::CANCELED]);
        })) {
            $order->receipt_status = PurchaseEnums\OrderReceiptStatus::FULL;
        } elseif ($order->operations->contains(function($receipt) {
            return $receipt->state == InventoryEnums\OperationState::DONE;
        })) {
            $order->receipt_status = PurchaseEnums\OrderReceiptStatus::PARTIAL;
        } else {
            $order->receipt_status = PurchaseEnums\OrderReceiptStatus::PENDING;
        }

        return $order;
    }

    public function computeInvoiceStatus(Order $order): Order
    {
        if (! in_array($order->state, [PurchaseEnums\OrderState::PURCHASE, PurchaseEnums\OrderState::DONE])) {
            $order->invoice_status = PurchaseEnums\OrderInvoiceStatus::NO;
            
            return $order;
        }

        $floatIsZero = function($value, $precision) {
            return abs($value) < pow(10, -$precision);
        };

        $precision = 4;

        if ($order->lines->contains(function($line) use($floatIsZero, $precision) {
            return ! $floatIsZero($line->qty_to_invoice, $precision);
        })) {
            $order->invoice_status = PurchaseEnums\OrderInvoiceStatus::TO_INVOICED;
        } elseif ($order->lines->every(function($line) use($floatIsZero, $precision) {
            return $floatIsZero($line->qty_to_invoice, $precision);
        }) && $order->accountMoves->isNotEmpty()) {
            $order->invoice_status = PurchaseEnums\OrderInvoiceStatus::INVOICED;
        } else {
            $order->invoice_status = PurchaseEnums\OrderInvoiceStatus::NO;
        }

        return $order;
    }

    public function computeQtyBilled(OrderLine $line): OrderLine
    {
        $qty = 0.0;

        foreach ($line->accountMoveLines as $accountMoveLine) {
            if (
                $accountMoveLine->move->state != AccountEnums\MoveState::CANCEL
                || $accountMoveLine->move->payment_state == AccountEnums\PaymentState::INVOICING_LEGACY
            ) {
                if ($accountMoveLine->move->move_type == AccountEnums\MoveType::IN_INVOICE) {
                    $qty += $accountMoveLine->uom->computeQuantity($accountMoveLine->quantity, $line->uom);
                } elseif ($accountMoveLine->move->move_type == AccountEnums\MoveType::IN_REFUND) {
                    $qty -= $accountMoveLine->uom->computeQuantity($accountMoveLine->quantity, $line->uom);
                }
            }
        }

        $line->qty_invoiced = $qty;

        if (in_array($line->order->state, [PurchaseEnums\OrderState::PURCHASE, PurchaseEnums\OrderState::DONE])) {
            if ($line->product->purchase_method == 'purchase') {
                $line->qty_to_invoice = $line->product_qty - $line->qty_invoiced;
            } else {
                $line->qty_to_invoice = $line->qty_received - $line->qty_invoiced;
            }
        } else {
            $line->qty_to_invoice = 0;
        }

        return $line;
    }

    public function computeQtyReceived(OrderLine $line): OrderLine
    {
        $line->qty_received = 0.0;

        if ($line->qty_received_method == Enums\QtyReceivedMethod::MANUAL) {
            $line->qty_received = $line->qty_received_manual ?? 0.0;
        }

        if ($line->qty_received_method == Enums\QtyReceivedMethod::STOCK_MOVE) {
            $total = 0.0;

            foreach ($line->inventoryMoves as $move) {
                if ($move->state !== InventoryEnums\MoveState::DONE) {
                    continue;
                }

                if ($move->isPurchaseReturn()) {
                    if (! $move->originReturnedMove || $move->is_refund) {
                        $total -= $move->uom->computeQuantity(
                            $move->quantity, 
                            $line->uom, 
                            true, 
                            'HALF-UP'
                        );
                    }
                } elseif (
                    $move->originReturnedMove
                    && $move->originReturnedMove->isDropshipped()
                    && ! $move->isDropshippedReturned()
                ) {
                    // Edge case: The dropship is returned to the stock, not to the supplier.
                    // In this case, the received quantity on the Purchase order is set although we didn't
                    // receive the product physically in our stock. To avoid counting the
                    // quantity twice, we do nothing.
                    continue;
                } elseif (
                    $move->originReturnedMove
                    && $move->originReturnedMove->isPurchaseReturn()
                    && ! $move->is_refund
                ) {
                    continue;
                } else {
                    $total += $move->uom->computeQuantity(
                        $move->quantity, 
                        $line->uom, 
                        true, 
                        'HALF-UP'
                    );
                }

                $line->qty_received = $total;
            }
        }

        return $line;
    }

    public function generateRFQPdf($record)
    {
        $pdfPath = 'Request for Quotation-'.str_replace('/', '_', $record->name).'.pdf';

        if (! Storage::exists($pdfPath)) {
            $pdf = PDF::loadView('purchases::filament.admin.clusters.orders.orders.actions.print-quotation', [
                'records'  => [$record],
            ]);

            Storage::disk('public')->put($pdfPath, $pdf->output());
        }

        return $pdfPath;
    }

    public function generatePurchaseOrderPdf($record)
    {
        $pdfPath = 'Purchase Order-'.str_replace('/', '_', $record->name).'.pdf';

        if (! Storage::exists($pdfPath)) {
            $pdf = PDF::loadView('purchases::filament.admin.clusters.orders.orders.actions.print-purchase-order', [
                'records'  => [$record],
            ]);

            Storage::disk('public')->put($pdfPath, $pdf->output());
        }

        return $pdfPath;
    }

    protected function createInventoryReceipt(Order $record): void
    {
        if (! in_array($record->state, [PurchaseEnums\OrderState::PURCHASE, PurchaseEnums\OrderState::DONE])) {
            return;
        }

        if (! $record->lines->contains(fn ($line) => $line->product->type === ProductType::GOODS)) {
            return;
        }

        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        $record->operation_type_id = $this->getInventoryOperationType($record)->id;

        $record->save();

        $supplierLocation = Location::where('type', InventoryEnums\LocationType::SUPPLIER)->first();

        $operation = Receipt::create([
            'state'                   => InventoryEnums\OperationState::DRAFT,
            'move_type'               => InventoryEnums\MoveType::DIRECT,
            'origin'                  => $record->name,
            'partner_id'              => $record->partner_id,
            'partner_address_id'      => $record->partner_address_id,
            'date'                    => $record->ordered_at,
            'operation_type_id'       => $record->operation_type_id,
            'source_location_id'      => $supplierLocation->id,
            'destination_location_id' => $record->operationType->destination_location_id,
            'company_id'              => $record->company_id,
            'user_id'                 => Auth::id(),
            'creator_id'              => Auth::id(),
        ]);

        $operation->save();

        foreach ($record->lines as $line) {
            $line->update([
                'final_location_id' => $this->getFinalWarehouseLocation($record)->id,
            ]);

            $move = Move::create([
                'operation_id'            => $operation->id,
                'name'                    => $operation->name,
                'reference'               => $operation->name,
                'description_picking'     => $line->product->picking_description ?? $line->name,
                'state'                   => InventoryEnums\MoveState::DRAFT,
                'scheduled_at'            => $line->planned_at,
                'deadline'                => $line->planned_at,
                'reservation_date'        => now(),
                'product_packaging_id'    => $line->product_packaging_id,
                'product_id'              => $line->product_id,
                'product_qty'             => $line->product_qty,
                'product_uom_qty'         => $line->product_uom_qty,
                'quantity'                => $line->product_uom_qty,
                'uom_id'                  => $line->uom_id,
                'partner_id'              => $operation->partner_id,
                'warehouse_id'            => $operation->destinationLocation->warehouse_id,
                'source_location_id'      => $operation->source_location_id,
                'destination_location_id' => $operation->destination_location_id,
                'operation_type_id'       => $operation->operation_type_id,
                'final_location_id'       => $line->final_location_id,
                'company_id'              => $operation->destinationLocation->company_id,
                'purchase_order_line_id'  => $line->id,
            ]);
        }

        $record->operations()->attach($operation->id);

        $operation->refresh();

        foreach ($operation->moves as $move) {
            OperationResource::updateOrCreateMoveLines($move);
        }

        OperationResource::computeTransferState($operation);

        $url = PurchaseOrderResource::getUrl('view', ['record' => $record]);

        $operation->addMessage([
            'body' => "This transfer has been created from <a href=\"{$url}\" target=\"_blank\" class=\"text-primary-600 dark:text-primary-400\">{$record->name}</a>.",
            'type' => 'comment',
        ]);
    }

    protected function cancelInventoryOperations(Order $record): void
    {
        if (! Schema::hasTable('inventories_operations')) {
            return;
        }

        if ($record->operations->isEmpty()) {
            return;
        }

        $record->operations->each(function ($operation) {
            foreach ($operation->moves as $move) {
                $move->update([
                    'state'    => InventoryEnums\MoveState::CANCELED,
                    'quantity' => 0,
                ]);

                $move->lines()->delete();
            }

            OperationResource::computeTransferState($operation);
        });
    }

    protected function getInventoryOperationType(Order $record): ?OperationType
    {
        $operationType = OperationType::where('type', '=', InventoryEnums\OperationType::INCOMING)
            ->whereHas('warehouse', function($query) use ($record) {
                $query->where('company_id', '=', $record->company_id);
            })
            ->first();
        
        if (! $operationType) {
            $operationType = OperationType::where('type', '=', InventoryEnums\OperationType::INCOMING)
                ->whereDoesntHave('warehouse')
                ->first();
        }

        return $operationType;
    }

    protected function getFinalWarehouseLocation(Order $record): ?Location
    {
        return $record->operationType->warehouse->lotStockLocation;
    }

    public function createAccountMove($record): void
    {
        $accountMove = AccountMove::create([
            'state'                        => AccountEnums\MoveState::DRAFT,
            'move_type'                    => $record->qty_to_invoice >=0 ? AccountEnums\MoveType::IN_INVOICE : AccountEnums\MoveType::IN_REFUND,
            'payment_state'                => AccountEnums\PaymentState::NOT_PAID,
            'invoice_partner_display_name' => $record->partner->name,
            'invoice_origin'               => $record->name,
            'date'                         => now(),
            'invoice_date_due'             => now(),
            'invoice_currency_rate'        => 1,
            'journal_id'                   => AccountJournal::where('type', AccountEnums\JournalType::PURCHASE->value)->first()?->id,
            'company_id'                   => $record->company_id,
            'currency_id'                  => $record->currency_id,
            'invoice_payment_term_id'      => $record->payment_term_id,
            'partner_id'                   => $record->partner_id,
            'commercial_partner_id'        => $record->partner_id,
            'partner_shipping_id'          => $record->partner_shipping_id,
            // 'partner_bank_id' => $record->partner_bank_id,//TODO: add partner bank id
            'fiscal_position_id' => $record->fiscal_position_id,
            // 'preferred_payment_method_line_id' => 1,
            'creator_id' => Auth::id(),
        ]);

        $record->accountMoves()->attach($accountMove->id);

        foreach ($record->lines as $line) {
            $this->createAccountMoveLine($accountMove, $line);
        }

        if ($record->qty_to_invoice >= 0) {
            BillResource::collectTotals($accountMove);
        } else {
            RefundResource::collectTotals($accountMove);
        }
    }

    public function createAccountMoveLine($accountMove, $orderLine): void
    {
        $accountMoveLine = $accountMove->lines()->create([
            'state'                  => AccountEnums\MoveState::DRAFT,
            'name'                   => $orderLine->name,
            'display_type'           => AccountEnums\DisplayType::PRODUCT,
            'date'                   => $accountMove->date,
            'creator_id'             => $accountMove?->creator_id,
            'parent_state'           => $accountMove->state,
            'quantity'               => abs($orderLine->qty_to_invoice),
            'price_unit'             => $orderLine->price_unit,
            'discount'               => $orderLine->discount,
            'journal_id'             => $accountMove->journal_id,
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