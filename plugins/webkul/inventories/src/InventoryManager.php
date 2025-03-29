<?php

namespace Webkul\Inventory;

use Webkul\Inventory\Enums;
use Illuminate\Support\Facades\Auth;
use Webkul\Support\Models\UOM;
use Webkul\Inventory\Models\ProductQuantity;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Operation;
use Webkul\Support\Package;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;

class InventoryManager
{
    public function checkTransferAvailability(Operation $record): Operation
    {
        foreach ($record->moves as $move) {
            $this->updateOrCreateMoveLines($move);
        }

        $record = $this->computeTransferState($record);

        return $record;
    }

    public function todoTransfer(Operation $record): Operation
    {
        foreach ($record->moves as $move) {
            $this->updateOrCreateMoveLines($move);
        }

        $record = $this->computeTransferState($record);

        return $record;
    }

    public function validateTransfer(Operation $record): Operation
    {
        return $record;
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

        foreach ($newOperation->moves as $move) {
            $this->updateOrCreateMoveLines($move);
        }

        $this->computeTransferState($newOperation);

        $record->update(['return_id' => $record->id]);

        if (Package::isPluginInstalled('purchases')) {
            $newOperation->purchaseOrders()->attach($record->return->purchaseOrders->pluck('id'));
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

    public function updateOrCreateMoveLines(Move $record)
    {
        $lines = $record->lines()->orderBy('created_at')->get();

        if (! is_null($record->quantity)) {
            // $remainingQty = static::calculateProductQuantity($record->uom_id, $record->quantity);
            $remainingQty = $record->uom->computeQuantity($record->quantity, $record->product->uom);
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
                        // 'qty'     => static::calculateProductUOMQuantity($record->uom_id, $newQty),
                        'qty'     => $record->product->uom->computeQuantity($newQty, $record->uom),
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
                        // 'qty'                     => static::calculateProductUOMQuantity($record->uom_id, $newQty),
                        'qty'                     => $record->product->uom->computeQuantity($newQty, $record->uom),
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
                        // 'qty'                     => static::calculateProductUOMQuantity($record->uom_id, $newQty),
                        'qty'                     => $record->product->uom->computeQuantity($newQty, $record->uom),
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
                // 'quantity' => static::calculateProductUOMQuantity($record->uom_id, $availableQuantity),
                'quantity' => $record->product->uom->computeQuantity($availableQuantity, $record->uom),
            ]);

            $record->lines()->update([
                'state' => Enums\MoveState::PARTIALLY_ASSIGNED,
            ]);
        } else {
            $record->update([
                'state'    => Enums\MoveState::ASSIGNED,
                // 'quantity' => static::calculateProductUOMQuantity($record->uom_id, $availableQuantity),
                'quantity' => $record->product->uom->computeQuantity($availableQuantity, $record->uom),
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
            $record->update(['state' => Enums\OperationState::CONFIRMED]);
        } elseif ($record->moves->every(fn ($move) => $move->state === Enums\MoveState::DONE)) {
            $record->update(['state' => Enums\OperationState::DONE]);
        } elseif ($record->moves->every(fn ($move) => $move->state === Enums\MoveState::CANCELED)) {
            $record->update(['state' => Enums\OperationState::CANCELED]);
        } elseif ($record->moves->contains(fn ($move) => $move->state === Enums\MoveState::ASSIGNED ||
            $move->state === Enums\MoveState::PARTIALLY_ASSIGNED
        )) {
            $record->update(['state' => Enums\OperationState::ASSIGNED]);
        }

        return $record;
    }
}