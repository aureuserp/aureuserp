<?php

namespace Webkul\Manufacturing\Services;

use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\Move;
use Webkul\Product\Enums\ProductType;

class KitExpander
{
    public function expandMoves(Collection $moves): Collection
    {
        $resolved = collect();

        $exploded = collect();

        $componentAttributes = [];

        foreach ($moves as $move) {
            $kit = $this->kitFor($move);

            if (! $kit) {
                $resolved->push($move);

                continue;
            }

            $componentAttributes = array_merge($componentAttributes, $this->componentsOf($move, $kit));

            $exploded->push($move);
        }

        if ($componentAttributes !== []) {
            $components = collect($componentAttributes)->map(fn (array $attributes) => Move::create($attributes));

            $components->each->resolveProcureMethod();

            $resolved = $resolved->merge($this->expandMoves($components));
        }

        $this->discard($exploded);

        return $resolved;
    }

    protected function kitFor(Move $move): ?BillOfMaterial
    {
        $producesItself = $move->order_id && $move->order->product_id === $move->product_id;

        if (! $move->operation_type_id || $producesItself) {
            return null;
        }

        return BillOfMaterial::bomFind(
            collect([$move->product]),
            companyId: $move->company_id,
            bomType: 'phantom'
        )[$move->product_id] ?? null;
    }

    protected function componentsOf(Move $move, BillOfMaterial $kit): array
    {
        $tracksDemand = ! float_is_zero($move->product_uom_qty, precisionRounding: $move->uom->rounding);

        $factor = $move->uom->computeQuantity(
            $tracksDemand ? $move->product_uom_qty : $move->quantity,
            $kit->uom
        ) / $kit->quantity;

        [, $kitLines] = $kit->explode($move->product, $factor, operationType: $kit->operationType);

        $attributes = [];

        foreach ($kitLines as [$kitLine, $lineData]) {
            $attributes = array_merge($attributes, $this->componentMoveAttributes(
                $move,
                $kitLine,
                demand: $tracksDemand ? $lineData['qty'] : 0,
                produced: $tracksDemand ? 0 : $lineData['qty'],
            ));
        }

        return $attributes;
    }

    public function componentMoveAttributes(Move $move, $kitLine, float $demand, float $produced): array
    {
        if ($kitLine->product->type !== ProductType::GOODS) {
            return [];
        }

        $attributes = $move->replicate()
            ->fill($this->kitLineAttributes($move, $kitLine, $demand, $produced))
            ->toArray();

        if ($move->state === MoveState::ASSIGNED) {
            $attributes['state'] = MoveState::ASSIGNED;
        }

        return [$attributes];
    }

    protected function kitLineAttributes(Move $move, $kitLine, float $demand, float $produced): array
    {
        return [
            'operation_id'    => $move->operation?->id,
            'product_id'      => $kitLine->product->id,
            'product_uom'     => $kitLine->uom->id,
            'product_uom_qty' => $demand,
            'quantity'        => $produced,
            'name'            => $move->name,
            'is_picked'       => $move->is_picked,
            'bom_line_id'     => $kitLine->id,
        ];
    }

    protected function discard(Collection $moves): void
    {
        $moves->each(function (Move $move) {
            $move->update(['quantity' => 0]);

            InventoryFacade::cancelMoves(collect([$move]));

            $move->delete();
        });
    }
}
