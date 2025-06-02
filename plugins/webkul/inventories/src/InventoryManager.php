<?php

namespace Webkul\Inventory;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Rule;
use Webkul\Purchase\Facades\PurchaseOrder as PurchaseOrderFacade;
use Webkul\Sale\Facades\SaleOrder as SaleFacade;
use Webkul\Support\Package;

class InventoryManager
{
    public function checkTransferAvailability(Operation $record): Operation
    {
        return $this->computeTransfer($record);
    }

    public function todoTransfer(Operation $record): Operation
    {
        return $this->computeTransfer($record);
    }

    public function validateTransfer(Operation $record): Operation
    {
        $record = $this->computeTransfer($record);

        // Update each move and its lines, adjusting quantities.
        foreach ($record->moves as $move) {
            $this->validateTransferMove($move);
        }

        $record = $this->computeTransferState($record);

        $record->save();

        if (Package::isPluginInstalled('purchases')) {
            foreach ($record->purchaseOrders as $purchaseOrder) {
                PurchaseOrderFacade::computePurchaseOrder($purchaseOrder);
            }
        }

        if (Package::isPluginInstalled('sales')) {
            if ($record->saleOrder) {
                SaleFacade::computeSaleOrder($record->saleOrder);
            }
        }

        $this->applyPushRules($record);

        return $record;
    }

    public function validateTransferMove(Move $move): Move
    {
        $move->update([
            'state'     => Enums\MoveState::DONE,
            'is_picked' => true,
        ]);

        foreach ($move->lines()->get() as $moveLine) {
            $this->validateTransferMoveLine($moveLine);
        }

        return $move;
    }

    public function validateTransferMoveLine(MoveLine $moveLine): MoveLine
    {
        $moveLine->update(['state' => Enums\MoveState::DONE]);

        // Process source quantity
        $sourceQuantity = ProductQuantity::where('product_id', $moveLine->product_id)
            ->where('location_id', $moveLine->source_location_id)
            ->where('lot_id', $moveLine->lot_id)
            ->where('package_id', $moveLine->package_id)
            ->first();

        if ($sourceQuantity) {
            $remainingQty = $sourceQuantity->quantity - $moveLine->uom_qty;

            if ($remainingQty == 0) {
                $sourceQuantity->delete();
            } else {
                $reservedQty = $this->calculateReservedQty($moveLine->sourceLocation, $moveLine->uom_qty);

                $sourceQuantity->update([
                    'quantity'                => $remainingQty,
                    'reserved_quantity'       => $sourceQuantity->reserved_quantity - $reservedQty,
                    'inventory_diff_quantity' => $sourceQuantity->inventory_diff_quantity + $moveLine->uom_qty,
                ]);
            }
        } else {
            ProductQuantity::create([
                'product_id'              => $moveLine->product_id,
                'location_id'             => $moveLine->source_location_id,
                'lot_id'                  => $moveLine->lot_id,
                'package_id'              => $moveLine->package_id,
                'quantity'                => -$moveLine->uom_qty,
                'inventory_diff_quantity' => $moveLine->uom_qty,
                'company_id'              => $moveLine->sourceLocation->company_id,
                'creator_id'              => Auth::id(),
                'incoming_at'             => now(),
            ]);
        }

        // Process destination quantity
        $destinationQuantity = ProductQuantity::where('product_id', $moveLine->product_id)
            ->where('location_id', $moveLine->destination_location_id)
            ->where('lot_id', $moveLine->lot_id)
            ->where('package_id', $moveLine->result_package_id)
            ->first();

        $reservedQty = $this->calculateReservedQty($moveLine->destinationLocation, $moveLine->uom_qty);

        if ($destinationQuantity) {
            $destinationQuantity->update([
                'quantity'                => $destinationQuantity->quantity + $moveLine->uom_qty,
                'reserved_quantity'       => $destinationQuantity->reserved_quantity + $reservedQty,
                'inventory_diff_quantity' => $destinationQuantity->inventory_diff_quantity - $moveLine->uom_qty,
            ]);
        } else {
            ProductQuantity::create([
                'product_id'              => $moveLine->product_id,
                'location_id'             => $moveLine->destination_location_id,
                'package_id'              => $moveLine->result_package_id,
                'lot_id'                  => $moveLine->lot_id,
                'quantity'                => $moveLine->uom_qty,
                'reserved_quantity'       => $reservedQty,
                'inventory_diff_quantity' => -$moveLine->uom_qty,
                'incoming_at'             => now(),
                'creator_id'              => Auth::id(),
                'company_id'              => $moveLine->destinationLocation->company_id,
            ]);
        }

        // Update package and lot if applicable.
        if ($moveLine->result_package_id && $moveLine->resultPackage) {
            $moveLine->resultPackage->update([
                'location_id' => $moveLine->destination_location_id,
                'pack_date'   => now(),
            ]);
        }

        if ($moveLine->lot_id && $moveLine->lot) {
            $moveLine->lot->update([
                'location_id' => $moveLine->lot->total_quantity >= $moveLine->uom_qty
                    ? $moveLine->destination_location_id
                    : null,
            ]);
        }

        return $moveLine;
    }

    public function cancelTransfer(Operation $record): Operation
    {
        foreach ($record->moves as $move) {
            $move->update([
                'state'        => Enums\MoveState::CANCELED,
                'quantity'     => 0,
            ]);

            $move->lines()->delete();
        }

        $record = $this->computeTransferState($record);

        $record->save();

        return $record;
    }

    public function returnTransfer(Operation $record): Operation
    {
        $newOperation = $record->replicate()->fill([
            'state'                   => Enums\OperationState::DRAFT,
            'origin'                  => 'Return of '.$record->name,
            'operation_type_id'       => $record->operationType->returnOperationType?->id ?? $record->operation_type_id,
            'source_location_id'      => $record->destination_location_id,
            'destination_location_id' => $record->source_location_id,
            'return_id'               => $record->id,
            'user_id'                 => Auth::id(),
            'creator_id'              => Auth::id(),
        ]);

        $newOperation->save();

        foreach ($record->moves as $move) {
            $newMove = $move->replicate()->fill([
                'operation_id'            => $newOperation->id,
                'reference'               => $newOperation->name,
                'state'                   => Enums\MoveState::DRAFT,
                'is_refund'               => true,
                'product_qty'             => $move->product_qty,
                'product_uom_qty'         => $move->product_uom_qty,
                'source_location_id'      => $move->destination_location_id,
                'destination_location_id' => $move->source_location_id,
                'origin_returned_move_id' => $move->id,
                'operation_type_id'       => $newOperation->operation_type_id,
            ]);

            $newMove->save();
        }

        $newOperation->refresh();

        $newOperation = $this->computeTransfer($newOperation);

        if (Package::isPluginInstalled('purchases')) {
            $newOperation->purchaseOrders()->attach($record->purchaseOrders->pluck('id'));
        }

        $url = OperationResource::getUrl('view', ['record' => $record]);

        $newOperation->addMessage([
            'body' => "This transfer has been created from <a href=\"{$url}\" target=\"_blank\" class=\"text-primary-600 dark:text-primary-400\">{$record->name}</a>.",
            'type' => 'comment',
        ]);

        $url = OperationResource::getUrl('view', ['record' => $newOperation]);

        $record->addMessage([
            'body' => "The return <a href=\"{$url}\" target=\"_blank\" class=\"text-primary-600 dark:text-primary-400\">{$newOperation->name}</a> has been created.",
            'type' => 'comment',
        ]);

        return $newOperation;
    }

    /**
     * Process back order for the operation.
     */
    public function createBackOrder(Operation $record): void
    {
        if (! $this->canCreateBackOrder($record)) {
            return;
        }

        $newOperation = $record->replicate()->fill([
            'state'         => Enums\OperationState::DRAFT,
            'origin'        => $record->origin ?? $record->name,
            'back_order_id' => $record->id,
            'user_id'       => Auth::id(),
            'creator_id'    => Auth::id(),
        ]);

        $newOperation->save();

        foreach ($record->moves as $move) {
            if ($move->product_uom_qty <= $move->quantity) {
                continue;
            }

            $remainingQty = round($move->product_uom_qty - $move->quantity, 4);

            $newMove = $move->replicate()->fill([
                'operation_id'    => $newOperation->id,
                'reference'       => $newOperation->name,
                'state'           => Enums\MoveState::DRAFT,
                'product_qty'     => $move->uom->computeQuantity($remainingQty, $move->product->uom, true, 'HALF-UP'),
                'product_uom_qty' => $remainingQty,
                'quantity'        => $remainingQty,
            ]);

            $newMove->save();
        }

        $newOperation->refresh();

        $newOperation = $this->computeTransfer($newOperation);

        if (Package::isPluginInstalled('purchases')) {
            $newOperation->purchaseOrders()->attach($record->purchaseOrders->pluck('id'));

            foreach ($record->purchaseOrders as $purchaseOrder) {
                PurchaseOrderFacade::computePurchaseOrder($purchaseOrder);
            }
        }

        $url = OperationResource::getUrl('view', ['record' => $record]);

        $newOperation->addMessage([
            'body' => "This transfer has been created from <a href=\"{$url}\" target=\"_blank\" class=\"text-primary-600 dark:text-primary-400\">{$record->name}</a>.",
            'type' => 'comment',
        ]);

        $url = OperationResource::getUrl('view', ['record' => $newOperation]);

        $record->addMessage([
            'body' => "The backorder <a href=\"{$url}\" target=\"_blank\" class=\"text-primary-600 dark:text-primary-400\">{$newOperation->name}</a> has been created.",
            'type' => 'comment',
        ]);
    }

    public function computeTransfer(Operation $record): Operation
    {
        if (in_array($record->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED])) {
            return $record;
        }

        foreach ($record->moves as $move) {
            $this->computeTransferMove($move);
        }

        $record = $this->computeTransferState($record);

        $record->save();

        return $record;
    }

    public function computeTransferMove(Move $record): Move
    {
        $lines = $record->lines()->orderBy('created_at')->get();

        if (! is_null($record->quantity)) {
            $remainingQty = $record->uom->computeQuantity($record->quantity, $record->product->uom, true, 'HALF-UP');
        } else {
            $remainingQty = $record->product_qty;
        }

        $updatedLines = collect();

        $availableQuantity = 0;

        $isSupplierSource = $record->sourceLocation->type === Enums\LocationType::SUPPLIER;

        $productQuantities = collect();

        if (! $isSupplierSource) {
            $productQuantities = ProductQuantity::with(['location', 'lot', 'package'])
                ->where('product_id', $record->product_id)
                // Todo: Fix this to handle nesting
                ->whereHas('location', function (Builder $query) use ($record) {
                    $query->where('id', $record->source_location_id)
                        ->orWhere('parent_id', $record->source_location_id);
                })
                ->when(
                    $record->sourceLocation->type != Enums\LocationType::SUPPLIER
                    && $record->product->tracking == Enums\ProductTracking::LOT,
                    fn ($query) => $query->whereNotNull('lot_id')
                )
                ->get();
        }

        foreach ($lines as $line) {
            $currentLocationQty = null;

            if (! $isSupplierSource) {
                $currentLocationQty = $productQuantities
                    ->where('location_id', $line->source_location_id)
                    ->where('lot_id', $line->lot_id)
                    ->where('package_id', $line->package_id)
                    ->first()?->quantity ?? 0;

                if ($currentLocationQty <= 0) {
                    $line->delete();

                    continue;
                }
            }

            if ($remainingQty > 0) {
                $newQty = $isSupplierSource
                    ? min($line->uom_qty, $remainingQty)
                    : min($line->uom_qty, $currentLocationQty, $remainingQty);

                if ($newQty != $line->uom_qty) {
                    $line->update([
                        'qty'     => $record->product->uom->computeQuantity($newQty, $record->uom, true, 'HALF-UP'),
                        'uom_qty' => $newQty,
                        'state'   => Enums\MoveState::ASSIGNED,
                    ]);
                }

                $updatedLines->push($line->source_location_id.'-'.$line->lot_id.'-'.$line->package_id);

                $remainingQty = round($remainingQty - $newQty, 4);

                $availableQuantity += $newQty;
            } else {
                $line->delete();
            }
        }

        if ($remainingQty > 0) {
            if ($isSupplierSource) {
                while ($remainingQty > 0) {
                    $newQty = $remainingQty;

                    if ($record->product->tracking == Enums\ProductTracking::SERIAL) {
                        $newQty = 1;
                    }

                    $record->lines()->create([
                        'qty'                     => $record->product->uom->computeQuantity($newQty, $record->uom, true, 'HALF-UP'),
                        'uom_qty'                 => $newQty,
                        'source_location_id'      => $record->source_location_id,
                        'state'                   => Enums\MoveState::ASSIGNED,
                        'reference'               => $record->reference,
                        'picking_description'     => $record->description_picking,
                        'is_picked'               => $record->is_picked,
                        'scheduled_at'            => $record->scheduled_at,
                        'operation_id'            => $record->operation_id,
                        'product_id'              => $record->product_id,
                        'uom_id'                  => $record->uom_id,
                        'destination_location_id' => $record->destination_location_id,
                        'company_id'              => $record->company_id,
                        'creator_id'              => Auth::id(),
                    ]);

                    $remainingQty = round($remainingQty - $newQty, 4);

                    $availableQuantity += $newQty;
                }
            } else {
                foreach ($productQuantities as $productQuantity) {
                    if ($remainingQty <= 0) {
                        break;
                    }

                    if ($updatedLines->contains($productQuantity->location_id.'-'.$productQuantity->lot_id.'-'.$productQuantity->package_id)) {
                        continue;
                    }

                    if ($productQuantity->quantity <= 0) {
                        continue;
                    }

                    $newQty = min($productQuantity->quantity, $remainingQty);

                    $availableQuantity += $newQty;

                    $record->lines()->create([
                        'qty'                     => $record->product->uom->computeQuantity($newQty, $record->uom, true, 'HALF-UP'),
                        'uom_qty'                 => $newQty,
                        'lot_name'                => $productQuantity->lot?->name,
                        'lot_id'                  => $productQuantity->lot_id,
                        'package_id'              => $productQuantity->package_id,
                        'result_package_id'       => $newQty == $productQuantity->quantity ? $productQuantity->package_id : null,
                        'source_location_id'      => $productQuantity->location_id,
                        'state'                   => Enums\MoveState::ASSIGNED,
                        'reference'               => $record->reference,
                        'picking_description'     => $record->description_picking,
                        'is_picked'               => $record->is_picked,
                        'scheduled_at'            => $record->scheduled_at,
                        'operation_id'            => $record->operation_id,
                        'product_id'              => $record->product_id,
                        'uom_id'                  => $record->uom_id,
                        'destination_location_id' => $record->destination_location_id,
                        'company_id'              => $record->company_id,
                        'creator_id'              => Auth::id(),
                    ]);

                    $remainingQty = round($remainingQty - $newQty, 4);
                }
            }
        }

        $requestedQty = $record->product_qty;

        if ($availableQuantity <= 0) {
            $record->update([
                'state'    => Enums\MoveState::CONFIRMED,
                'quantity' => null,
            ]);

            $record->lines()->update([
                'state' => Enums\MoveState::CONFIRMED,
            ]);
        } elseif ($availableQuantity < $requestedQty) {
            $record->update([
                'state'    => Enums\MoveState::PARTIALLY_ASSIGNED,
                'quantity' => $record->product->uom->computeQuantity($availableQuantity, $record->uom, true, 'HALF-UP'),
            ]);

            $record->lines()->update([
                'state' => Enums\MoveState::PARTIALLY_ASSIGNED,
            ]);
        } else {
            $record->update([
                'state'    => Enums\MoveState::ASSIGNED,
                'quantity' => $record->product->uom->computeQuantity($availableQuantity, $record->uom, true, 'HALF-UP'),
            ]);
        }

        return $record;
    }

    public function computeTransferState(Operation $record): Operation
    {
        $record->refresh();

        if (in_array($record->state, [Enums\OperationState::DONE, Enums\OperationState::CANCELED])) {
            return $record;
        }

        if ($record->moves->every(fn ($move) => $move->state === Enums\MoveState::CONFIRMED)) {
            $record->state = Enums\OperationState::CONFIRMED;
        } elseif ($record->moves->every(fn ($move) => $move->state === Enums\MoveState::DONE)) {
            $record->state = Enums\OperationState::DONE;
        } elseif ($record->moves->every(fn ($move) => $move->state === Enums\MoveState::CANCELED)) {
            $record->state = Enums\OperationState::CANCELED;
        } elseif ($record->moves->contains(fn ($move) => $move->state === Enums\MoveState::ASSIGNED ||
            $move->state === Enums\MoveState::PARTIALLY_ASSIGNED
        )) {
            $record->state = Enums\OperationState::ASSIGNED;
        }

        return $record;
    }

    /**
     * Check if a back order can be processed.
     */
    public function canCreateBackOrder(Operation $record): bool
    {
        if ($record->operationType->create_backorder === Enums\CreateBackorder::NEVER) {
            return false;
        }

        return $record->moves->sum('product_uom_qty') > $record->moves->sum('quantity');
    }

    /**
     * Calculate reserved quantity for a location.
     */
    private function calculateReservedQty($location, $qty): int
    {
        if ($location->type === Enums\LocationType::INTERNAL && ! $location->is_stock_location) {
            return $qty;
        }

        return 0;
    }

    /**
     * Apply push rules for the operation.
     */
    public function applyPushRules(Operation $record): void
    {
        $rules = [];

        foreach ($record->moves as $move) {
            if ($move->origin_returned_move_id) {
                continue;
            }

            $rule = $this->getPushRule($move);

            if (! $rule) {
                continue;
            }

            $ruleId = $rule->id;

            $pushedMove = $this->runPushRule($rule, $move);

            if (! isset($rules[$ruleId])) {
                $rules[$ruleId] = [
                    'rule'  => $rule,
                    'moves' => [$pushedMove],
                ];
            } else {
                $rules[$ruleId]['moves'][] = $pushedMove;
            }
        }

        foreach ($rules as $ruleData) {
            $this->createPushOperation($record, $ruleData['rule'], $ruleData['moves']);
        }
    }

    /**
     * Create a new operation based on a push rule and assign moves to it.
     */
    private function createPushOperation(Operation $record, Rule $rule, array $moves): void
    {
        $newOperation = Operation::create([
            'state'                   => Enums\OperationState::DRAFT,
            'origin'                  => $record->name,
            'operation_type_id'       => $rule->operation_type_id,
            'source_location_id'      => $rule->source_location_id,
            'destination_location_id' => $rule->destination_location_id,
            'scheduled_at'            => now()->addDays($rule->delay),
            'company_id'              => $rule->company_id,
            'user_id'                 => Auth::id(),
            'creator_id'              => Auth::id(),
        ]);

        foreach ($moves as $move) {
            $move->update([
                'operation_id' => $newOperation->id,
                'reference'    => $newOperation->name,
            ]);
        }

        $newOperation->refresh();

        $this->computeTransfer($newOperation);
    }

    /**
     * Run a push rule on a move.
     */
    public function runPushRule(Rule $rule, Move $move)
    {
        if ($rule->auto !== Enums\RuleAuto::MANUAL) {
            return;
        }

        $newMove = $move->replicate()->fill([
            'state'                   => Enums\MoveState::DRAFT,
            'reference'               => null,
            'product_qty'             => $move->uom->computeQuantity($move->quantity, $move->product->uom, true, 'HALF-UP'),
            'product_uom_qty'         => $move->quantity,
            'origin'                  => $move->origin ?? $move->operation->name ?? '/',
            'operation_id'            => null,
            'source_location_id'      => $move->destination_location_id,
            'destination_location_id' => $rule->destination_location_id,
            'final_location_id'       => $move->final_location_id,
            'rule_id'                 => $rule->id,
            'scheduled_at'            => $move->scheduled_at->addDays($rule->delay),
            'company_id'              => $rule->company_id,
            'operation_type_id'       => $rule->operation_type_id,
            'propagate_cancel'        => $rule->propagate_cancel,
            'warehouse_id'            => $rule->warehouse_id,
            'procure_method'          => Enums\ProcureMethod::MAKE_TO_ORDER,
        ]);

        $newMove->save();

        if ($newMove->shouldBypassReservation()) {
            $newMove->update([
                'procure_method' => Enums\ProcureMethod::MAKE_TO_STOCK,
            ]);
        }

        if (! $newMove->sourceLocation->shouldBypassReservation()) {
            $move->moveDestinations()->attach($newMove->id);
        }

        return $newMove;
    }

    /**
     * Traverse up the location tree to find a matching push rule.
     */
    public function getPushRule(Move $move, array $filters = [])
    {
        $foundRule = null;

        $location = $move->destinationLocation;

        $filters['action'] = [Enums\RuleAction::PUSH, Enums\RuleAction::PULL_PUSH];

        while (! $foundRule && $location) {
            $filters['source_location_id'] = $location->id;

            $foundRule = $this->searchPushRule(
                $move->productPackaging,
                $move->product,
                $move->warehouse,
                $filters
            );

            $location = $location->parent;
        }

        return $foundRule;
    }

    /**
     * Search for a push rule based on the provided filters.
     */
    public function searchPushRule($productPackaging, $product, $warehouse, array $filters)
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
