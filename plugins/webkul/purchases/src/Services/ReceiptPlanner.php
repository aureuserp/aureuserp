<?php

namespace Webkul\Purchase\Services;

use Illuminate\Support\Str;
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
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Models\OrderLine;

class ReceiptPlanner
{
    protected const OPEN_DESTINATION_TYPES = [
        InventoryEnums\LocationType::INTERNAL,
        InventoryEnums\LocationType::TRANSIT,
        InventoryEnums\LocationType::CUSTOMER,
    ];

    protected const SETTLED_STATES = [
        InventoryEnums\OperationState::DONE,
        InventoryEnums\OperationState::CANCELED,
    ];

    public function syncFromLines($lines): void
    {
        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        foreach ($lines as $line) {
            if (! $line->product || $line->product->type !== ProductType::GOODS) {
                continue;
            }

            $order = $line->order->refresh();

            $this->assertOrderedCoversReceived($line);

            $operation = $this->openOperationFor($line, $order);

            if (! $operation) {
                if ($line->product_qty <= $line->qty_received) {
                    continue;
                }

                $order->refresh();

                $operation = Receipt::create($this->buildOperationAttributes($order));

                $order->operations()->attach($operation->id);
            }

            $moves = InventoryFacade::confirmMoves($this->createMoves(collect([$line]), $operation));

            InventoryFacade::reserveMoves(Move::where('id', $moves->pluck('id'))->get());
        }
    }

    protected function assertOrderedCoversReceived(OrderLine $line): void
    {
        if (float_compare($line->product_qty, $line->qty_received, precisionRounding: $line->uom->rounding) < 0) {
            throw new \Exception(__('You cannot decrease the ordered quantity below the received quantity.\nCreate a return first.'));
        }
    }

    protected function isOpen($operation): bool
    {
        return ! in_array($operation->state, static::SETTLED_STATES)
            && in_array($operation->destinationLocation->type, static::OPEN_DESTINATION_TYPES);
    }

    protected function openOperationFor(OrderLine $line, Order $order)
    {
        $fromLine = $line->inventoryMoves
            ->pluck('operation')
            ->filter()
            ->unique('id')
            ->filter(fn ($operation) => $this->isOpen($operation));

        return $fromLine->isNotEmpty()
            ? $fromLine->first()
            : $order->operations->filter(fn ($operation) => $this->isOpen($operation))->first();
    }

    public function planForOrder(Order $record): void
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
            fn ($operation) => ! in_array($operation->state, static::SETTLED_STATES)
        );

        if ($operations->isEmpty()) {
            $operation = Receipt::create($this->buildOperationAttributes($record));

            $record->operations()->attach($operation->id);

            $operations = collect([$operation]);
        } else {
            $operation = $operations->first();
        }

        $moves = $this->createMoves($record->lines, $operation)
            ->filter(fn ($move) => ! in_array($move->state, [InventoryEnums\MoveState::DONE, InventoryEnums\MoveState::CANCELED]));

        $moves = InventoryFacade::confirmMoves($moves);

        $sort = 0;

        foreach ($moves->sortBy('date') as $move) {
            $move->update(['sort' => $sort += 5]);
        }

        $operation->refresh();

        InventoryFacade::reserveMoves($operation->moves);

        $operation->refresh();

        $operations->merge(Receipt::impactedBy($moves))
            ->each(fn ($impacted) => InventoryFacade::confirmTransfer($impacted->refresh()));

        $this->linkBackToOrder($operation, $record);
    }

    protected function linkBackToOrder($operation, Order $record): void
    {
        $url = PurchaseOrderResource::getUrl('view', ['record' => $record]);

        $operation->addMessage([
            'body' => "This transfer has been created from <a href=\"{$url}\" target=\"_blank\" class=\"fi-color fi-color-primary fi-text-color-600 dark:fi-text-color-300 fi-link fi-size-sm\">{$record->name}</a>.",
            'type' => 'comment',
        ]);
    }

    protected function createMoves($orderLines, $operation)
    {
        $attributes = [];

        foreach ($orderLines->filter(fn ($line) => ! $line->display_type) as $line) {
            foreach ($this->buildMovesForLine($line, $operation) as $moveAttributes) {
                $attributes[] = $moveAttributes;
            }

            $line->moveDestinations->each(fn ($move) => $move->purchaseOrderLines()->detach());
        }

        return collect(array_map(fn ($values) => Move::create($values), $attributes));
    }

    public function buildOperationAttributes(Order $order): array
    {
        if (! $order->procurement_group_id) {
            $group = ProcurementGroup::create([
                'name'       => $order->name,
                'partner_id' => $order->partner_id,
            ]);

            $order->update(['procurement_group_id' => $group->id]);

            $order->lines->each(fn ($line) => $line->update(['procurement_group_id' => $group->id]));
        }

        return [
            'state'                   => InventoryEnums\OperationState::DRAFT,
            'date'                    => $order->ordered_at,
            'origin'                  => $order->name,
            'partner_id'              => $order->partner_id,
            'operation_type_id'       => $order->operation_type_id,
            'source_location_id'      => $order->operationType->source_location_id,
            'destination_location_id' => $this->destinationLocation($order)->id,
            'procurement_group_id'    => $order->procurement_group_id,
            'company_id'              => $order->company_id,
        ];
    }

    public function buildMovesForLine(OrderLine $line, $operation): array
    {
        if ($line->product->type !== ProductType::GOODS) {
            return [];
        }

        $priceUnit = $line->getInventoryMovePriceUnit();

        $alreadyProcured = $this->procuredQuantity($line);

        $moveDestinations = ($line->moveDestinations->isNotEmpty()
            ? $line->moveDestinations
            : $line->inventoryMoves->flatMap->moveDestinations
        )->filter(fn ($move) => $move->state !== InventoryEnums\MoveState::CANCELED && ! $move->isPurchaseReturn());

        $downstreamDemand = $this->downstreamInitialDemand($line, $moveDestinations);

        $qtyToPush = $line->product_qty - $alreadyProcured;

        $qtyToAttach = $moveDestinations->isEmpty() ? 0 : $downstreamDemand - $alreadyProcured;

        $attributes = [];

        if (float_compare($qtyToAttach, 0.0, precisionRounding: $line->uom->rounding) > 0) {
            $qtyToPush = $line->product_qty - $downstreamDemand;

            [$productUomQty, $productUom] = $line->uom->adjustUomQuantities($qtyToAttach, $line->product->uom);

            $attributes[] = $this->buildMoveAttributes($line, $operation, $priceUnit, $productUomQty, $productUom);
        }

        if (! float_is_zero($qtyToPush, precisionRounding: $line->uom->rounding)) {
            [$productUomQty, $productUom] = $line->uom->adjustUomQuantities($qtyToPush, $line->product->uom);

            $attributes[] = array_merge(
                $this->buildMoveAttributes($line, $operation, $priceUnit, $productUomQty, $productUom),
                ['move_destination_ids' => null]
            );
        }

        return $attributes;
    }

    public function buildMoveAttributes(OrderLine $line, $operation, $priceUnit, $productUomQty, $productUom): array
    {
        $this->assertReorderingRuleMatchesWarehouse($line);

        $destinationLocation = $this->destinationLocation($line->order);

        $finalLocation = $line->final_location_id ? $line->finalLocation : $this->finalLocation($line->order);

        if ($finalLocation && $finalLocation->isDescendantOf($destinationLocation)) {
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

    public function assertReorderingRuleMatchesWarehouse(OrderLine $line): void
    {
        $warehouseLocation = $line->order->operationType->warehouse->viewLocation;

        $destinationLocation = $line->moveDestinations->first()?->sourceLocation ?? $line->orderPoint?->location;

        $mismatched = $warehouseLocation
            && $destinationLocation
            && $destinationLocation->warehouse_id
            && ! Str::contains($destinationLocation->parent_path, $warehouseLocation->parent_path);

        if (! $mismatched) {
            return;
        }

        throw new \Exception(__('The warehouse of operation type (:operation_type) is inconsistent with location (:location) of reordering rule (:reordering_rule) for product :product. Change the operation type or cancel the request for quotation.', [
            'product'         => $line->product->name,
            'operation_type'  => $line->order->operationType->name,
            'location'        => $line->orderPoint->location->full_name,
            'reordering_rule' => $line->orderPoint->name,
        ]));
    }

    public function destinationLocation(Order $order): Location
    {
        if ($this->isDropship($order)) {
            return Location::where('type', InventoryEnums\LocationType::CUSTOMER)->first();
        }

        return $order->operationType->destinationLocation;
    }

    public function finalLocation(Order $order): Location
    {
        if ($this->isDropship($order)) {
            return Location::where('type', InventoryEnums\LocationType::CUSTOMER)->first();
        }

        return $order->operationType->warehouse->lotStockLocation;
    }

    protected function isDropship(Order $order): bool
    {
        return (bool) $order->destination_address_id
            && $order->operationType->type === InventoryEnums\OperationType::DROPSHIP;
    }

    public function procuredQuantity(OrderLine $line): float
    {
        [$outgoingMoves, $incomingMoves] = $this->outgoingAndIncomingMoves($line);

        $inLineUom = fn ($move) => $move->uom->computeQuantity(
            $move->state === InventoryEnums\MoveState::DONE ? $move->quantity : $move->product_uom_qty,
            $line->uom,
            roundingMethod: 'HALF-UP'
        );

        return $incomingMoves->sum($inLineUom) - $outgoingMoves->sum($inLineUom);
    }

    public function outgoingAndIncomingMoves(OrderLine $line): array
    {
        $outgoing = collect();

        $incoming = collect();

        $relevant = $line->inventoryMoves->filter(
            fn ($move) => $move->state !== InventoryEnums\MoveState::CANCELED
                && ! $move->is_scraped
                && $line->product_id === $move->product_id
        );

        foreach ($relevant as $move) {
            if ($move->isPurchaseReturn() && ($move->is_refund || ! $move->origin_returned_move_id)) {
                $outgoing->push($move);
            } elseif ($move->destinationLocation->type !== InventoryEnums\LocationType::SUPPLIER) {
                if (! $move->origin_returned_move_id || $move->is_refund) {
                    $incoming->push($move);
                }
            }
        }

        return [$outgoing, $incoming];
    }

    public function downstreamInitialDemand(OrderLine $line, $moveDestinations): float
    {
        $totalQty = $moveDestinations
            ->filter(fn ($move) => $move->state !== InventoryEnums\MoveState::CANCELED
                && $move->destinationLocation->type !== InventoryEnums\LocationType::SUPPLIER)
            ->sum('product_qty');

        return $line->product->uom->computeQuantity($totalQty, $line->uom, roundingMethod: 'HALF-UP');
    }

    public function cancelOperations(Order $record): void
    {
        if (! Package::isPluginInstalled('inventories') || $record->operations->isEmpty()) {
            return;
        }

        $record->operations->each(fn ($operation) => InventoryFacade::cancelTransfer($operation));
    }
}
