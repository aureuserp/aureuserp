<?php

namespace Webkul\Inventory;

use Carbon\Carbon;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Inventory\Enums\GroupPropagation;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\OperationType;
use Webkul\Inventory\Enums\PackageUse;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Enums\RuleAction;
use Webkul\Inventory\Enums\RuleAuto;
use Webkul\Inventory\Events\OperationAssigned;
use Webkul\Inventory\Events\OperationBackOrdered;
use Webkul\Inventory\Events\OperationCanceled;
use Webkul\Inventory\Events\OperationConfirmed;
use Webkul\Inventory\Events\OperationDone;
use Webkul\Inventory\Events\OperationReturned;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\Package as PackageModel;
use Webkul\Inventory\Models\PackageLevel;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Rule;
use Webkul\PluginManager\Package;
use Webkul\Product\Enums\ProductType;
use Webkul\Purchase\Enums as PurchaseOrderEnums;
use Webkul\Purchase\Models\OrderLine as PurchaseOrderLine;
use Webkul\Purchase\Models\PurchaseOrder;

class InventoryManager
{
    public function confirmTransfer(Operation $record, $merge = false): Operation
    {
        $this->confirmMoves($record->moves->filter(fn (Move $move) => $move->state === MoveState::DRAFT));

        $record->refresh();

        $record->computeState();

        $record->save();

        OperationConfirmed::dispatch($record);

        return $record;
    }

    public function assignTransfer(Operation $record): Operation
    {
        if ($record->state === OperationState::DRAFT) {
            $record = $this->confirmTransfer($record);
        }

        $moves = $record->moves->filter(fn (Move $move) => ! in_array($move->state, [MoveState::DRAFT, MoveState::CANCELED, MoveState::DONE]))
            ->sortBy([
                fn (Move $move) => ! (bool) $move->deadline,
                fn (Move $move) => $move->deadline,
                fn (Move $move) => $move->scheduled_at,
                fn (Move $move) => $move->id,
            ]);

        if ($moves->isEmpty()) {
            throw new \Exception(__('inventories::system.inventory-manager.check-availability.no-moves'));
        }

        $this->assignMoves($moves);

        $record->refresh();

        $record->computeState();

        $record->save();

        OperationAssigned::dispatch($record);

        return $record;
    }

    public function doneTransfer(Operation $record, $cancelBackOrder = false): Operation
    {
        $this->checkForErrors($record);

        $todoMoves = $record->moves->filter(fn ($move) => in_array($move->state, [
            MoveState::DRAFT,
            MoveState::WAITING,
            MoveState::PARTIALLY_ASSIGNED,
            MoveState::ASSIGNED,
            MoveState::CONFIRMED,
        ]));

        $hasQuantity = false;

        $hasPick = false;

        foreach ($record->moves as $move) {
            if ($move->quantity) {
                $hasQuantity = true;
            }

            if ($move->is_scraped) {
                continue;
            }

            if ($move->is_picked) {
                $hasPick = true;
            }

            if ($hasQuantity && $hasPick) {
                break;
            }
        }

        if ($hasQuantity && ! $hasPick) {
            $record->moves->each->update(['is_picked' => true]);
        }

        $this->doneMoves($todoMoves, $cancelBackOrder);

        $record->refresh();

        $record->computeState();

        $record->closed_at = Carbon::now();

        $record->save();

        ProductQuantity::deleteZeroQuantities();

        OperationDone::dispatch($record);

        return $record;
    }

    public function cancelTransfer(Operation $record): Operation
    {
        $this->cancelMoves($record->moves);

        $record->computeState();

        $record->save();

        OperationCanceled::dispatch($record);

        return $record;
    }

    public function returnTransfer(Operation $record, array $moveQuantities = []): Operation
    {
        $movesToReturn = Move::whereIn('id', array_keys($moveQuantities))
            ->where('operation_id', $record->id)
            ->get();

        foreach ($movesToReturn as $move) {
            $movesToUnreserve = $move->moveDestinations->filter(fn ($mv) => ! in_array($mv->state, [MoveState::DONE, MoveState::CANCELED]));

            $this->unreserveMoves($movesToUnreserve);
        }

        $newOperation = $record->replicate()
            ->fill($this->prepareReturnOperationValues($record));

        $newOperation->save();

        foreach ($movesToReturn as $move) {
            $values = $this->prepareReturnMoveValues($newOperation, $move, $moveQuantities[$move->id]);

            $newMove = $move->replicate()
                ->fill($values);

            $newMove->save();

            $moveOriginToLink = $move->moveDestinations->flatMap->returnedMoves;

            $moveOriginToLink = $moveOriginToLink->merge(collect([$move]));

            $moveOriginToLink = $moveOriginToLink->merge(
                $move->moveDestinations
                    ->filter(fn ($move) => $move->state !== MoveState::CANCELED)
                    ->flatMap->moveOrigins
                    ->filter(fn ($move) => $move->state !== MoveState::CANCELED)
            );

            $moveDestinationToLink = $move->moveOrigins->flatMap->returnedMoves;

            $moveDestinationToLink = $moveDestinationToLink->merge(
                $move->moveOrigins->flatMap->returnedMoves
                    ->flatMap->moveOrigins
                    ->filter(fn ($move) => $move->state !== MoveState::CANCELED)
                    ->flatMap->moveDestinations
                    ->filter(fn ($move) => $move->state !== MoveState::CANCELED)
            );

            $newMove->moveOrigins()->syncWithoutDetaching($moveOriginToLink->pluck('id')->all());

            $newMove->moveDestinations()->syncWithoutDetaching($moveDestinationToLink->pluck('id')->all());
        }

        $newOperation->refresh();

        $newOperation = $this->assignTransfer($newOperation);

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

        OperationReturned::dispatch($record);

        return $newOperation;
    }

    public function confirmMoves($moves, $merge = true, $mergeInto = null, $bypassEntirePack = true)
    {
        if ($moves->isEmpty()) {
            return collect();
        }

        $movesToCreateProcurement = collect();

        $movesToConfirm = collect();

        $movesWaiting = collect();

        $movesToAssign = [];

        foreach ($moves as $move) {
            if ($move->state != MoveState::DRAFT) {
                continue;
            }

            if ($move->moveOrigins->isNotEmpty()) {
                $movesWaiting->push($move);
            } elseif ($move->procure_method === ProcureMethod::MAKE_TO_ORDER) {
                $movesWaiting->push($move);

                $movesToCreateProcurement->push($move);
            } elseif ($move->rule?->procure_method === ProcureMethod::MTS_ELSE_MTO) {
                $movesToCreateProcurement->push($move);

                $movesToConfirm->push($move);
            } else {
                $movesToConfirm->push($move);
            }

            if ($move->shouldBeAssigned()) {
                $key = implode('_', $move->keyAssignOperation());

                $movesToAssign[$key][] = $move;
            }
        }

        $procurements = collect();

        $quantities = $this->prepareProcurementQty($movesToCreateProcurement);

        foreach ($movesToCreateProcurement->zip($quantities) as [$move, $quantity]) {
            $values = $move->prepareProcurementValues();

            $origin = $move->prepareProcurementOrigin();

            $procurements->push([
                'product'     => $move->product,
                'product_qty' => $quantity,
                'product_uom' => $move->uom,
                'location'    => $move->sourceLocation,
                'name'        => $move->rule?->name ?? '/',
                'origin'      => $origin,
                'company'     => $move->company,
                'values'      => $values,
            ]);
        }

        $this->runProcurements($procurements);

        $movesToConfirm->each(fn (Move $move) => $move->update(['state' => MoveState::CONFIRMED]));

        $movesWaiting->each(fn (Move $move) => $move->update(['state' => MoveState::WAITING]));

        $movesToConfirm->merge($movesWaiting)
            ->filter(fn ($move) => $move->operationType?->reservation_method === ReservationMethod::AT_CONFIRM)
            ->each(fn (Move $move) => $move->update(['reservation_date' => now()]));

        foreach ($movesToAssign as $movesGroup) {
            $this->assignOperation(collect($movesGroup));
        }

        if ($merge) {
            $moves = $this->mergeMoves($moves, mergeInto: $mergeInto)
                ->map(fn ($move) => Move::find($move->id))
                ->filter();
        }

        $negReturnMoves = $moves->filter(
            fn (Move $move) => float_compare($move->product_uom_qty, 0, precisionRounding: $move->uom->rounding) < 0
        );

        $negToPush = $negReturnMoves->filter(
            fn ($move) => $move->final_location_id && $move->destination_location_id !== $move->final_location_id
        );

        $newPushMoves = collect();

        if ($negToPush->isNotEmpty()) {
            $newPushMoves = $this->applyPushRules($negToPush);
        }

        foreach ($negReturnMoves as $move) {
            [$move->source_location_id, $move->destination_location_id, $move->final_location_id] = [
                $move->destination_location_id,
                $move->source_location_id,
                $move->source_location_id,
            ];

            $originMoveIds = [];

            $destinationMoveIds = [];

            foreach ($move->moveOrigins->merge($move->moveDestinations) as $relatedMove) {
                $fromLocationId = $relatedMove->source_location_id;

                $toLocationId = $relatedMove->destination_location_id;

                if (float_compare($relatedMove->product_uom_qty, 0, precisionRounding: $relatedMove->uom->rounding) < 0) {
                    [$fromLocationId, $toLocationId] = [$toLocationId, $fromLocationId];
                }

                if ($toLocationId === $move->source_location_id) {
                    $originMoveIds[] = $relatedMove->id;
                } elseif ($move->destination_location_id === $fromLocationId) {
                    $destinationMoveIds[] = $relatedMove->id;
                }
            }

            $move->moveOrigins()->sync($originMoveIds);

            $move->moveDestinations()->sync($destinationMoveIds);

            $move->product_uom_qty *= -1;

            if ($move->operationType->return_operation_type_id) {
                $move->operation_type_id = $move->operationType->return_operation_type_id;
            }

            $move->procure_method = ProcureMethod::MAKE_TO_STOCK;
            $move->save();
        }

        $this->assignOperation($negReturnMoves);

        $movesToAssign = $moves->filter(fn ($move) => in_array($move->state, [MoveState::CONFIRMED, MoveState::PARTIALLY_ASSIGNED])
            && (
                $move->shouldBypassReservation()
                || $move->operationType->reservation_method === ReservationMethod::AT_CONFIRM
                || ($move->reservation_date && $move->reservation_date <= now()->toDateString())
            )
        );

        $this->assignMoves($movesToAssign);

        if ($newPushMoves->isNotEmpty()) {
            $negPushMoves = $newPushMoves->filter(
                fn ($move) => float_compare($move->product_uom_qty, 0, precisionRounding: $move->uom->rounding) < 0
            );

            $this->confirmMoves($newPushMoves->diff($negPushMoves));

            $this->confirmMoves(
                $negPushMoves,
                mergeInto: $negPushMoves->flatMap->moveOrigins->flatMap->moveDestinations
            );
        }

        return $moves;
    }

    public function assignMoves($moves, mixed $forceQty = false)
    {
        if ($moves->isEmpty()) {
            return;
        }

        $assignedMovesIds = collect();

        $partiallyAssignedMovesIds = collect();

        $reservedAvailability = $moves->mapWithKeys(fn ($move) => [$move->id => $move->quantity]);

        $roundings = $moves->mapWithKeys(fn ($move) => [$move->id => $move->product->uom->rounding]);

        $moveLineValsList = collect();

        $movesToRedirect = collect();

        $movesToAssign = $moves;

        if (! $forceQty) {
            $movesToAssign = $movesToAssign->filter(
                fn ($move) => ! $move->is_picked && in_array($move->state, [MoveState::CONFIRMED, MoveState::WAITING, MoveState::PARTIALLY_ASSIGNED])
            );
        }

        $movesMto = $movesToAssign->filter(fn ($move) => $move->moveOrigins->isNotEmpty() && ! $move->shouldBypassReservation());

        $quantityCache = ProductQuantity::getQuantitiesByProductsLocations($movesMto->pluck('product_id'), $movesMto->pluck('source_location_id'));

        foreach ($movesToAssign as $move) {
            $rounding = $roundings[$move->id];

            $missingReservedUomQuantity = ! $forceQty
                ? $move->product_uom_qty - $reservedAvailability[$move->id]
                : $forceQty;

            if (float_compare($missingReservedUomQuantity, 0, precisionRounding: $rounding) <= 0) {
                $assignedMovesIds->push($move->id);

                continue;
            }

            $missingReservedQuantity = $move->uom->computeQuantity(
                $missingReservedUomQuantity,
                $move->product->uom,
                roundingMethod: 'HALF-UP'
            );

            if ($move->shouldBypassReservation()) {
                if ($move->moveOrigins->isNotEmpty()) {
                    $availableMoveLines = $move->getAvailableMoveLines($assignedMovesIds, $partiallyAssignedMovesIds);

                    foreach ($availableMoveLines as $key => $quantity) {
                        $keyValues = explode('_', $key);

                        $locationId = $keyValues[0];

                        $lotId = $keyValues[1] ?: null;

                        $packageId = $keyValues[2] ?: null;

                        $qtyAdded = min($missingReservedQuantity, $quantity);

                        $moveLineVals = $move->prepareLineValues($qtyAdded);

                        $moveLineVals += [
                            'source_location_id' => $locationId,
                            'lot_id'             => $lotId,
                            'lot_name'           => $lotId ? Lot::find($lotId)?->name : null,
                            'package_id'         => $packageId,
                        ];

                        $moveLineValsList->push($moveLineVals);

                        $missingReservedQuantity -= $qtyAdded;

                        if (float_is_zero($missingReservedQuantity, precisionRounding: $move->product->uom->rounding)) {
                            break;
                        }
                    }
                }

                if (
                    $missingReservedQuantity
                    && $move->product->tracking === ProductTracking::SERIAL
                    && (
                        $move->operationType->use_create_lots
                        || $move->operationType->use_existing_lots
                    )
                ) {
                    for ($i = 0; $i < (int) $missingReservedQuantity; $i++) {
                        $moveLineValsList->push($move->prepareLineValues(quantity: 1));
                    }
                } elseif ($missingReservedQuantity) {
                    $toUpdate = $move->lines->filter(
                        fn ($ml) => $ml->uom_id === $move->uom_id
                            && $ml->source_location_id === $move->source_location_id
                            && $ml->destination_location_id === $move->destination_location_id
                            && $ml->operation_id === $move->operation_id
                            && ! $ml->is_picked
                            && ! $ml->lot_id
                            && ! $ml->result_package_id
                            && ! $ml->package_id
                    );

                    if ($toUpdate->isNotEmpty()) {
                        $toUpdate->first()->update([
                            'qty' => $toUpdate->first()->qty + $move->product->uom->computeQuantity(
                                $missingReservedQuantity,
                                $move->uom,
                                roundingMethod: 'HALF-UP'
                            ),
                        ]);
                    } else {
                        $moveLineValsList->push($move->prepareLineValues(quantity: $missingReservedQuantity));
                    }
                }

                $assignedMovesIds->push($move->id);

                $movesToRedirect->push($move->id);
            } else {
                if (float_is_zero($move->product_uom_qty, precisionRounding: $move->uom->rounding) && ! $forceQty) {
                    $assignedMovesIds->push($move->id);
                } elseif ($move->moveOrigins->isEmpty()) {
                    if ($move->procure_method === ProcureMethod::MAKE_TO_ORDER) {
                        continue;
                    }

                    $need = $missingReservedQuantity;

                    if (float_is_zero($need, precisionRounding: $rounding)) {
                        $assignedMovesIds->push($move->id);

                        continue;
                    }

                    $forcedPackage = $move->packageLevel?->package;

                    $takenQuantity = $move->updateReservedQuantity($need, $move->sourceLocation, package: $forcedPackage, strict: false);

                    if (float_is_zero($takenQuantity, precisionRounding: $rounding)) {
                        continue;
                    }

                    $movesToRedirect->push($move->id);

                    if (float_compare($need, $takenQuantity, precisionRounding: $rounding) === 0) {
                        $assignedMovesIds->push($move->id);
                    } else {
                        $partiallyAssignedMovesIds->push($move->id);
                    }
                } else {
                    $availableMoveLines = $move->getAvailableMoveLines($assignedMovesIds, $partiallyAssignedMovesIds);

                    if (empty($availableMoveLines)) {
                        continue;
                    }

                    foreach ($move->lines->filter(fn ($ml) => $ml->uom_qty) as $moveLine) {
                        $key = implode('_', [$moveLine->source_location_id, $moveLine->lot_id, $moveLine->package_id]);

                        if (isset($availableMoveLines[$key])) {
                            $availableMoveLines[$key] -= $moveLine->uom_qty;
                        }
                    }

                    foreach ($availableMoveLines as $key => $quantity) {
                        $keyValues = explode('_', $key);

                        $location = Location::find($keyValues[0]);

                        $location = $keyValues[0] ? Location::find($keyValues[0]) : null;

                        $lot = $keyValues[1] ? Lot::find($keyValues[1]) : null;

                        $package = $keyValues[2] ? PackageModel::find($keyValues[2]) : null;

                        $need = $move->product_qty - $move->lines->sum('uom_qty');

                        $takenQuantity = $move
                            ->setContext([
                                'quantity_cache' => $quantityCache,
                            ])->updateReservedQuantity(
                                min($quantity, $need),
                                $location,
                                $lot,
                                $package
                            );

                        if (float_is_zero($takenQuantity, precisionRounding: $rounding)) {
                            continue;
                        }

                        $movesToRedirect->push($move->id);

                        if (float_is_zero($need - $takenQuantity, precisionRounding: $rounding)) {
                            $assignedMovesIds->push($move->id);

                            break;
                        }

                        $partiallyAssignedMovesIds->push($move->id);
                    }
                }
            }

            if ($move->product->tracking === ProductTracking::SERIAL) {
                $move->update(['next_serial_count' => $move->product_uom_qty]);
            }
        }

        $moveLineValsList->each(fn ($vals) => MoveLine::create($vals));

        Move::whereIn('id', $partiallyAssignedMovesIds)->get()->each(fn ($move) => $move->update(['state' => MoveState::PARTIALLY_ASSIGNED]));

        Move::whereIn('id', $assignedMovesIds)->get()->each(fn ($move) => $move->update(['state' => MoveState::ASSIGNED]));

        foreach ($moves as $move){
            $this->checkForEntirePack($move->operation);
        }

        $moveLines = Move::whereIn('id', $movesToRedirect)
            ->with('lines')
            ->get()
            ->flatMap(fn ($move) => $move->lines);

        $this->applyPutawayStrategy($moveLines);
    }

    public function doneMoves($moves, $cancelBackOrder = false)
    {
        $confirmedMoves = $moves->filter(
            fn ($move) => $move->state === MoveState::DRAFT
                || float_is_zero($move->product_uom_qty, precisionRounding: $move->uom->rounding)
        );

        $newMoves = $this->confirmMoves($confirmedMoves, merge: false);

        $moves = $moves
            ->merge($newMoves)
            ->filter(fn ($move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED]));

        $moveLineIdsToDelete = collect();

        foreach ($moves as $move) {
            if ($move->is_picked) {
                $moveLineIdsToDelete = $moveLineIdsToDelete->merge(
                    $move->lines->filter(fn ($ml) => ! $ml->is_picked)->pluck('id')
                );
            }

            if (
                (
                    $move->quantity <= 0
                    || ! $move->is_picked
                )
                && ! $move->is_inventory
            ) {
                if (
                    float_is_zero($move->product_uom_qty, precisionRounding: $move->uom->rounding)
                    || $cancelBackOrder
                ) {
                    $this->cancelMoves(collect([$move]));
                }
            }
        }

        MoveLine::whereIn('id', $moveLineIdsToDelete)->get()->each(fn ($ml) => $ml->delete());

        $movesTodo = $moves->filter(
            fn ($move) => ! (
                $move->state === MoveState::CANCELED
                || ($move->quantity <= 0 && ! $move->is_inventory)
                || ! $move->is_picked
            )
        );

        if (! $cancelBackOrder) {
            $this->createMovesBackOrder($movesTodo);
        }

        $this->doneMoveLines($movesTodo->flatMap->lines->sortBy('id'));

        $resultPackages = $movesTodo->flatMap->lines
            ->filter(fn ($moveLine) => $moveLine->is_picked)
            ->pluck('resultPackage')
            ->filter()
            ->unique('id')
            ->filter(fn ($package) => $package->quantities->count() > 1);

        foreach ($resultPackages as $resultPackage) {
            $locationCount = $resultPackage->quantities
                ->filter(fn($quantity) => float_compare($quantity->quantity, 0.0, precisionRounding: $quantity->uom->rounding) > 0)
                ->pluck('location_id')
                ->unique()
                ->count();

            if ($locationCount > 1) {
                throw new \Exception(__('inventories::filament/clusters/operations/actions/validate.notification.warning.partial-package.body'));
            }
        }

        if ($movesTodo->flatMap->lines->some(fn ($moveLine) => $moveLine->package_id && $moveLine->package_id === $moveLine->result_package_id)) {
            ProductQuantity::deleteZeroQuantities();
        }

        $operation = $movesTodo->pluck('operation')->unique()->filter()->first();

        $movesTodo->each->update([
            'state' => MoveState::DONE,
            'date'  => now(),
        ]);

        $movesToPush = $movesTodo->filter(fn ($move) => ! $move->skipPush());

        if ($movesToPush->isNotEmpty()) {
            $this->applyPushRules($movesToPush);
        }

        $moveDestinationsPerCompany = [];

        $movesTodo->load('moveDestinations');

        foreach ($movesTodo->flatMap->moveDestinations as $moveDestination) {
            $moveDestinationsPerCompany[$moveDestination->company_id][] = $moveDestination;
        }

        foreach ($moveDestinationsPerCompany as $moveDestinations) {
            $this->assignMoves(collect($moveDestinations));
        }

        if ($operation && ! $cancelBackOrder) {
            $backOrder = $this->createBackOrder($operation);

            if ($backOrder?->moves->some(fn ($move) => $move->state === MoveState::ASSIGNED)) {
                $this->checkForEntirePack($backOrder);
            }
        }

        if ($movesTodo->isNotEmpty()) {
            $movesTodo->each(fn ($move) => $move->checkQuantity());
        }

        return $movesTodo;
    }

    public function cancelMoves($moves)
    {
        if ($moves->some(fn ($move) => $move->state === MoveState::DONE && ! $move->is_scraped)) {
            throw new \Exception(__('inventories::system.inventory-manager.cancel-move.already-done'));
        }

        $movesToCancel = $moves->filter(
            fn ($move) => $move->state !== MoveState::CANCELED
                && ! ($move->state === MoveState::DONE && $move->is_scraped)
        );

        $movesToCancel->each->update(['is_picked' => false]);

        $this->unreserveMoves($movesToCancel);

        $cancelMovesOrigin = false;

        $movesToCancel->each->update(['state' => MoveState::CANCELED]);

        foreach ($movesToCancel as $move) {
            $siblingsStates = $move->moveDestinations
                ->flatMap
                ->moveOrigins
                ->diff(collect([$move]))
                ->pluck('state');

            if ($move->propagate_cancel) {
                if ($siblingsStates->every(fn ($state) => $state === MoveState::CANCELED)) {
                    $this->cancelMoves(
                        $move->moveDestinations->filter(
                            fn ($move) => $move->state !== MoveState::DONE &&
                                $move->destination_location_id === $move->moveDestinations->first()?->source_location_id
                        )
                    );

                    if ($cancelMovesOrigin) {
                        $this->cancelMoves($move->moveOrigins->filter(fn ($move) => $move->state !== MoveState::DONE));
                    }
                }
            } else {
                if ($siblingsStates->every(fn ($state) => in_array($state, [MoveState::DONE, MoveState::CANCELED]))) {
                    $move->moveDestinations->each(function ($destMove) use ($move) {
                        $destMove->update(['procure_method' => ProcureMethod::MAKE_TO_STOCK]);

                        $destMove->moveOrigins()->detach($move->id);
                    });
                }
            }
        }

        $movesToCancel->each(function ($move) {
            $move->update(['procure_method' => ProcureMethod::MAKE_TO_STOCK]);

            $move->moveOrigins()->detach();
        });

        return true;
    }

    public function unreserveMoves($moves)
    {
        $movesToUnreserve = $moves->filter(function ($move) {
            if (
                $move->state === MoveState::CANCELED
                || ($move->state === MoveState::DONE && $move->is_scraped)
                || $move->is_picked
            ) {
                return false;
            }

            if ($move->state === MoveState::DONE) {
                throw new \Exception(__('inventories::system.inventory-manager.unreserve-move.already-done'));
            }

            return true;
        });

        $moveLineToUnlink = collect();

        $movesNotToRecompute = collect();

        foreach ($movesToUnreserve->flatMap->lines as $moveLine) {
            if ($moveLine->is_picked) {
                $movesNotToRecompute->push($moveLine->move_id);

                continue;
            }

            $moveLineToUnlink->push($moveLine->id);
        }

        MoveLine::whereIn('id', $moveLineToUnlink)->get()->each->delete();

        $movesToUnreserve
            ->filter(fn ($move) => ! $movesNotToRecompute->contains('id', $move->id))
            ->each(function ($move) {
                $move->computeQuantity();

                $move->computeState();

                $move->saveQuietly();
            });

        return true;
    }

    public function doneMoveLines($moveLines)
    {
        $moveLineIdsTrackedWithoutLot = collect();

        $moveLineIdsToDelete = collect();

        $moveLineIdsToCreateLot = collect();

        $moveLineIdsToCheck = [];

        foreach ($moveLines as $moveLine) {
            $uomQty = float_round($moveLine->qty, precisionRounding: $moveLine->uom->rounding, roundingMethod: 'HALF-UP');

            $quantity = float_round($moveLine->qty, precisionDigits: 2, roundingMethod: 'HALF-UP');

            if (float_compare($uomQty, $quantity, precisionDigits: 2) !== 0) {
                throw new \Exception(__('inventories::system.inventory-manager.validate.quantity-rounding-mismatch', [
                    'product' => $moveLine->product->name,
                    'unit'    => $moveLine->uom->name,
                ]));
            }

            $qtyDoneFloatCompared = float_compare($moveLine->qty, 0, precisionRounding: $moveLine->uom->rounding);

            if ($qtyDoneFloatCompared > 0) {
                if ($moveLine->product->tracking === ProductTracking::QTY) {
                    continue;
                }

                $operationType = $moveLine->move->operationType;

                if (! $operationType && ! $moveLine->is_inventory && ! $moveLine->lot_id) {
                    $moveLineIdsTrackedWithoutLot->push($moveLine->id);

                    continue;
                }

                if (! $operationType || $moveLine->lot_id || (! $operationType->use_create_lots && ! $operationType->use_existing_lots)) {
                    continue;
                }

                if ($operationType->use_create_lots) {
                    $key = $moveLine->product_id.'_'.$moveLine->company_id;

                    $moveLineIdsToCheck[$key][] = $moveLine->id;
                } else {
                    $moveLineIdsTrackedWithoutLot->push($moveLine->id);
                }
            } elseif ($qtyDoneFloatCompared < 0) {
                throw new \Exception(__('inventories::system.inventory-manager.validate.no-negative-quantities'));
            } elseif (! $moveLine->is_inventory) {
                $moveLineIdsToDelete->push($moveLine->id);
            }
        }

        foreach ($moveLineIdsToCheck as $key => $moveLineIds) {
            [$productId, $companyId] = explode('_', $key);

            $lines = MoveLine::whereIn('id', $moveLineIds)->get();

            $lotNames = $lines->pluck('lot_name')->filter()->all();

            $lots = Lot::where(function ($q) use ($companyId) {
                $q->whereNull('company_id')->orWhere('company_id', $companyId);
            })
                ->where('product_id', $productId)
                ->whereIn('name', $lotNames)
                ->get()
                ->keyBy('name');

            foreach ($lines as $moveLine) {
                $lot = $lots->get($moveLine->lot_name);

                if ($lot) {
                    $moveLine->update(['lot_id' => $lot->id]);
                } elseif ($moveLine->lot_name) {
                    $moveLineIdsToCreateLot->push($moveLine->id);
                } else {
                    $moveLineIdsTrackedWithoutLot->push($moveLine->id);
                }
            }
        }

        if ($moveLineIdsTrackedWithoutLot->isNotEmpty()) {
            $productNames = MoveLine::whereIn('id', $moveLineIdsTrackedWithoutLot)
                ->get()
                ->pluck('product.name')
                ->map(fn ($name) => "- $name")
                ->implode("\n");

            throw new \Exception(__('inventories::system.inventory-manager.validate.missing-lot-serial-number', [
                'products' => $productNames,
            ]));
        }

        if ($moveLineIdsToCreateLot->isNotEmpty()) {
            MoveLine::whereIn('id', $moveLineIdsToCreateLot)->get()->each->createAndAssignProductionLot();
        }

        MoveLine::whereIn('id', $moveLineIdsToDelete)->get()->each(fn ($moveLine) => $moveLine->delete());

        $moveLinesTodo = $moveLines->filter(fn ($moveLine) => ! $moveLineIdsToDelete->contains($moveLine->id));

        $moveLineIdsToIgnore = collect();

        $quantityCache = ProductQuantity::getQuantitiesByProductsLocations(
            $moveLinesTodo->pluck('product_id'),
            $moveLinesTodo->pluck('source_location_id')->merge($moveLinesTodo->pluck('destination_location_id'))->unique(),
            extraFilters: [['lot_id', 'in', $moveLinesTodo->pluck('lot_id')->filter()->all()], ['lot_id', '=', null]],
        );


        foreach ($moveLinesTodo as $moveLine) {
            $moveLine->refresh();
            
            $moveLine->setContext([
                'quantity_cache' => $quantityCache,
            ]);

            $moveLine->synchronizeQuantity(
                -$moveLine->uom_qty,
                $moveLine->sourceLocation,
                action: 'reserved'
            );

            [$availableQty, $incomingDate] = $moveLine->synchronizeQuantity(-$moveLine->uom_qty, $moveLine->sourceLocation);

            $moveLine->synchronizeQuantity(
                $moveLine->uom_qty,
                $moveLine->destinationLocation,
                incomingDate: $incomingDate,
                values: [
                    'package' => $moveLine->resultPackage,
                ]
            );

            if ($availableQty < 0) {
                $moveLine->freeReservation(
                    product: $moveLine->product,
                    location: $moveLine->sourceLocation,
                    quantity: abs($availableQty),
                    lot: $moveLine->lot,
                    package: $moveLine->package,
                    moveLineIdsToIgnore: $moveLineIdsToIgnore,
                );
            }

            $moveLineIdsToIgnore->push($moveLine->id);
        }

        $moveLinesTodo->each->update(['scheduled_at' => now()]);
    }

    public function mergeMoves($moves, $mergeInto = null)
    {
        $candidateMovesSet = [];

        $moves->each(fn ($move) => $move->load('operation'));

        if (! $mergeInto) {
            $operations = $moves
                ->map(fn ($move) => $move->operation)
                ->filter()
                ->unique('id');

            foreach ($operations as $operation) {
                $candidateMovesSet[$operation->id] = $operation->moves;
            }
        } else {
            $candidateMovesSet = array_merge($mergeInto, $moves->toArray());
        }

        $distinctFields = [
            'product_id',
            // 'price_unit',
            'procure_method',
            'source_location_id',
            'destination_location_id',
            'final_location_id',
            'uom_id',
            'restrict_partner_id',
            'origin_returned_move_id',
            'package_level_id',
            'description_picking',
            'product_packaging_id',
        ];

        $movesToDelete = collect();

        $mergedMoves = collect();

        $movesToCancel = collect();

        $movesByNegKey = collect();

        $negQtyMoves = $moves->filter(fn ($move) => float_compare($move->product_qty, 0.0, precisionRounding: $move->uom->rounding) < 0)
            ->each(function ($move) {
                $move->operation_id = null;
            });

        $negKeyFields = array_values(array_diff($distinctFields, ['description_picking', 'price_unit']));

        $negKey = fn ($move) => collect($negKeyFields)
            ->map(fn ($field) => $move->$field instanceof \BackedEnum ? $move->$field->value : (string) $move->$field)
            ->implode('_');

        $priceUnitPrecision = 2;

        foreach ($candidateMovesSet as $candidateMoves) {
            $candidateMoves = $candidateMoves->filter(fn ($move) => ! in_array($move->state, [
                MoveState::DRAFT,
                MoveState::DONE,
                MoveState::CANCELED,
            ]))
                ->diff($negQtyMoves);

            $distinctKey = fn ($move) => collect($distinctFields)
                ->map(fn ($field) => $move->$field instanceof \BackedEnum ? $move->$field->value : (string) $move->$field)
                ->implode('_');

            foreach ($candidateMoves->groupBy($distinctKey) as $group) {
                if ($group->count() > 1) {
                    $group->flatMap->lines->each->update(['move_id' => $group->first()->id]);

                    $mergeExtra = (bool) $mergeInto;

                    ['move_destinations' => $destinations, 'move_origins' => $origins] = $fields = $this->mergeMoveValues($group, $mergeExtra);

                    $values = collect($fields)->except(['move_destinations', 'move_origins'])->all();

                    $group->first()->update($values);

                    $group->first()->moveDestinations()->sync($destinations->pluck('id')->all());

                    $group->first()->moveOrigins()->sync($origins->pluck('id')->all());

                    $movesToDelete = $movesToDelete->merge($group->skip(1));

                    $mergedMoves = $mergedMoves->merge([$group->first()]);
                }

                $negKeyValue = $negKey($group->first());

                $movesByNegKey->put(
                    $negKeyValue,
                    $movesByNegKey->has($negKeyValue)
                        ? $movesByNegKey->get($negKeyValue)->push($group->first())
                        : collect([$group->first()])
                );
            }
        }

        foreach ($negQtyMoves as $negMove) {
            foreach ($movesByNegKey->get($negKey($negMove), collect()) as $posMove) {
                if (float_compare($posMove->price_unit, $negMove->price_unit, precisionDigits: 2) == 0) {
                    $newTotalValue = $posMove->product_qty * $posMove->price_unit + $negMove->product_qty * $negMove->price_unit;

                    if (float_compare($posMove->product_uom_qty, abs($negMove->product_uom_qty), precisionRounding: $posMove->uom->rounding) >= 0) {
                        $posMove->product_uom_qty += $negMove->product_uom_qty;

                        $posMove->product_qty += $negMove->product_qty;

                        $moveDestinationIds = $negMove->moveDestinations
                            ->filter(fn ($move) => $move->source_location_id === $posMove->destination_location_id)
                            ->pluck('id')
                            ->all();

                        $moveOriginIds = $negMove->moveOrigins
                            ->filter(fn ($move) => $move->destination_location_id === $posMove->source_location_id)
                            ->pluck('id')
                            ->all();

                        $posMove->update([
                            'price_unit' => $posMove->product_qty
                                ? round($newTotalValue / $posMove->product_qty, $priceUnitPrecision)
                                : 0,
                        ]);

                        $posMove->moveDestinations()->syncWithoutDetaching($moveDestinationIds);

                        $posMove->moveOrigins()->syncWithoutDetaching($moveOriginIds);

                        $mergedMoves->push($posMove);

                        $movesToDelete->push($negMove);

                        if (float_is_zero($posMove->product_uom_qty, precisionRounding: $posMove->uom->rounding)) {
                            $movesToCancel->push($posMove);
                        }

                        break;
                    }

                    $negMove->product_qty += $posMove->product_qty;

                    $negMove->product_uom_qty += $posMove->product_uom_qty;

                    $negMove->price_unit = round($newTotalValue / $negMove->product_qty, $priceUnitPrecision);

                    $posMove->product_uom_qty = 0;

                    $posMove->save();

                    $movesToCancel->push($posMove);
                }
            }
        }

        if ($movesToDelete->isNotEmpty()) {
            $this->cancelMoves($movesToDelete);

            foreach ($movesToDelete as $move) {
                foreach ($move->lines()->get() as $line) {
                    $line->delete();
                }

                $move->delete();
            }

            $movesToDelete->each->delete();
        }

        if ($movesToCancel->isNotEmpty()) {
            $this->cancelMoves($movesToCancel->filter(fn ($move) => ! $move->is_picked));
        }

        return $moves->merge($mergedMoves)->reject(fn ($move) => $movesToDelete->contains('id', $move->id));
    }

    public function applyPutawayStrategy(mixed $moveLines): void
    {
        $groupedByPackage = $moveLines->groupBy(fn ($moveLine) => $moveLine->result_package_id);

        foreach ($groupedByPackage as $packageId => $packageMoveLines) {
            $excludedMoveLines = $packageMoveLines->pluck('id')->all();

            $package = null;

            if ($packageId) {
                $package = PackageModel::find($packageId);
            }

            if ($package?->package_type_id) {
                $bestLocation = $packageMoveLines->first()->move->destinationLocation
                    ->getPutawayStrategy(
                        product: null,
                        package: $package,
                        excludeMoveLineIds: $excludedMoveLines,
                        products: $packageMoveLines->pluck('product')->all()
                    );

                $packageMoveLines->each(function ($moveLine) use ($bestLocation) {
                    $moveLine->update(['destination_location_id' => $bestLocation->id]);

                    $moveLine->packageLevel?->update(['destination_location_id' => $bestLocation->id]);
                });
            } elseif ($package) {
                $usedLocations = collect();

                foreach ($packageMoveLines as $moveLine) {
                    if ($usedLocations->count() > 1) {
                        break;
                    }

                    $location = $moveLine->move->destinationLocation
                        ->getPutawayStrategy(
                            product: $moveLine->product,
                            quantity: $moveLine->qty,
                            excludeMoveLineIds: $excludedMoveLines,
                        );

                    $moveLine->update(['destination_location_id' => $location->id]);

                    $excludedMoveLines = array_diff($excludedMoveLines, [$moveLine->id]);

                    $usedLocations->push($location->id);
                }

                if ($usedLocations->unique()->count() > 1) {
                    $packageMoveLines->groupBy('move_id')->each(function ($groupedMoveLines, $moveId) {
                        $move = Move::find($moveId);

                        $groupedMoveLines->each->update(['destination_location_id' => $move->destination_location_id]);
                    });
                } else {
                    $packageMoveLines->each(function ($moveLine) {
                        $moveLine->packageLevel?->update(['destination_location_id' => $moveLine->destination_location_id]);
                    });
                }
            } else {
                foreach ($packageMoveLines as $moveLine) {
                    $location = $moveLine->move->destinationLocation
                        ->getPutawayStrategy(
                            product: $moveLine->product,
                            quantity: $moveLine->qty,
                            packaging: $moveLine->move->productPackaging,
                            excludeMoveLineIds: $excludedMoveLines,
                        );

                    if ($location->id !== $moveLine->destination_location_id) {
                        $moveLine->update(['destination_location_id' => $location->id]);
                    }

                    $excludedMoveLines = array_diff($excludedMoveLines, [$moveLine->id]);
                }
            }
        }
    }

    public function assignOperation($moves, $mergeInto = null)
    {
        if ($moves->isEmpty()) {
            return;
        }

        $groupedMoves = $moves->groupBy(fn ($move) => implode('_', $move->keyAssignOperation()));

        foreach ($groupedMoves as $moves) {
            $operation = $this->searchOperationForAssignation($moves[0]);

            if ($operation) {
                $vals = [];

                if ($moves->some(fn ($move) => $operation->partner_id !== $move->partner_id)) {
                    $vals['partner_id'] = null;
                }

                if ($moves->some(fn ($move) => $operation->origin !== $move->origin)) {
                    $vals['origin'] = null;
                }

                if (! empty($vals)) {
                    $operation->update($vals);
                }
            } else {
                $moves->each(fn ($move) => $move->load('uom'));

                $moves = $moves->filter(fn ($move) => float_compare($move->product_uom_qty, 0.0, precisionRounding: $move->uom->rounding) >= 0);

                if ($moves->isEmpty()) {
                    continue;
                }

                $operation = Operation::create($this->getNewOperationValues($moves));
            }

            foreach ($moves as $move) {
                $move->update([
                    'operation_id' => $operation->id,
                ]);
            }

            $operation->refresh();

            $operation->computeState();

            $operation->save();
        }
    }

    public function createMovesBackOrder($moves)
    {
        $backOrderMovesValues = collect();

        foreach ($moves as $move) {
            if (float_compare($move->quantity, $move->product_uom_qty, precisionDigits: 2) < 0) {
                $qtySplit = $move->uom->computeQuantity(
                    $move->product_uom_qty - $move->quantity,
                    $move->product->uom,
                    roundingMethod: 'HALF-UP'
                );

                $backOrderMovesValues->push($move->split($qtySplit));
            }
        }

        $backOrderMoves = collect();

        foreach ($backOrderMovesValues as $moveValues) {
            $originIds = $moveValues['move_origin_ids'] ?? [];

            $destinationIds = $moveValues['move_destination_ids'] ?? [];

            unset($moveValues['move_origin_ids'], $moveValues['move_destination_ids']);

            $move = Move::create($moveValues);

            if (! empty($originIds)) {
                $move->moveOrigins()->attach($originIds);
            }

            if (! empty($destinationIds)) {
                $move->moveDestinations()->attach($destinationIds);
            }

            $backOrderMoves->push($move);
        }

        $this->confirmMoves($backOrderMoves, merge: false);

        return $backOrderMoves;
    }

    public function checkForErrors($operation): void
    {
        $noQuantitiesDoneIds = collect();

        $productsWithoutLots = collect();

        $hasLotsIssue = false;

        $hasNoMoves = $operation->moves->isEmpty() && $operation->moveLines->isEmpty();

        $hasNoQuantities = $operation->moves
            ->filter(fn ($move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED]))
            ->every(fn ($move) => float_is_zero($move->quantity, precisionDigits: 2));

        if ($operation->operationType->use_create_lots || $operation->operationType->use_existing_lots) {
            $linesToCheck = $this->getLotMoveLinesForErrorsCheck($operation, $noQuantitiesDoneIds);

            foreach ($linesToCheck as $line) {
                if (! $line->lot_name && ! $line->lot_id) {
                    $hasLotsIssue = true;

                    $productsWithoutLots->push($line->product);
                }
            }
        }

        if ($hasNoMoves) {
            throw new \Exception(__('inventories::filament/clusters/operations/actions/validate.notification.warning.lines-missing.body'));
        }

        if ($hasNoQuantities) {
            throw new \Exception(__('inventories::filament/clusters/operations/actions/validate.notification.warning.no-quantities-reserved.body'));
        }

        if ($hasLotsIssue) {
            throw new \Exception(__('inventories::filament/clusters/operations/actions/validate.notification.warning.lot-missing.body', [
                'products' => $productsWithoutLots->pluck('name')->implode(', '),
            ]));
        }
    }

    public function getLotMoveLinesForErrorsCheck(Operation $operation, $noQuantitiesDoneIds)
    {
        $getLineWithDoneQty = fn ($moveLines) => $moveLines->filter(
            fn ($moveLine) => $moveLine->product
                && $moveLine->product->tracking !== ProductTracking::QTY
                && $moveLine->is_picked
                && float_compare($moveLine->qty, 0, precisionRounding: $moveLine->uom->rounding) > 0
        );

        if ($noQuantitiesDoneIds->contains($operation->id)) {
            $linesToCheck = $operation->moveLines->filter(
                fn ($moveLine) => $moveLine->product && $moveLine->product->tracking !== ProductTracking::QTY
            );
        } else {
            $linesToCheck = $getLineWithDoneQty($operation->moveLines);
        }

        return $linesToCheck;
    }

    public function createBackOrder(Operation $record, $backOrderMoves = null)
    {
        if ($backOrderMoves) {
            $movesToBackOrder = $backOrderMoves->filter(fn ($move) => $move->operation_id === $record->id);
        } else {
            $movesToBackOrder = $record->moves()->get()->filter(
                fn ($move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED])
            );
        }

        $movesToBackOrder->each(function ($move) {
            $move->computeState();

            $move->save();
        });

        if ($movesToBackOrder->isEmpty()) {
            return;
        }

        $backOrderOperation = $record->replicate(['name', 'moves', 'moveLines']);

        $backOrderOperation->fill([
            'name'          => '/',
            'back_order_id' => $record->id,
            'user_id'       => null,
        ]);

        $backOrderOperation->save();

        $movesToBackOrder->each->update([
            'operation_id' => $backOrderOperation->id,
            'is_picked'    => false,
        ]);

        $movesToBackOrder
            ->flatMap->lines
            ->flatMap->packageLevel
            ->filter()
            ->each->update(['operation_id' => $backOrderOperation->id]);

        $movesToBackOrder
            ->flatMap->lines
            ->each->update(['operation_id' => $backOrderOperation->id]);

        if ($backOrderOperation->operationType->reservation_method === ReservationMethod::AT_CONFIRM) {
            $this->assignTransfer($backOrderOperation);
        }

        if (Package::isPluginInstalled('purchases')) {
            $backOrderOperation->purchaseOrders()->attach($record->purchaseOrders->pluck('id'));
        }

        OperationBackOrdered::dispatch($record);

        $url = OperationResource::getUrl('view', ['record' => $record]);

        $backOrderOperation->addMessage([
            'body' => "This transfer has been created from <a href=\"{$url}\" target=\"_blank\" class=\"text-primary-600 dark:text-primary-400\">{$record->name}</a>.",
            'type' => 'comment',
        ]);

        $url = OperationResource::getUrl('view', ['record' => $backOrderOperation]);

        $record->addMessage([
            'body' => "The back order <a href=\"{$url}\" target=\"_blank\" class=\"text-primary-600 dark:text-primary-400\">{$backOrderOperation->name}</a> has been created.",
            'type' => 'comment',
        ]);

        return $backOrderOperation;
    }

    public function getNewOperationValues($moves): array
    {
        $origins = $moves->filter(fn ($move) => $move->origin)
            ->pluck('origin')
            ->unique()
            ->values();

        if ($origins->isEmpty()) {
            $origin = null;
        } else {
            $origin = $origins->take(5)->implode(',');

            if ($origins->count() > 5) {
                $origin .= '...';
            }
        }

        $partners = $moves->pluck('partner_id')->unique();

        $partner = $partners->count() === 1 ? $partners->first() : null;

        $values = [
            'origin'               => $origin,
            'company_id'           => $moves->pluck('company_id')->first(),
            'user_id'              => null,
            'procurement_group_id' => $moves->pluck('procurement_group_id')->first(),
            'partner_id'           => $partner,
            'operation_type_id'    => $moves->pluck('operation_type_id')->first(),
            'source_location_id'   => $moves->pluck('source_location_id')->first(),
        ];

        $destinationLocationIds = $moves->pluck('destination_location_id')->filter()->unique();

        if ($destinationLocationIds->isNotEmpty()) {
            $values['destination_location_id'] = $destinationLocationIds->first();
        }

        if ($saleOrderId = $moves->first()?->procurementGroup?->sale_order_id) {
            $values['sale_order_id'] = $saleOrderId;
        }

        return $values;
    }

    public function searchOperationForAssignation(Move $move)
    {
        $query = Operation::where('procurement_group_id', $move->procurement_group_id)
            ->where('source_location_id', $move->source_location_id)
            ->where('destination_location_id', $move->destination_location_id ?? $move->operationType->destination_location_id)
            ->where('operation_type_id', $move->operation_type_id)
            // ->where('printed', false)
            ->whereIn('state', [OperationState::DRAFT, OperationState::CONFIRMED, OperationState::ASSIGNED]);

        if ($move->partner_id && ! $move->procurement_group_id) {
            $query->where('partner_id', $move->partner_id);
        }

        return $query->first();
    }

    public function mergeMoveValues($moves, $mergeExtra = false)
    {
        $state = $this->getRelevantStateAmongMoves($moves);

        $origin = $moves->filter(fn ($move) => $move->origin)
            ->pluck('origin')
            ->unique()
            ->implode('/');

        $date = $moves->pluck('operation')->every(fn ($operation) => $operation->move_type === MoveType::DIRECT)
            ? $moves->min('date')
            : $moves->max('date');

        return [
            'product_uom_qty'   => ! $mergeExtra
                ? $moves->sum('product_uom_qty')
                : $moves->first()->product_uom_qty,
            'product_qty'       => ! $mergeExtra
                ? $moves->sum('product_qty')
                : $moves->first()->product_qty,
            'date'              => $date,
            'state'             => $state,
            'origin'            => $origin,
            'move_destinations' => $moves->flatMap->moveDestinations,
            'move_origins'      => $moves->flatMap->moveOrigins,
        ];
    }

    public function applyPushRules($moves)
    {
        $newMoves = collect();

        foreach ($moves as $move) {
            $newMove = null;

            $warehouse = $move->warehouse ?? $move->operation?->operationType->warehouse;

            $rule = $this->getPushRule($move->product, $move->destinationLocation, [
                'routes'    => $move->routes,
                'packaging' => $move->productPackaging,
                'warehouse' => $warehouse,
            ]);

            if (
                $rule
                && (
                    ! $move->origin_returned_move_id
                    || $move->originReturnedMove->destination_location_id !== $rule->destination_location_id
                )
            ) {
                $newMove = $this->runPushRule($rule, $move);

                if ($newMove) {
                    $newMoves->push($newMove);
                }
            }

            $movesToPropagate = collect();

            $movesToMts = collect();

            foreach ($move->moveDestinations->diff($newMove ? collect([$newMove]) : collect()) as $m) {
                if ($newMove && $move->final_location_id && $m->source_location_id === $move->final_location_id) {
                    $movesToPropagate->push($m);
                } elseif (! $m->sourceLocation->isChildOf($move->destinationLocation)) {
                    $movesToMts->push($m);
                }
            }

            foreach ($movesToMts as $m) {
                $m->moveOrigins()->detach($move->id);

                $m->procure_method = ProcureMethod::MAKE_TO_STOCK;

                $m->computeState();

                $m->save();
            }

            $move->moveDestinations()->detach($movesToPropagate->pluck('id')->all());

            $newMove?->moveDestinations()->syncWithoutDetaching($movesToPropagate->pluck('id')->all());
        }

        $this->confirmMoves($newMoves);

        return $newMoves;
    }

    public function runProcurements($procurements)
    {
        if ($procurements->isEmpty()) {
            return;
        }

        $actionsToRun = [];

        $procurementErrors = [];

        foreach ($procurements as $procurement) {
            if (
                $procurement['product']->type !== ProductType::GOODS
                || float_is_zero($procurement['product_qty'], precisionRounding: $procurement['product_uom']->rounding)
            ) {
                continue;
            }

            $procurement['values']['company'] = $procurement['values']['company'] ?? $procurement['location']->company;
            $procurement['values']['priority'] = $procurement['values']['priority'] ?? '0';
            $procurement['values']['planned'] = $procurement['values']['planned'] ?? now();

            $rule = $this->getRule($procurement['product'], $procurement['location'], $procurement['values']);

            if (! $rule) {
                $error = __('inventories::system.inventory-manager.run-procurement.no-rule-found', [
                    'product'  => $procurement['product']->name,
                    'location' => $procurement['location']->full_name,
                ]);

                $procurementErrors[] = ['procurement' => $procurement, 'error' => $error];
            } else {
                $action = $rule->action === RuleAction::PULL_PUSH ? RuleAction::PULL : $rule->action;

                if (! isset($actionsToRun[$action->value])) {
                    $actionsToRun[$action->value] = [];
                }

                $actionsToRun[$action->value][] = [$procurement, $rule];
            }
        }

        foreach ($actionsToRun as $action => $procurements) {
            $method = 'run'.ucfirst($action).'Rule';

            try {
                $this->$method($procurements);
            } catch (\Exception $e) {
                $procurementErrors[] = $e->getMessage();
            }
        }

        if (! empty($procurementErrors)) {
            $errorMessage = collect($procurementErrors)->map(function ($error) {
                if (isset($error['error'])) {
                    return $error['error'];
                }

                return $error;
            })->implode("\n");

            throw new \Exception($errorMessage);
        }
    }

    public function runPullRule($procurements)
    {
        foreach ($procurements as [$procurement, $rule]) {
            if (! $rule->source_location_id) {
                throw new \Exception(__('inventories::system.inventory-manager.run-procurement.no-source-location', [
                    'name' => $rule->name,
                ]));
            }
        }

        usort($procurements, function ($procurement) {
            return float_compare($procurement[0]['product_qty'], 0.0, precisionRounding: $procurement[0]['product_uom']->rounding) > 0 ? 1 : 0;
        });

        $movesValuesByCompany = [];

        foreach ($procurements as [$procurement, $rule]) {
            $procureMethod = $rule->procure_method;

            if ($rule->procure_method === ProcureMethod::MTS_ELSE_MTO) {
                $procureMethod = ProcureMethod::MAKE_TO_STOCK;
            }

            $moveValues = $this->prepareMoveValues($rule, $procurement);

            $moveValues['procure_method'] = $procureMethod;

            $movesValuesByCompany[$procurement['company']->id][] = $moveValues;
        }

        foreach ($movesValuesByCompany as $companyId => $moveValues) {
            $moves = collect();

            foreach ($moveValues as $moveValue) {
                $move = Move::create($moveValue);

                $moves->push($move);

                if ($moveValue['move_destinations']->isNotEmpty()) {
                    $move->moveDestinations()->attach($moveValue['move_destinations']->pluck('id')->all());
                }
            }

            $this->confirmMoves($moves);
        }
    }

    public function runPushRule(Rule $rule, Move $move)
    {
        $newScheduledAt = $move->scheduled_at->addDays($rule->delay);

        if ($rule->auto == RuleAuto::TRANSPARENT) {
            $move->update([
                'scheduled_at'            => $newScheduledAt,
                'destination_location_id' => $rule->destination_location_id,
            ]);

            if ($move->lines->isNotEmpty()) {
                $putAwayLocation = $move->destinationLocation->getPutawayStrategy($move->product);

                foreach ($move->lines as $moveLine) {
                    $moveLine->update([
                        'destination_location_id' => $putAwayLocation?->id ?? $move->destination_location_id,
                    ]);
                }
            }

            if ($rule->destination_location_id !== $move->destination_location_id) {
                return $this->applyPushRules(collect([$move]))->first();
            }
        } else {
            $newMoveValues = $this->preparePushMoveCopyValues($rule, $move, $newScheduledAt);

            $newMove = $move->replicate(['order_id', 'work_order_id', 'purchase_order_line_id'])->fill($newMoveValues);

            $newMove->save();

            if ($newMove->shouldBypassReservation()) {
                $newMove->update([
                    'procure_method' => ProcureMethod::MAKE_TO_STOCK,
                ]);
            }

            if (! $newMove->sourceLocation->shouldBypassReservation()) {
                $move->moveDestinations()->attach($newMove->id);
            }
        }

        return $newMove->refresh();
    }

    public function runBuyRule($procurements)
    {
        return;
        if (! Package::isPluginInstalled('purchases')) {
            return;
        }

        $procurementsByPoFilters = [];

        $errors = [];

        foreach ($procurements as [$procurement, $rule]) {
            $procurementDatePlanned = \DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $procurement['values']['planned']
            );

            $supplier = false;

            $company = $rule->company ?: $procurement['company'];

            if (! empty($procurement['values']['supplierinfo'])) {
                $supplier = $procurement['values']['supplierinfo'];
            } elseif (
                ! empty($procurement['values']['order_point']) &&
                $procurement['values']['order_point']->supplier
            ) {
                $supplier = $procurement['values']['order_point']->supplier;
            } else {
                $supplier = $procurement['product']
                    ->getSeller([
                        'partner'    => $procurement['values']['supplier'] ?? null,
                        'quantity'   => $procurement['product_qty'],
                        'date'       => max(
                            $procurementDatePlanned->format('Y-m-d'),
                            now()->format('Y-m-d')
                        ),
                        'uom'        => $procurement['product_uom'],
                        'company'    => $company,
                        'params'     => ['force_uom' => $procurement['values']['force_uom'] ?? null],
                    ]);
            }

            $supplier = $supplier ?: $procurement['product']
                ->prepareSellers(false)
                ->filter(fn ($seller) => ! $seller->company_id || $seller->company_id === $company->id)
                ->first();

            if (! $supplier && $procurement['values']['from_order_point'] ?? null) {
                $msg = __('inventories::system.inventory-manager.run-procurement.no-vendor-price', [
                    'product' => $procurement['product']->name,
                ]);

                $errors[] = [$procurement, $msg];
            } elseif (! $supplier) {
                $moves = $procurement['values']['move_destinations'] ?? collect();

                foreach ($moves as $move) {
                    if ($move->propagate_cancel) {
                        $this->cancelMoves(collect([$move]));
                    }

                    $move->procure_method = 'make_to_stock';
                }

                continue;
            }

            $partner = $supplier->partner;

            $procurement['values']['supplier'] = $supplier;

            $procurement['values']['propagate_cancel'] = $rule->propagate_cancel;

            $filters = $this->getPurchaseOrderFilters($rule, $company, $procurement['values'], $partner);

            $filtersKey = serialize($filters);

            $procurementsByPoFilters[$filtersKey][] = [$procurement, $rule];
        }

        if (! empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }

        foreach ($procurementsByPoFilters as $filtersKey => $procurementsRules) {
            $procurements = collect($procurementsRules)->pluck(0);

            $rules = collect($procurementsRules)->pluck(1);

            $filters = unserialize($filtersKey);

            $origins = $procurements->pluck('origin')->unique()->filter()->all();

            $purchaseOrder = PurchaseOrder::where($filters)->first();

            $company = $rules->first()->company ?: $procurements->first()['company'];

            if (! $purchaseOrder) {
                $positiveValues = $procurements
                    ->filter(fn ($procurement) => bccomp(round($procurement['product_qty'], $procurement['product_uom']->rounding), 0.0) >= 0)
                    ->pluck('values')
                    ->all();

                if (! empty($positiveValues)) {
                    $values = $this->preparePurchaseOrderValues($rules->first(), $company, $origins, $positiveValues);

                    $purchaseOrder = PurchaseOrder::create($values);
                }
            } else {
                if ($purchaseOrder->origin) {
                    $missingOrigins = array_diff($origins, explode(', ', $purchaseOrder->origin));

                    if (! empty($missingOrigins)) {
                        $purchaseOrder->update(['origin' => $purchaseOrder->origin.', '.implode(', ', $missingOrigins)]);
                    }
                } else {
                    $purchaseOrder->update(['origin' => implode(', ', $origins)]);
                }
            }

            $procurementsToMerge = $this->getProcurementsToMerge($procurements->all());

            $procurements = $this->mergeProcurements($procurementsToMerge);

            $purchaseOrderLinesByProduct = $purchaseOrder->orderLines
                ->filter(fn ($line) => ! $line->display_type && $line->uom_id === $line->product->uom_po_id)
                ->groupBy('product_id');

            $purchaseOrderLineValues = [];

            foreach ($procurements as $procurement) {
                $purchaseOrderLines = $purchaseOrderLinesByProduct->get($procurement['product_id'], collect());

                $purchaseOrderLine = $purchaseOrderLines->findCandidate($procurement);

                if ($purchaseOrderLine) {
                    $values = $this->updatePurchaseOrderLine(
                        $procurement['product'],
                        $procurement['product_qty'],
                        $procurement['product_uom'],
                        $company,
                        $procurement['values'],
                        $purchaseOrderLine,
                    );

                    $purchaseOrderLine->update($values);
                } else {
                    if (bccomp(round($procurement['product_qty'], $procurement['product_uom']->rounding), 0) <= 0) {
                        continue;
                    }

                    $purchaseOrderLineValues[] = PurchaseOrderLine::preparePurchaseOrderLineFromProcurement($procurement, $purchaseOrder);

                    $orderDatePlanned = Carbon::parse($procurement['values']['planned'])
                        ->subDays($procurement['values']['supplier']?->delay ?? 0);

                    if ($orderDatePlanned->toDateString() < Carbon::parse($purchaseOrder->ordered_at)->toDateString()) {
                        $purchaseOrder->update(['ordered_at' => $orderDatePlanned]);
                    }
                }
            }
        }

        if (! empty($purchaseOrderLineValues)) {
            PurchaseOrderLine::insert($purchaseOrderLineValues);
        }
    }

    public function runManufactureRule($procurements) {}

    public function getPushRule(Product $product, Location $destinationLocation, array $values = [])
    {
        $foundRule = null;

        $location = $destinationLocation;

        $filters['action'] = [RuleAction::PUSH, RuleAction::PULL_PUSH];

        while (! $foundRule && $location) {
            $filters['source_location_id'] = $location->id;

            $foundRule = $this->searchRule(
                $values['routes'] ?? collect(),
                $values['packaging'] ?? null,
                $product,
                $values['warehouse'] ?? null,
                $filters
            );

            $location = $location->parent;
        }

        return $foundRule;
    }

    public function getRule(Product $product, Location $location, array $values = [])
    {
        $foundRule = null;

        $filters['action'] = ['!=', RuleAction::PUSH];

        while (! $foundRule && $location) {
            $filters['destination_location_id'] = $location->id;

            $foundRule = $this->searchRule(
                $values['routes'] ?? collect(),
                $values['packaging'] ?? null,
                $product,
                $values['warehouse'] ?? null,
                $filters
            );

            $location = $location->parent;
        }

        return $foundRule;
    }

    public function searchRule($routes, $productPackaging, $product, $warehouse, array $filters)
    {
        if ($warehouse) {
            $filters['warehouse_id'] = $warehouse->id;
        }

        $routeIds = collect();

        if ($routes?->isNotEmpty()) {
            $routeIds = $routeIds->merge($routes->pluck('id'));
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

            $routeIds = $routeIds->merge($source->{$relationName}->pluck('id'))
                ->unique();

            if ($routeIds->isEmpty()) {
                continue;
            }

            $foundRule = Rule::whereIn('route_id', $routeIds)
                ->where(function ($query) use ($filters) {
                    foreach ($filters as $column => $value) {
                        if (is_array($value)) {
                            if (count($value) === 2 && $value[0] === '!=') {
                                [, $val] = $value;

                                $query->where($column, '!=', $val);
                            } else {
                                $query->whereIn($column, $value);
                            }
                        } else {
                            $query->where($column, $value);
                        }
                    }
                })
                ->orderBy('route_sort', 'asc')
                ->orderBy('sort', 'asc')
                ->first();

            if ($foundRule) {
                return $foundRule;
            }
        }

        return null;
    }

    public function prepareProcurementQty($moves)
    {
        $quantities = [];

        $mtsoProductsByLocations = [];

        $mtsoMoveIds = [];

        foreach ($moves as $move) {
            if ($move->rule?->procure_method === ProcureMethod::MTS_ELSE_MTO) {
                $mtsoMoveIds[$move->id] = true;

                $mtsoProductsByLocations[$move->source_location_id][] = $move->product_id;
            }
        }

        $forecastedQuantitiesByLocation = [];

        foreach ($mtsoProductsByLocations as $locationId => $productIds) {
            $location = Location::find($locationId);

            if (! $location || $location->shouldBypassReservation()) {
                continue;
            }

            $forecastedQuantitiesByLocation[$locationId] = Product::whereIn('id', array_unique($productIds))
                ->get()
                ->mapWithKeys(function ($product) use ($locationId) {
                    $product->context = ['location_id' => $locationId];

                    return [$product->id => $product->free_qty];
                })
                ->all();
        }

        foreach ($moves as $move) {
            $rounding = $move->product->uom->rounding ?? 0.01;

            if (
                ! isset($mtsoMoveIds[$move->id])
                || float_compare($move->product_qty, 0, precisionRounding: $rounding) <= 0
            ) {
                $quantities[] = $move->product_uom_qty;

                continue;
            }

            if ($move->shouldBypassReservation()) {
                $quantities[] = $move->product_uom_qty;

                continue;
            }

            $freeQty = max($forecastedQuantitiesByLocation[$move->source_location_id][$move->product_id] ?? 0, 0);

            $quantity = max($move->product_qty - $freeQty, 0);

            $productUomQty = $move->product->uom->computeQuantity(
                $quantity,
                $move->uom,
                roundingMethod: 'HALF-UP'
            );

            $quantities[] = $productUomQty;

            $forecastedQuantitiesByLocation[$move->source_location_id][$move->product_id] =
                ($forecastedQuantitiesByLocation[$move->source_location_id][$move->product_id] ?? 0)
                - min($move->product_qty, $freeQty);
        }

        return $quantities;
    }

    public function preparePushMoveCopyValues(Rule $rule, Move $moveToCopy, $newScheduledAt)
    {
        $companyId = $rule->company_id;

        $copiedQuantity = $moveToCopy->quantity;

        if (float_compare($moveToCopy->product_uom_qty, 0, precisionRounding: $moveToCopy->uom->rounding) < 0) {
            $copiedQuantity = $moveToCopy->product_uom_qty;
        }

        if (! $companyId) {
            $companyId = $rule->warehouse?->company_id
                ?? $rule->operationType?->warehouse?->company_id;
        }

        return [
            'state'                   => MoveState::DRAFT,
            'reference'               => null,
            'product_uom_qty'         => $copiedQuantity,
            'product_qty'             => $moveToCopy->uom->computeQuantity($copiedQuantity, $moveToCopy->product->uom, true, 'HALF-UP'),
            'quantity'                => 0,
            'is_picked'               => false,
            'origin'                  => $moveToCopy->origin ?? $moveToCopy->operation->name ?? '/',
            'operation_id'            => null,
            'source_location_id'      => $moveToCopy->destination_location_id,
            'destination_location_id' => $rule->destination_location_id,
            'final_location_id'       => $moveToCopy->final_location_id,
            'rule_id'                 => $rule->id,
            'scheduled_at'            => $newScheduledAt,
            'company_id'              => $companyId,
            'operation_type_id'       => $rule->operation_type_id,
            'propagate_cancel'        => $rule->propagate_cancel,
            'warehouse_id'            => $rule->warehouse_id,
            'procure_method'          => ProcureMethod::MAKE_TO_ORDER,
        ];
    }

    public function prepareMoveValues($rule, $procurement)
    {
        $procurementGroupId = null;

        if ($rule->group_propagation_option === GroupPropagation::PROPAGATE) {
            $procurementGroupId = $procurement['values']['procurement_group']?->id;
        } elseif ($rule->group_propagation_option === GroupPropagation::FIXED) {
            $procurementGroupId = $rule->procurement_group_id;
        }

        $dateScheduled = $procurement['values']['planned']->copy()->subDays($rule->delay ?? 0);

        $dateDeadline = isset($procurement['values']['deadline'])
            ? $procurement['values']['deadline']->copy()->subDays($rule->delay ?? 0)
            : null;

        $partner = $rule->partnerAddress ?? $procurement['values']['procurement_group']?->partner;

        $pickingDescription = $procurement['product']->getDescription($rule->operationType);

        $qtyLeft = $procurement['product_qty'];

        if (! $partner && ! $procurement['values']['move_destinations']?->isNotEmpty()) {
            $moveDestinations = $procurement['values']['move_destinations'];

            $transitLocation = Location::where('type', LocationType::TRANSIT)->whereNotNull('company_id')->first();

            if ($procurement['location']->id === $transitLocation->is) {
                $partners = $moveDestinations->pluck('destinationLocation.warehouse.partner')->filter()->unique('id');

                if ($partners->count() === 1) {
                    $partner = $partners->first();
                }

                $moveDestinations->each->update([
                    'partner_id' => $rule->sourceLocation->warehouse?->partner_id ?? $rule->company->partner_id,
                ]);
            }
        }

        if (float_compare($procurement['product_qty'], 0.0, precisionRounding: $procurement['product_uom']->rounding) < 0) {
            $isRefund = true;
        }

        $moveValues = [
            'name'                 => substr($procurement['name'], 0, 2000),
            'company_id'           => $rule->company_id ?? $procurement['location']->company_id ?? $rule->destinationLocation?->company_id ?? $procurement['company']?->id,
            'product_id'           => $procurement['product']->id,
            'uom_id'               => $procurement['product_uom']?->id,
            'product_uom_qty'      => $qtyLeft,
            'product_qty'          => $procurement['product_uom']->computeQuantity($qtyLeft, $procurement['product']->uom, roundingMethod: 'HALF-UP'),
            'partner_id'           => $partner?->id,
            'source_location_id'   => $rule->source_location_id,
            'final_location_id'    => $rule->destination_location_id,
            'move_destinations'    => $procurement['values']['move_destinations'] ?? collect(),
            'rule_id'              => $rule->id,
            'procure_method'       => $rule->procure_method,
            'origin'               => $procurement['origin'] ?? null,
            'operation_type_id'    => $rule->operation_type_id,
            'procurement_group_id' => $procurementGroupId,
            'routes'               => $procurement['values']['routes'] ?? collect(),
            'warehouse_id'         => $rule->warehouse_id,
            'scheduled_at'         => $dateScheduled,
            'deadline'             => $rule->group_propagation_option === GroupPropagation::FIXED ? null : $dateDeadline,
            'propagate_cancel'     => $rule->propagate_cancel,
            'description_picking'  => $pickingDescription,
            'priority'             => $procurement['values']['priority'] ?? '0',
            'order_point_id'       => $procurement['values']['order_point']?->is ?? null,
            'product_packaging_id' => $procurement['values']['product_packaging']?->id ?? null,
        ];

        if (isset($procurement['values']['sale_order_line_id'])) {
            $moveValues['sale_order_line_id'] = $procurement['values']['sale_order_line_id'];
        }

        if (isset($procurement['values']['purchase_order_line_id'])) {
            $moveValues['purchase_order_line_id'] = $procurement['values']['purchase_order_line_id'];
        }

        if (isset($procurement['values']['work_order_id'])) {
            $moveValues['work_order_id'] = $procurement['values']['work_order_id'];
        }

        if (isset($procurement['values']['bom_line_id'])) {
            $moveValues['bom_line_id'] = $procurement['values']['bom_line_id'];
        }

        if ($rule->location_dest_from_rule) {
            $moveValues['destination_location_id'] = $rule->destination_location_id;
        }

        return $moveValues;
    }

    public function prepareReturnOperationValues(Operation $operation): array
    {
        $sourceLocation = $operation->destinationLocation;

        $returnType = $operation->operationType->returnOperationType;

        if ($returnType?->type === OperationType::INCOMING) {
            $destinationLocation = $returnType->destinationLocation;
        } else {
            $destinationLocation = $operation->sourceLocation;
        }

        return [
            'state'                   => OperationState::DRAFT,
            'origin'                  => __('inventories::system.inventory-manager.return.origin', ['operation_name' => $operation->name]),
            'operation_type_id'       => $returnType?->id ?? $operation->operation_type_id,
            'source_location_id'      => $sourceLocation->id,
            'location_destination_id' => $destinationLocation->id,
            'return_id'               => $operation->id,
            'user_id'                 => null,
        ];
    }

    public function prepareReturnMoveValues(Operation $operation, Move $move, mixed $quantity): array
    {
        $values = [
            'name'                    => $operation->name,
            'product_id'              => $move->product_id,
            'product_uom_qty'         => $quantity,
            'product_qty'             => $move->uom->computeQuantity($quantity, $move->product->uom, roundingMethod: 'HALF-UP'),
            // 'quantity'                => $quantity,
            'quantity'                => 0,
            'is_picked'               => 0,
            'uom_id'                  => $move->product->uom_id,
            'operation_id'            => $operation->id,
            'state'                   => MoveState::DRAFT,
            'date'                    => now(),
            'source_location_id'      => $operation->source_location_id ?? $move->destination_location_id,
            'destination_location_id' => $operation->destination_location_id ?? $move->source_location_id,
            'final_location_id'       => null,
            'operation_type_id'       => $operation->operation_type_id,
            'warehouse_id'            => $operation->operationType->warehouse_id,
            'origin_returned_move_id' => $move->id,
            'procure_method'          => ProcureMethod::MAKE_TO_STOCK,
            'procurement_group_id'    => $operation->procurement_group_id,
        ];

        if ($operation->operationType->type === OperationType::OUTGOING) {
            $values['partner_id'] = $operation->partner_id;
        }

        return $values;
    }

    public function preparePurchaseOrderValues($rule, $company, $origins, $values)
    {
        $purchaseDate = collect($values)
            ->map(fn ($value) => ! empty($value['scheduled_at'])
                ? Carbon::parse($value['scheduled_at'])
                : Carbon::parse($value['planned'])->subDays((int) $value['supplier']?->delay ?? 0)
            )
            ->min();

        $value = $values[0];

        $partner = $value['supplier']->partner;

        // $fiscalPosition = FiscalPosition::getFiscalPosition($partner);

        $gpo = $rule->group_propagation_option;

        $procurementGroupId = match (true) {
            $gpo === GroupPropagation::FIXED     => $rule->procurement_group_id,
            $gpo === GroupPropagation::PROPAGATE => $values['procurement_group']?->id ?? false,
            default                              => false,
        };

        return [
            'partner_id'             => $partner->id,
            'user_id'                => $partner->user_id,
            'operation_type_id'      => $rule->operation_type_id,
            'company_id'             => $company->id,
            'currency_id'            => $partner->purchase_currency_id ?? $company->currency_id,
            'destination_address_id' => $value['partner_id'] ?? null,
            'origin'                 => implode(', ', $origins),
            'payment_term_id'        => $partner->property_supplier_payment_term_id,
            'ordered_at'             => $purchaseDate,
            // 'fiscal_position_id'     => $fiscalPosition?->id,
            'procurement_group_id'   => $procurementGroupId,
        ];
    }

    public function getRelevantStateAmongMoves($moves): \BackedEnum
    {
        $sortMap = [
            MoveState::ASSIGNED->value           => 4,
            MoveState::WAITING->value            => 3,
            MoveState::PARTIALLY_ASSIGNED->value => 2,
            MoveState::CONFIRMED->value          => 1,
        ];

        $movesTodo = $moves->filter(fn ($move) => ! in_array($move->state, [MoveState::CANCELED, MoveState::DONE]) &&
                ! ($move->state === MoveState::ASSIGNED && ! $move->product_uom_qty)
        )
            ->sortBy([
                fn ($a, $b) => ($sortMap[$a->state->value] ?? 0) <=> ($sortMap[$b->state->value] ?? 0),
                fn ($a, $b) => $a->product_uom_qty <=> $b->product_uom_qty,
            ])
            ->values();

        if ($movesTodo->isEmpty()) {
            return MoveState::ASSIGNED;
        }

        $firstMove = $movesTodo->first();

        if ($firstMove->operation && $firstMove->operation->move_type === MoveType::ONE) {
            if ($movesTodo->every(fn ($move) => ! $move->product_uom_qty)) {
                return MoveState::ASSIGNED;
            }

            $mostImportantMove = $movesTodo->first();

            if ($mostImportantMove->state === MoveState::CONFIRMED) {
                return MoveState::CONFIRMED;
            } elseif ($mostImportantMove->state === MoveState::PARTIALLY_ASSIGNED) {
                return MoveState::CONFIRMED;
            } else {
                return $mostImportantMove->state ?? MoveState::DRAFT;
            }
        } elseif (
            $firstMove->state !== MoveState::ASSIGNED
            && $movesTodo->some(fn ($move) => in_array($move->state, [MoveState::ASSIGNED, MoveState::PARTIALLY_ASSIGNED]))
        ) {
            return MoveState::PARTIALLY_ASSIGNED;
        } else {
            $leastImportantMove = $movesTodo->last();

            if ($leastImportantMove->state === MoveState::CONFIRMED && $leastImportantMove->product_uom_qty == 0) {
                return MoveState::ASSIGNED;
            }

            return $leastImportantMove->state ?? MoveState::DRAFT;
        }
    }

    public function getPurchaseOrderFilters($rule, $company, $values, $partner)
    {
        $gpo = $rule->group_propagation_option;

        $procurementGroupId = match (true) {
            $gpo === GroupPropagation::FIXED     => $rule->procurement_group_id,
            $gpo === GroupPropagation::PROPAGATE => $values['procurement_group']?->id ?? false,
            default                              => false,
        };

        $filters = [
            ['partner_id', '=', $partner->id],
            ['state', '=', PurchaseOrderEnums\OrderState::DRAFT],
            ['operation_type_id', '=', $rule->operation_type_id],
            ['company_id', '=', $company->id],
            ['user_id', '=', $partner->user_id],
        ];

        if (! empty($values['order_point'])) {
            $procurementDate = Carbon::parse($values['planned'])
                ->subDays($values['supplier']->delay ?? 0)
                ->toDateString();

            $filters[] = ['ordered_at', '<=', Carbon::parse($procurementDate)->endOfDay()];
            $filters[] = ['ordered_at', '>=', Carbon::parse($procurementDate)->startOfDay()];
        }

        if ($procurementGroupId) {
            $filters[] = ['procurement_group_id', '=', $procurementGroupId];
        }

        return $filters;
    }

    public function getProcurementsToMerge($procurements)
    {
        return collect($procurements)
            ->groupBy(function ($procurement) {
                $orderPointKey = (! empty($procurement['values']['order_point']) && empty($procurement['values']['move_destinations']))
                    ? $procurement['values']['order_point']->id
                    : null;

                return implode('_', [
                    $procurement['product']->id,
                    $procurement['product_uom']->id,
                    (int) $procurement['values']['propagate_cancel'],
                    $orderPointKey ?? '',
                ]);
            })
            ->values()
            ->all();
    }

    public function mergeProcurements($procurements)
    {
        $mergedProcurements = [];

        foreach ($procurements as $procurements) {
            $quantity = 0;

            $moveDestinations = collect();

            $orderPoint = null;

            foreach ($procurements as $procurement) {
                if (! empty($procurement['values']['move_destinations'])) {
                    $moveDestinations = $moveDestinations->merge($procurement['values']['move_destinations']);
                }

                if (! $orderPoint && ! empty($procurement['values']['order_point'])) {
                    $orderPoint = $procurement['values']['order_point'];
                }

                $quantity += $procurement['product_qty'];
            }

            $values = array_merge($procurement['values'], [
                'move_destinations' => $moveDestinations,
                'order_point'       => $orderPoint,
            ]);

            $mergedProcurements[] = [
                'product'     => $procurement['product'],
                'product_qty' => $quantity,
                'product_uom' => $procurement['product_uom'],
                'location'    => $procurement['location'],
                'name'        => $procurement['name'],
                'origin'      => $procurement['origin'],
                'company'     => $procurement['company'],
                'values'      => $values,
            ];
        }

        return $mergedProcurements;
    }

    public function updatePurchaseOrderLine($product, $quantity, $uom, $company, $values, $line)
    {
        $partner = $values['supplier']->partner;

        $procurementUOMPoQty = $uom->computeQuantity($quantity, $product->uomPO, roundingMethod: 'HALF-UP');

        $seller = $product
            ->getSeller([
                'partner'  => $partner,
                'quantity' => $line->product_qty + $procurementUOMPoQty,
                'date'     => $line->order->ordered_at?->toDateString(),
                'uom'      => $product->uomPO,
                'company'  => $company,
            ]);

        $priceUnit = $seller
            ? TaxFacade::fixTaxIncludedPriceCompany($seller->price, $line->product->supplierTaxes, $line->taxes, $company)
            : 0.0;

        if ($priceUnit && $seller && $line->order->currency && $seller->currency_id !== $line->order->currency_id) {
            $priceUnit = $seller->currency->convert(
                $priceUnit,
                $line->order->currency,
                $line->order->company,
                now()->toDateString(),
            );
        }

        $result = [
            'product_qty'       => $line->product_qty + $procurementUOMPoQty,
            'price_unit'        => $priceUnit,
            'move_destinations' => collect($values['move_destinations'] ?? collect()),
        ];

        if (! empty($values['order_point'])) {
            $result['order_point_id'] = $values['order_point']->id;
        }

        return $result;
    }

    public function checkForEntirePack($operation)
    {
        if (! $operation) {
            return;
        }

        $groupedByPackage = $operation->moveLines->groupBy('package_id');

        foreach ($groupedByPackage as $packageId => $packageMoveLines) {
            if (! $packageId) {
                continue;
            }

            $package  = PackageModel::find($packageId);

            $operations = $packageMoveLines->pluck('operation')->unique('id');

            if (! $operation->checkMoveLinesMapQuant($packageMoveLines, $package)) {
                continue;
            }

            $packageLevelIds = $operations->flatMap->packageLevels->filter(fn ($packageLevel) => $packageLevel->package_id === $packageId);

            $moveLinesToPack = $packageMoveLines->filter(
                fn ($moveLine) => ! $moveLine->result_package_id &&
                    ! in_array($moveLine->state, [MoveState::DONE, MoveState::CANCELED])
            );

            if ($packageLevelIds->isEmpty()) {
                if ($operations->count() === 1) {
                    $operation = $operations->first();

                    $packageLocation = $operation->getEntirePackDestinationLocation($moveLinesToPack)
                        ?? $operation->destination_location_id;

                    $packageLevel = PackageLevel::create([
                        'operation_id'            => $operation->id,
                        'package_id'              => $package->id,
                        'location_id'             => $package->location_id,
                        'destination_location_id' => $packageLocation,
                        'company_id'              => $operation->company_id,
                    ]);

                    $moveLinesToPack->each(function ($moveLine) use ($packageLevel) {
                        $moveLine->update([
                            'package_level_id' => $packageLevel->id,
                        ]);
                    });

                    if ($package->package_use === PackageUse::DISPOSABLE) {
                        $moveLinesToPack->each->update(['result_package_id' => $package->id]);
                    }
                }
            } else {
                $moveLinesInPackageLevel = $moveLinesToPack->filter(fn ($moveLine) => $moveLine->move?->package_level_id);

                $moveLinesWithoutPackageLevel = $moveLinesToPack->diff($moveLinesInPackageLevel);

                if ($package->package_use === PackageUse::DISPOSABLE) {
                    $moveLinesInPackageLevel->merge($moveLinesWithoutPackageLevel)
                        ->each->update(['result_package_id' => $package->id]);
                }

                foreach ($moveLinesInPackageLevel as $moveLine) {
                    $moveLine->update(['package_level_id' => $moveLine->move->package_level_id]);
                }

                $moveLinesWithoutPackageLevel->each->update(['package_level_id' => $packageLevelIds->first()->id]);

                $moveLinesByPackageLevel = $packageMoveLines->groupBy('package_level_id');

                foreach ($packageLevelIds as $packageLevel) {
                    $packageLevelMoveLines = $moveLinesByPackageLevel->get($packageLevel->id, collect());
                    
                    $packageLevelDestinationLocationId  = $operations->first()->getEntirePackDestinationLocation($packageLevelMoveLines)
                        ?? $operations->first()->destination_location_id;

                    if ($packageLevel->destination_location_id !== $packageLevelDestinationLocationId) {
                        $packageLevel->update(['destination_location_id' => $packageLevelDestinationLocationId]);
                    }
                }

                foreach ($moveLinesToPack->pluck('move')->unique('id') as $move) {
                    if (
                        $move->lines->every(fn($line) => $line->package_level_id)
                        && $move->lines->pluck('package_level_id')->unique()->count() === 1
                    ) {
                        $move->update(['package_level_id' => $move->lines->first()->package_level_id]);
                    }
                }
            }
        }
    }
}
