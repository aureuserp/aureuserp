<?php

namespace Webkul\Inventory\Services;

use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Operation;

class TransferValidator
{
    public function assertReadyToComplete(Operation $operation): void
    {
        if ($operation->moves->isEmpty() && $operation->moveLines->isEmpty()) {
            throw new \Exception(__('inventories::filament/clusters/operations/actions/validate.notification.warning.lines-missing.body'));
        }

        $nothingPicked = $operation->moves
            ->filter(fn (Move $move) => ! in_array($move->state, [MoveState::DONE, MoveState::CANCELED]))
            ->every(fn (Move $move) => float_is_zero($move->quantity, precisionDigits: 2));

        if ($nothingPicked) {
            throw new \Exception(__('inventories::filament/clusters/operations/actions/validate.notification.warning.no-quantities-reserved.body'));
        }

        $untracked = $this->linesMissingTracking($operation);

        if ($untracked->isNotEmpty()) {
            throw new \Exception(__('inventories::filament/clusters/operations/actions/validate.notification.warning.lot-missing.body', [
                'products' => $untracked->pluck('product.name')->implode(', '),
            ]));
        }
    }

    protected function linesMissingTracking(Operation $operation): Collection
    {
        if (! $operation->operationType->use_create_lots && ! $operation->operationType->use_existing_lots) {
            return collect();
        }

        return $operation->moveLines
            ->filter(fn (MoveLine $line) => $line->product
                && $line->product->tracking !== ProductTracking::QTY
                && $line->is_picked
                && float_compare($line->qty, 0, precisionRounding: $line->uom->rounding) > 0)
            ->filter(fn (MoveLine $line) => ! $line->lot_name && ! $line->lot_id)
            ->values();
    }
}
