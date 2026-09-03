<?php

namespace Webkul\Inventory\Services;

use Carbon\Carbon;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\OperationType;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Events\OperationAssigned;
use Webkul\Inventory\Events\OperationCanceled;
use Webkul\Inventory\Events\OperationConfirmed;
use Webkul\Inventory\Events\OperationDone;
use Webkul\Inventory\Events\OperationReturned;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\PluginManager\Package;

class TransferWorkflow
{
    public function confirm(Operation $operation): Operation
    {
        app(MoveConfirmer::class)->confirm(
            $operation->moves->filter(fn (Move $move) => $move->state === MoveState::DRAFT)
        );

        $this->refreshState($operation);

        OperationConfirmed::dispatch($operation);

        return $operation;
    }

    public function reserve(Operation $operation): Operation
    {
        if ($operation->state === OperationState::DRAFT) {
            $operation = $this->confirm($operation);
        }

        $moves = $operation->moves
            ->filter(fn (Move $move) => ! in_array($move->state, [MoveState::DRAFT, MoveState::CANCELED, MoveState::DONE]))
            ->sortBy([
                fn (Move $move) => ! (bool) $move->deadline,
                fn (Move $move) => $move->deadline,
                fn (Move $move) => $move->scheduled_at,
                fn (Move $move) => $move->id,
            ]);

        if ($moves->isEmpty()) {
            throw new \Exception(__('inventories::system.inventory-manager.check-availability.no-moves'));
        }

        app(MoveReserver::class)->reserve($moves);

        $this->refreshState($operation);

        OperationAssigned::dispatch($operation);

        return $operation;
    }

    public function complete(Operation $operation, bool $cancelBackorder = false): Operation
    {
        app(TransferValidator::class)->assertReadyToComplete($operation);

        $pending = $operation->moves->filter(fn (Move $move) => in_array($move->state, [
            MoveState::DRAFT,
            MoveState::WAITING,
            MoveState::PARTIALLY_ASSIGNED,
            MoveState::ASSIGNED,
            MoveState::CONFIRMED,
        ]));

        $this->markEverythingPickedIfNeeded($operation);

        app(MoveCompleter::class)->complete($pending, $cancelBackorder);

        $operation->refresh();

        $operation->computeState();

        $operation->closed_at = Carbon::now();

        $operation->save();

        ProductQuantity::purgeEmpty();

        OperationDone::dispatch($operation);

        return $operation;
    }

    public function cancel(Operation $operation): Operation
    {
        app(MoveCanceller::class)->cancel($operation->moves);

        $operation->computeState();

        $operation->save();

        OperationCanceled::dispatch($operation);

        return $operation;
    }

    public function createReturn(Operation $operation, array $moveQuantities = []): Operation
    {
        $sourceMoves = Move::query()
            ->whereIn('id', array_keys($moveQuantities))
            ->where('operation_id', $operation->id)
            ->get();

        foreach ($sourceMoves as $move) {
            app(MoveReserver::class)->release(
                $move->moveDestinations->filter(
                    fn (Move $destination) => ! in_array($destination->state, [MoveState::DONE, MoveState::CANCELED])
                )
            );
        }

        $return = $operation->replicate(['back_order_id', 'closed_at', 'is_printed'])
            ->fill($this->buildReturnOperationAttributes($operation));

        $return->save();

        foreach ($sourceMoves as $move) {
            $this->createReturnMove($return, $move, $moveQuantities[$move->id]);
        }

        $return->refresh();

        $return = $this->reserve($return);

        if (Package::isPluginInstalled('purchases')) {
            $return->purchaseOrders()->attach($operation->purchaseOrders->pluck('id'));
        }

        $this->crossLink($operation, $return);

        OperationReturned::dispatch($operation);

        return $return;
    }

    protected function createReturnMove(Operation $return, Move $move, mixed $entry): void
    {
        $quantity = is_array($entry) ? ($entry['quantity'] ?? 0) : $entry;

        $isRefund = is_array($entry) ? (bool) ($entry['to_refund'] ?? true) : true;

        $returnMove = $move->replicate()->fill($this->buildReturnMoveAttributes($return, $move, $quantity, $isRefund));

        $returnMove->save();

        $origins = $move->moveDestinations->flatMap->returnedMoves
            ->merge(collect([$move]))
            ->merge(
                $move->moveDestinations
                    ->filter(fn (Move $destination) => $destination->state !== MoveState::CANCELED)
                    ->flatMap->moveOrigins
                    ->filter(fn (Move $origin) => $origin->state !== MoveState::CANCELED)
            );

        $destinations = $move->moveOrigins->flatMap->returnedMoves
            ->merge(
                $move->moveOrigins->flatMap->returnedMoves
                    ->flatMap->moveOrigins
                    ->filter(fn (Move $origin) => $origin->state !== MoveState::CANCELED)
                    ->flatMap->moveDestinations
                    ->filter(fn (Move $destination) => $destination->state !== MoveState::CANCELED)
            );

        $returnMove->moveOrigins()->syncWithoutDetaching($origins->pluck('id')->all());

        $returnMove->moveDestinations()->syncWithoutDetaching($destinations->pluck('id')->all());
    }

    public function buildReturnOperationAttributes(Operation $operation): array
    {
        $returnType = $operation->operationType->returnOperationType;

        $destination = $returnType?->type === OperationType::INCOMING
            ? $returnType->destinationLocation
            : $operation->sourceLocation;

        return [
            'state'                   => OperationState::DRAFT,
            'origin'                  => __('inventories::system.inventory-manager.return.origin', ['operation_name' => $operation->name]),
            'operation_type_id'       => $returnType?->id ?? $operation->operation_type_id,
            'source_location_id'      => $operation->destinationLocation->id,
            'destination_location_id' => $destination->id,
            'return_id'               => $operation->id,
            'user_id'                 => null,
        ];
    }

    public function buildReturnMoveAttributes(Operation $return, Move $move, mixed $quantity, bool $isRefund = true): array
    {
        $attributes = [
            'name'                    => $return->name,
            'product_id'              => $move->product_id,
            'product_uom_qty'         => $quantity,
            'product_qty'             => $move->uom->computeQuantity($quantity, $move->product->uom, roundingMethod: 'HALF-UP'),
            'quantity'                => 0,
            'is_picked'               => false,
            'is_refund'               => $isRefund,
            'uom_id'                  => $move->product->uom_id,
            'operation_id'            => $return->id,
            'state'                   => MoveState::DRAFT,
            'scheduled_at'            => now(),
            'deadline'                => null,
            'price_unit'              => 0,
            'source_location_id'      => $return->source_location_id ?? $move->destination_location_id,
            'destination_location_id' => $return->destination_location_id ?? $move->source_location_id,
            'final_location_id'       => null,
            'operation_type_id'       => $return->operation_type_id,
            'warehouse_id'            => $return->operationType->warehouse_id,
            'origin_returned_move_id' => $move->id,
            'procure_method'          => ProcureMethod::MAKE_TO_STOCK,
            'procurement_group_id'    => $return->procurement_group_id,
        ];

        if ($return->operationType->type === OperationType::OUTGOING) {
            $attributes['partner_id'] = $return->partner_id;
        }

        return $attributes;
    }

    protected function markEverythingPickedIfNeeded(Operation $operation): void
    {
        $hasQuantity = false;

        $hasPick = false;

        foreach ($operation->moves as $move) {
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
            $operation->moves->each->update(['is_picked' => true]);
        }
    }

    protected function refreshState(Operation $operation): void
    {
        $operation->refresh();

        $operation->computeState();

        $operation->save();
    }

    protected function crossLink(Operation $operation, Operation $return): void
    {
        $sourceUrl = OperationResource::getUrl('view', ['record' => $operation]);

        $return->addMessage([
            'body' => "This transfer has been created from <a href=\"{$sourceUrl}\" target=\"_blank\" class=\"text-primary-600 dark:text-primary-400\">{$operation->name}</a>.",
            'type' => 'comment',
        ]);

        $returnUrl = OperationResource::getUrl('view', ['record' => $return]);

        $operation->addMessage([
            'body' => "The return <a href=\"{$returnUrl}\" target=\"_blank\" class=\"text-primary-600 dark:text-primary-400\">{$return->name}</a> has been created.",
            'type' => 'comment',
        ]);
    }
}
