<?php

namespace Webkul\Inventory\Services;

use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Events\OperationBackOrdered;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Operation;
use Webkul\PluginManager\Package;

class BackorderCreator
{
    public function splitShortMoves(Collection $moves): Collection
    {
        $splitAttributes = collect();

        foreach ($moves as $move) {
            if (float_compare($move->quantity, $move->product_uom_qty, precisionDigits: 2) >= 0) {
                continue;
            }

            $shortfall = $move->uom->computeQuantity(
                $move->product_uom_qty - $move->quantity,
                $move->product->uom,
                roundingMethod: 'HALF-UP'
            );

            $splitAttributes->push($move->split($shortfall));
        }

        $created = collect();

        foreach ($splitAttributes as $attributes) {
            $originIds = $attributes['move_origin_ids'] ?? [];

            $destinationIds = $attributes['move_destination_ids'] ?? [];

            unset($attributes['move_origin_ids'], $attributes['move_destination_ids']);

            $move = Move::create($attributes);

            if ($originIds !== []) {
                $move->moveOrigins()->attach($originIds);
            }

            if ($destinationIds !== []) {
                $move->moveDestinations()->attach($destinationIds);
            }

            $created->push($move);
        }

        app(MoveConfirmer::class)->confirm($created, merge: false);

        return $created;
    }

    public function createFor(Operation $operation, ?Collection $moves = null): ?Operation
    {
        $carriedOver = $moves
            ? $moves->filter(fn (Move $move) => $move->operation_id === $operation->id)
            : $operation->moves()->get()->filter(
                fn (Move $move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED])
            );

        $carriedOver->each(function (Move $move) {
            $move->computeState();

            $move->save();
        });

        if ($carriedOver->isEmpty()) {
            return null;
        }

        $backorder = $operation->replicate(['name', 'return_id', 'closed_at', 'is_printed', 'moves', 'moveLines']);

        $backorder->fill([
            'name'          => '/',
            'back_order_id' => $operation->id,
            'user_id'       => null,
        ]);

        $backorder->save();

        $carriedOver->each->update([
            'operation_id' => $backorder->id,
            'is_picked'    => false,
        ]);

        $carriedOver->flatMap->lines->flatMap->packageLevel->filter()
            ->each->update(['operation_id' => $backorder->id]);

        $carriedOver->flatMap->lines
            ->each->update(['operation_id' => $backorder->id]);

        if ($backorder->operationType->reservation_method === ReservationMethod::AT_CONFIRM) {
            app(TransferWorkflow::class)->reserve($backorder);
        }

        if (Package::isPluginInstalled('purchases')) {
            $backorder->purchaseOrders()->attach($operation->purchaseOrders->pluck('id'));
        }

        OperationBackOrdered::dispatch($operation);

        $this->crossLink($operation, $backorder);

        return $backorder;
    }

    protected function crossLink(Operation $operation, Operation $backorder): void
    {
        $sourceUrl = OperationResource::getUrl('view', ['record' => $operation]);

        $backorder->addMessage([
            'body' => "This transfer has been created from <a href=\"{$sourceUrl}\" target=\"_blank\" class=\"text-primary-600 dark:text-primary-400\">{$operation->name}</a>.",
            'type' => 'comment',
        ]);

        $backorderUrl = OperationResource::getUrl('view', ['record' => $backorder]);

        $operation->addMessage([
            'body' => "The back order <a href=\"{$backorderUrl}\" target=\"_blank\" class=\"text-primary-600 dark:text-primary-400\">{$backorder->name}</a> has been created.",
            'type' => 'comment',
        ]);
    }
}
