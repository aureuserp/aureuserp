<?php

namespace Webkul\Sale\Services;

use Webkul\Inventory\Enums as InventoryEnums;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Product as InventoryProduct;
use Webkul\Inventory\Support\ProcurementOptions;
use Webkul\Inventory\Support\ProcurementRequest;
use Webkul\PluginManager\Package;
use Webkul\Product\Enums as ProductEnums;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;

class ProcurementRequester
{
    public function requestForLines($lines, $previousProductUOMQty = false): void
    {
        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        $requests = collect();

        foreach ($lines as $line) {
            $line->refresh();

            if (! $this->needsProcurement($line)) {
                continue;
            }

            $alreadyProcured = $this->procuredQuantity($line, $previousProductUOMQty);

            if (float_compare($alreadyProcured, $line->product_qty, precisionDigits: 2) == 0) {
                continue;
            }

            $group = $this->syncProcurementGroup($line);

            [$productQty, $procurementUom] = $line->uom->adjustUomQuantities(
                $line->product_qty - $alreadyProcured,
                $line->product->uom
            );

            $requests->push($this->buildProcurementRequest($line, $productQty, $procurementUom, $this->originFor($line), $group));
        }

        InventoryFacade::runProcurements($requests);
    }

    protected function needsProcurement(OrderLine $line): bool
    {
        return $line->state === OrderState::SALE
            && ! $line->order->locked
            && $line->product?->type === ProductEnums\ProductType::GOODS;
    }

    protected function originFor(OrderLine $line): string
    {
        return $line->order->client_order_ref
            ? "{$line->order->name} - {$line->order->client_order_ref}"
            : $line->order->name;
    }

    protected function syncProcurementGroup(OrderLine $line)
    {
        $group = $line->order->procurementGroup;

        if (! $group) {
            $group = $line->order->procurementGroup()->create([
                'name'          => $line->order->name,
                'move_type'     => $line->order->picking_policy,
                'partner_id'    => $line->order->partner_shipping_id,
                'sale_order_id' => $line->order->id,
            ]);

            $line->order->procurement_group_id = $group->id;

            $line->order->save();

            return $group;
        }

        if ($group->partner_id !== $line->order->partner_shipping_id) {
            $group->update(['partner_id' => $line->order->partner_shipping_id]);
        }

        if ($group->move_type !== $line->order->picking_policy) {
            $group->update(['move_type' => $line->order->picking_policy]);
        }

        return $group;
    }

    public function outgoingAndIncomingMoves(OrderLine $orderLine, bool $strict = true): array
    {
        $moves = $orderLine->inventoryMoves->filter(
            fn ($move) => $move->state != InventoryEnums\MoveState::CANCELED
                && ! $move->is_scraped
                && $orderLine->product_id == $move->product_id
        );

        $triggeringRuleIds = $strict ? [] : $this->triggeringRuleIds($moves);

        $outgoingIds = [];

        $incomingIds = [];

        foreach ($moves as $move) {
            if ($this->isOutgoing($move, $strict, $triggeringRuleIds)) {
                if (! $move->origin_returned_move_id || $move->is_refund) {
                    $outgoingIds[] = $move->id;
                }
            } elseif ($move->sourceLocation->type == InventoryEnums\LocationType::CUSTOMER && $move->is_refund) {
                $incomingIds[] = $move->id;
            }
        }

        return [
            $moves->whereIn('id', $outgoingIds),
            $moves->whereIn('id', $incomingIds),
        ];
    }

    protected function triggeringRuleIds($moves): array
    {
        if ($moves->isEmpty()) {
            return [];
        }

        $ruleIds = [];

        $seenWarehouseIds = [];

        foreach ($moves->sortBy('id') as $move) {
            if (! in_array($move->warehouse->id, $seenWarehouseIds)) {
                $ruleIds[] = $move->rule_id;

                $seenWarehouseIds[] = $move->warehouse_id;
            }
        }

        return $ruleIds;
    }

    protected function isOutgoing($move, bool $strict, array $triggeringRuleIds): bool
    {
        if ($strict) {
            return $move->destinationLocation->type == InventoryEnums\LocationType::CUSTOMER;
        }

        return in_array($move->rule_id, $triggeringRuleIds)
            && ($move->finalLocation?->type ?? $move->destinationLocation->type) == InventoryEnums\LocationType::CUSTOMER;
    }

    public function procuredQuantity(OrderLine $line, $previousProductUOMQty = false)
    {
        [$outgoingMoves, $incomingMoves] = $this->outgoingAndIncomingMoves($line, strict: false);

        $quantityIn = fn ($move) => $move->uom->computeQuantity(
            $move->state === InventoryEnums\MoveState::DONE ? $move->quantity : $move->product_uom_qty,
            $line->uom,
            roundingMethod: 'HALF-UP'
        );

        return $outgoingMoves->sum($quantityIn) - $incomingMoves->sum($quantityIn);
    }

    public function buildProcurementOptions(OrderLine $line, $procurementGroup = null): ProcurementOptions
    {
        $deadline = $line->order->commitment_date ?? $line->expected_date;

        // TODO: This value will be set in the configuration
        $datePlanned = $deadline->subDays(0);

        return ProcurementOptions::make()
            ->group($procurementGroup)
            ->linkSaleOrderLine($line->id)
            ->plannedAt($datePlanned)
            ->deadline($deadline)
            ->routes($line->route ? collect([$line->route]) : collect())
            ->warehouse($line->warehouse)
            ->partner($line->order->partner)
            ->company($line->company)
            ->packaging($line->productPackaging)
            ->finalLocation($this->customerLocation());
    }

    public function buildProcurementRequest(
        OrderLine $line,
        $productQty,
        $procurementUom,
        $origin,
        $procurementGroup = null
    ): ProcurementRequest {
        $options = $this->buildProcurementOptions($line, $procurementGroup);

        return new ProcurementRequest(
            product: InventoryProduct::find($line->product_id),
            quantity: $productQty,
            uom: $procurementUom,
            location: $options->destinationLocation(),
            name: $line->product->name,
            origin: $origin,
            company: $line->company,
            options: $options,
        );
    }

    protected function customerLocation(): ?Location
    {
        return Location::where('type', InventoryEnums\LocationType::CUSTOMER)->first();
    }

    public function cancelOperations(Order $record): void
    {
        if (! Package::isPluginInstalled('inventories') || $record->operations->isEmpty()) {
            return;
        }

        $record->operations
            ->filter(fn ($operation) => ! in_array($operation->state, [
                InventoryEnums\OperationState::DONE,
                InventoryEnums\OperationState::CANCELED,
            ]))
            ->each(fn ($operation) => InventoryFacade::cancelTransfer($operation));
    }
}
