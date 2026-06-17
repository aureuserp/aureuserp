<?php

namespace Webkul\Purchase;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Account\Enums as AccountEnums;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Models\Partner;
use Webkul\Inventory\Enums as InventoryEnums;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\ProcurementGroup;
use Webkul\Inventory\Models\Receipt;
use Webkul\PluginManager\Package;
use Webkul\Product\Enums\ProductType;
use Webkul\Purchase\Enums as PurchaseEnums;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource;
use Webkul\Purchase\Mail\VendorPurchaseOrderMail;
use Webkul\Purchase\Models\AccountMove;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Purchase\Settings\OrderSettings;
use Webkul\Security\Enums\PermissionType;

class PurchaseOrder
{
    public static function getOrderSettings(): OrderSettings
    {
        return once(fn () => app(OrderSettings::class));
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
            'body' => Str::markdown($data['message']),
            'type' => 'comment',
        ]);

        $record->addAttachments(
            [$pdfPath],
            ['message_id' => $message->id],
        );

        return $record;
    }

    public function confirmPurchaseOrder(Order $record): Order
    {
        $user = Auth::user();

        $settings = static::getOrderSettings();

        $requiresApproval = $settings->enable_order_approval;

        $amountExceeds = $record->total_amount >= $settings->order_validation_amount;

        $needsApproval = $requiresApproval && $amountExceeds;

        if (! $needsApproval) {
            return $this->approveOrder($record, $settings);
        }

        if (static::canUserApprove($user)) {
            return $this->approveOrder($record, $settings);
        }

        $record->update([
            'state' => PurchaseEnums\OrderState::TO_APPROVE,
        ]);

        return $this->computePurchaseOrder($record);
    }

    private function approveOrder(Order $record, $settings): Order
    {
        $record->update([
            'state' => $settings->enable_lock_confirmed_orders
                ? PurchaseEnums\OrderState::DONE
                : PurchaseEnums\OrderState::PURCHASE,
            'approved_at' => now(),
        ]);

        $record = $this->computePurchaseOrder($record);

        $this->createInventoryOperation($record);

        return $record;
    }

    public function canUserApprove($user): bool
    {
        if (! $user) {
            return false;
        }

        if (in_array($user->resource_permission, [PermissionType::GLOBAL, PermissionType::GROUP])) {
            return true;
        }

        return false;
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
            'body' => Str::markdown($data['message']),
            'type' => 'comment',
        ]);

        $record->addAttachments(
            [$pdfPath],
            ['message_id' => $message->id],
        );

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

        if ($line->qty_received_method == PurchaseEnums\QtyReceivedMethod::MANUAL) {
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

        [$subTotal, $taxAmount] = TaxFacade::collect($taxIds, $subTotal, $line->product_qty);

        $line->price_subtotal = round($subTotal, 4);

        $line->price_tax = $taxAmount;

        $line->price_total = $subTotal + $taxAmount;

        $line->save();

        return $line;
    }

    public function computeInvoiceStatus(Order $order): Order
    {
        if (! in_array($order->state, [PurchaseEnums\OrderState::PURCHASE, PurchaseEnums\OrderState::DONE])) {
            $order->invoice_status = PurchaseEnums\OrderInvoiceStatus::NO;

            return $order;
        }

        $floatIsZero = function ($value, $precision) {
            return abs($value) < pow(10, -$precision);
        };

        $precision = 4;

        if ($order->lines->contains(function ($line) use ($floatIsZero, $precision) {
            return ! $floatIsZero($line->qty_to_invoice, $precision);
        })) {
            $order->invoice_status = PurchaseEnums\OrderInvoiceStatus::TO_INVOICED;
        } elseif ($order->lines->every(function ($line) use ($floatIsZero, $precision) {
            return $floatIsZero($line->qty_to_invoice, $precision);
        }) && $order->accountMoves->isNotEmpty()) {
            $order->invoice_status = PurchaseEnums\OrderInvoiceStatus::INVOICED;
        } else {
            $order->invoice_status = PurchaseEnums\OrderInvoiceStatus::NO;
        }

        return $order;
    }

    public function computeReceiptStatus(Order $order): Order
    {
        if (! Package::isPluginInstalled('inventories')) {
            $order->receipt_status = PurchaseEnums\OrderReceiptStatus::NO;

            return $order;
        }

        if ($order->operations->isEmpty() || $order->operations->every(function ($receipt) {
            return $receipt->state == InventoryEnums\OperationState::CANCELED;
        })) {
            $order->receipt_status = PurchaseEnums\OrderReceiptStatus::NO;
        } elseif ($order->operations->every(function ($receipt) {
            return in_array($receipt->state, [InventoryEnums\OperationState::DONE, InventoryEnums\OperationState::CANCELED]);
        })) {
            $order->receipt_status = PurchaseEnums\OrderReceiptStatus::FULL;
        } elseif ($order->operations->contains(function ($receipt) {
            return $receipt->state == InventoryEnums\OperationState::DONE;
        })) {
            $order->receipt_status = PurchaseEnums\OrderReceiptStatus::PARTIAL;
        } else {
            $order->receipt_status = PurchaseEnums\OrderReceiptStatus::PENDING;
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

        if ($line->qty_received_method == PurchaseEnums\QtyReceivedMethod::MANUAL) {
            $line->qty_received = $line->qty_received_manual ?? 0.0;
        }

        if ($line->qty_received_method == PurchaseEnums\QtyReceivedMethod::STOCK_MOVE) {
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
            $pdf = Pdf::loadView('purchases::filament.admin.clusters.orders.orders.actions.print-quotation', [
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
            $pdf = Pdf::loadView('purchases::filament.admin.clusters.orders.orders.actions.print-purchase-order', [
                'records'  => [$record],
            ]);

            Storage::disk('public')->put($pdfPath, $pdf->output());
        }

        return $pdfPath;
    }

    public function createOrUpdateInventoryOperation($lines): void
    {
        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        foreach ($lines as $line) {
            if (! $line->product || $line->product->type !== ProductType::GOODS) {
                continue;
            }

            $order = $line->order->refresh();

            $rounding = $line->uom->rounding;

            if (float_compare($line->product_qty, $line->qty_received, precisionRounding: $rounding) < 0) {
                throw new \Exception(__('You cannot decrease the ordered quantity below the received quantity.\nCreate a return first.'));
            }

            $lineOperations = $line->inventoryMoves
                ->pluck('operation')
                ->filter()
                ->unique('id')
                ->filter(fn ($operation) => ! in_array($operation->state, [
                        InventoryEnums\OperationState::DONE,
                        InventoryEnums\OperationState::CANCELED
                    ])
                    && in_array($operation->destinationLocation->type, [
                        InventoryEnums\LocationType::INTERNAL,
                        InventoryEnums\LocationType::TRANSIT,
                        InventoryEnums\LocationType::CUSTOMER
                    ])
                );

            if ($lineOperations->isNotEmpty()) {
                $operation = $lineOperations->first();
            } else {
                $operation = $order->operations
                    ->filter(fn ($operation) => ! in_array($operation->state, [
                        InventoryEnums\OperationState::DONE,
                        InventoryEnums\OperationState::CANCELED
                    ])
                    && in_array($operation->destinationLocation->type, [
                        InventoryEnums\LocationType::INTERNAL,
                        InventoryEnums\LocationType::TRANSIT,
                        InventoryEnums\LocationType::CUSTOMER
                    ])
                    )
                    ->first();
            }

            if (! $operation) {
                if (! ($line->product_qty > $line->qty_received)) {
                    continue;
                }

                $order->refresh();

                $values = $this->prepareInventoryOperation($order);

                $operation = Receipt::create($values);

                $order->operations()->attach($operation->id);
            }

            $moves = $this->createInventoryMoves(collect([$line]), $operation);

            $moves = InventoryFacade::confirmMoves($moves);

            $moves = Move::where('id', $moves->pluck('id'))->get();

            InventoryFacade::assignMoves($moves);
        }
    }

    protected function createInventoryOperation(Order $record): void
    {
        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        if (! in_array($record->state, [PurchaseEnums\OrderState::PURCHASE, PurchaseEnums\OrderState::DONE])) {
            return;
        }

        if (! $record->lines->contains(fn ($line) => $line->product->type === ProductType::GOODS)) {
            return;
        }

        $record->refresh();

        $operations = $record->operations->filter(
            fn ($operation) => ! in_array($operation->state, [InventoryEnums\OperationState::DONE, InventoryEnums\OperationState::CANCELED])
        );

        if ($operations->isEmpty()) {
            $values = $this->prepareInventoryOperation($record);

            $operation = Receipt::create($values);

            $record->operations()->attach($operation->id);

            $operations = collect([$operation]);
        } else {
            $operation = $operations->first();
        }

        $moves = $this->createInventoryMoves($record->lines, $operation);

        $moves = $moves->filter(fn ($m) => ! in_array($m->state, [InventoryEnums\MoveState::DONE, InventoryEnums\MoveState::CANCELED]));

        $moves = InventoryFacade::confirmMoves($moves);

        $sort = 0;

        foreach ($moves->sortBy('date') as $move) {
            $sort += 5;

            $move->update(['sort' => $sort]);
        }

        $operation->refresh();

        InventoryFacade::assignMoves($operation->moves);

        $operation->refresh();

        $forwardOperations = Receipt::getImpactedOperations($moves);

        $operations->merge($forwardOperations)->each(function ($operation) {
            InventoryFacade::confirmTransfer($operation->refresh());
        });

        $url = PurchaseOrderResource::getUrl('view', ['record' => $record]);

        $operation->addMessage([
            'body' => "This transfer has been created from <a href=\"{$url}\" target=\"_blank\" class=\"fi-color fi-color-primary fi-text-color-600 dark:fi-text-color-300 fi-link fi-size-sm\">{$record->name}</a>.",
            'type' => 'comment',
        ]);
    }

    protected function createInventoryMoves($orderLines, $operation)
    {
        $values = [];

        foreach ($orderLines->filter(fn ($line) => ! $line->display_type) as $line) {
            foreach ($this->prepareInventoryMoves($line, $operation) as $val) {
                $values[] = $val;
            }

            $line->moveDestinations->each(fn ($move) => $move->purchaseOrderLines()->detach());
        }

        return collect(array_map(fn ($val) => Move::create($val), $values));
    }

    public function prepareInventoryOperation(Order $order): array
    {
        if (! $order->procurement_group_id) {
            $procurementGroup = ProcurementGroup::create([
                'name'       => $order->name,
                'partner_id' => $order->partner_id,
            ]);

            $order->update([
                'procurement_group_id' => $procurementGroup->id,
            ]);

            $order->lines->each(function ($line) use ($procurementGroup) {
                $line->update([
                    'procurement_group_id' => $procurementGroup->id,
                ]);
            });
        }

        return [
            'state'                   => InventoryEnums\OperationState::DRAFT,
            'date'                    => $order->ordered_at,
            'origin'                  => $order->name,
            'partner_id'              => $order->partner_id,
            'operation_type_id'       => $order->operation_type_id,
            'source_location_id'      => $order->operationType->source_location_id,
            'destination_location_id' => $this->getDestinationLocation($order)->id,
            'procurement_group_id'    => $order->procurement_group_id,
            'company_id'              => $order->company_id,
        ];
    }

    public function prepareInventoryMoves(OrderLine $line, $operation)
    {
        $values = [];

        if ($line->product->type !== ProductType::GOODS) {
            return $values;
        }

        $priceUnit = $line->getInventoryMovePriceUnit();

        $qty = $this->getQtyProcurement($line);

        $moveDestinations = $line->moveDestinations->isNotEmpty()
            ? $line->moveDestinations
            : $line->inventoryMoves->flatMap->moveDestinations;

        $moveDestinations = $moveDestinations->filter(
            fn ($move) => $move->state !== InventoryEnums\MoveState::CANCELED && ! $move->isPurchaseReturn()
        );


        $qtyToPush = $line->product_qty - $qty;

        $moveDestinationsInitialDemand = $this->getMoveDestinationsInitialDemand($line, $moveDestinations);

        if ($moveDestinations->isEmpty()) {
            $qtyToAttach = 0;
        } else {
            $qtyToAttach = $moveDestinationsInitialDemand - $qty;
        }

        if (float_compare($qtyToAttach, 0.0, precisionRounding: $line->uom->rounding) > 0) {
            $qtyToPush = $line->product_qty - $moveDestinationsInitialDemand;

            [$productUomQty, $productUom] = $line->uom->adjustUomQuantities($qtyToAttach, $line->product->uom);

            $values[] = $this->prepareInventoryMoveValues($line, $operation, $priceUnit, $productUomQty, $productUom);
        }

        if (! float_is_zero($qtyToPush, precisionRounding: $line->uom->rounding)) {
            [$productUomQty, $productUom] = $line->uom->adjustUomQuantities($qtyToPush, $line->product->uom);

            $extraMoveVals = $this->prepareInventoryMoveValues($line, $operation, $priceUnit, $productUomQty, $productUom);

            $extraMoveVals['move_destination_ids'] = null;

            $values[] = $extraMoveVals;
        }

        return $values;
    }

    public function prepareInventoryMoveValues(OrderLine $line, $operation, $priceUnit, $productUomQty, $productUom): array
    {
        $this->checkOrderPointOperationType($line);

        $destinationLocation = $this->getDestinationLocation($line->order);

        $finalLocation = $line->final_location_id ? $line->finalLocation : $this->getFinalLocation($line->order);

        if ($finalLocation && $finalLocation->isChildOf($destinationLocation)) {
            $destinationLocation = $finalLocation;
        }

        $datePlanned = $line->planned_at ?? $line->order->planned_at;

        return [
            'name'                    => Str::limit($line->product->name ?? '', 2000, ''),
            'product_id'              => $line->product_id,
            'scheduled_at'            => $datePlanned,
            'deadline'                => $datePlanned,
            'source_location_id'      => $line->order->operationType->source_location_id,
            'destination_location_id' => $destinationLocation->id,
            'final_location_id'       => $finalLocation?->id,
            'operation_id'            => $operation->id,
            'partner_id'              => $line->order->destination_address_id,
            'move_destination_ids'    => $line->moveDestinations->pluck('id')->all(),
            'state'                   => InventoryEnums\MoveState::DRAFT,
            'purchase_order_line_id'  => $line->id,
            'company_id'              => $line->order->company_id,
            'price_unit'              => $priceUnit,
            'operation_type_id'       => $line->order->operation_type_id,
            'procurement_group_id'    => $line->order->procurement_group_id,
            'origin'                  => $line->order->name,
            'description_picking'     => $line->product->description_pickingin ?? $line->name,
            'propagate_cancel'        => $line->propagate_cancel,
            'warehouse_id'            => $line->order->operationType->warehouse_id,
            'product_uom_qty'         => $productUomQty,
            'product_uom'             => $productUom->id,
            'product_packaging_id'    => $line->product_packaging_id,
            'sort'                    => $line->sort,
        ];
    }

    public function checkOrderPointOperationType(OrderLine $line): void
    {
        $warehouseLocation = $line->order->operationType->warehouse->viewLocation;

        $destinationLocation = $line->moveDestinations->first()?->sourceLocation ?? $line->orderPoint?->location;

        if (
            $warehouseLocation
            && $destinationLocation
            && $destinationLocation->warehouse_id
            && ! Str::contains($destinationLocation->parent_path, $warehouseLocation->parent_path)
        ) {
            throw new \Exception(__('The warehouse of operation type (:operation_type) is inconsistent with location (:location) of reordering rule (:reordering_rule) for product :product. Change the operation type or cancel the request for quotation.', [
                'product'         => $line->product->name,
                'operation_type'  => $line->order->operationType->name,
                'location'        => $line->orderPoint->location->full_name,
                'reordering_rule' => $line->orderPoint->name,
            ]));
        }
    }

    public function getDestinationLocation(Order $order): Location
    {
        if ($order->destination_address_id && $order->operationType->type === InventoryEnums\OperationType::DROPSHIP) {
            return Location::where('type', InventoryEnums\LocationType::CUSTOMER)->first();
        }

        return $order->operationType->destinationLocation;
    }

    public function getFinalLocation(Order $order): Location
    {
        if ($order->destination_address_id && $order->operationType->type === InventoryEnums\OperationType::DROPSHIP) {
            return Location::where('type', InventoryEnums\LocationType::CUSTOMER)->first();
        }

        return $order->operationType->warehouse->lotStockLocation;
    }

    public function getQtyProcurement(OrderLine $line): float
    {
        $qty = 0.0;

        [$outgoingMoves, $incomingMoves] = $this->getOutgoingIncomingMoves($line);

        foreach ($outgoingMoves as $move) {
            $qtyToCompute = $move->state === InventoryEnums\MoveState::DONE ? $move->quantity : $move->product_uom_qty;

            $qty -= $move->uom->computeQuantity($qtyToCompute, $line->uom, roundingMethod: 'HALF-UP');
        }

        foreach ($incomingMoves as $move) {
            $qtyToCompute = $move->state === InventoryEnums\MoveState::DONE ? $move->quantity : $move->product_uom_qty;

            $qty += $move->uom->computeQuantity($qtyToCompute, $line->uom, roundingMethod: 'HALF-UP');
        }

        return $qty;
    }

    public function getOutgoingIncomingMoves(OrderLine $line): array
    {
        $outgoingMoves = collect();

        $incomingMoves = collect();

        $relevantMoves = $line->inventoryMoves->filter(
            fn ($move) => $move->state !== InventoryEnums\MoveState::CANCELED
                && ! $move->is_scraped
                && $line->product_id === $move->product_id
        );

        foreach ($relevantMoves as $move) {
            if ($move->isPurchaseReturn() && ($move->is_refund || ! $move->origin_returned_move_id)) {
                $outgoingMoves->push($move);
            } elseif ($move->destinationLocation->type !== InventoryEnums\LocationType::SUPPLIER) {
                if (! $move->origin_returned_move_id || ($move->origin_returned_move_id && $move->is_refund)) {
                    $incomingMoves->push($move);
                }
            }
        }

        return [$outgoingMoves, $incomingMoves];
    }

    public function getMoveDestinationsInitialDemand(OrderLine $line, $moveDestinations): float
    {
        $totalQty = $moveDestinations
            ->filter(fn ($move) => $move->state !== InventoryEnums\MoveState::CANCELED
                && $move->destinationLocation->type !== InventoryEnums\LocationType::SUPPLIER
            )
            ->sum('product_qty');

        return $line->product->uom->computeQuantity($totalQty, $line->uom, roundingMethod: 'HALF-UP');
    }

    protected function cancelInventoryOperations(Order $record): void
    {
        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        if ($record->operations->isEmpty()) {
            return;
        }

        $record->operations->each(fn ($operation) => InventoryFacade::cancelTransfer($operation));
    }

    protected function getFinalWarehouseLocation(Order $record): ?Location
    {
        return $record->operationType->warehouse->lotStockLocation;
    }

    public function createAccountMove($record): void
    {
        $accountMove = AccountMove::create([
            'move_type'               => $record->qty_to_invoice >= 0 ? AccountEnums\MoveType::IN_INVOICE : AccountEnums\MoveType::IN_REFUND,
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

        AccountFacade::computeAccountMove($accountMove);
    }

    public function createAccountMoveLine($accountMove, $orderLine): void
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
