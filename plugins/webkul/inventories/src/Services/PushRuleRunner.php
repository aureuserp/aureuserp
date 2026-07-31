<?php

namespace Webkul\Inventory\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\RuleAuto;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Rule;

class PushRuleRunner
{
    public function run(Collection $moves): Collection
    {
        $created = collect();

        foreach ($moves as $move) {
            $pushedMove = $this->push($move);

            if ($pushedMove) {
                $created->push($pushedMove);
            }

            $this->rewireDownstream($move, $pushedMove);
        }

        app(MoveConfirmer::class)->confirm($created);

        return $created;
    }

    protected function push(Move $move): ?Move
    {
        $rule = app(RuleResolver::class)->findPushRule($move->product, $move->destinationLocation, [
            'routes'    => $move->routes,
            'packaging' => $move->productPackaging,
            'warehouse' => $move->warehouse ?? $move->operation?->operationType->warehouse,
        ]);

        if (! $rule) {
            return null;
        }

        $returnsToSameLocation = $move->origin_returned_move_id
            && $move->originReturnedMove->destination_location_id === $rule->destination_location_id;

        if ($returnsToSameLocation) {
            return null;
        }

        return $rule->auto == RuleAuto::TRANSPARENT
            ? $this->redirectInPlace($rule, $move)
            : $this->chainNewMove($rule, $move);
    }

    protected function redirectInPlace(Rule $rule, Move $move): ?Move
    {
        $move->update([
            'scheduled_at'            => $move->scheduled_at->addDays($rule->delay),
            'destination_location_id' => $rule->destination_location_id,
        ]);

        if ($move->lines->isEmpty()) {
            return null;
        }

        $putawayLocation = $move->destinationLocation->resolvePutawayLocation($move->product);

        $move->lines->each(fn (MoveLine $line) => $line->update([
            'destination_location_id' => $putawayLocation?->id ?? $move->destination_location_id,
        ]));

        return null;
    }

    protected function chainNewMove(Rule $rule, Move $move): Move
    {
        $newMove = $move->replicate(['order_id', 'work_order_id', 'purchase_order_line_id'])
            ->fill($this->buildChainedMoveAttributes($rule, $move, $move->scheduled_at->addDays($rule->delay)));

        $newMove->save();

        if ($newMove->bypassesReservation()) {
            $newMove->update(['procure_method' => ProcureMethod::MAKE_TO_STOCK]);
        }

        if (! $newMove->sourceLocation->bypassesReservation()) {
            $move->moveDestinations()->attach($newMove->id);
        }

        return $newMove->refresh();
    }

    protected function rewireDownstream(Move $move, ?Move $pushedMove): void
    {
        $toPropagate = collect();

        $toDetach = collect();

        $existing = $pushedMove ? collect([$pushedMove]) : collect();

        foreach ($move->moveDestinations->diff($existing) as $destination) {
            if ($pushedMove && $move->final_location_id && $destination->source_location_id === $move->final_location_id) {
                $toPropagate->push($destination);
            } elseif (! $destination->sourceLocation->isDescendantOf($move->destinationLocation)) {
                $toDetach->push($destination);
            }
        }

        foreach ($toDetach as $destination) {
            $destination->moveOrigins()->detach($move->id);

            $destination->procure_method = ProcureMethod::MAKE_TO_STOCK;

            $destination->computeState();

            $destination->save();
        }

        $move->moveDestinations()->detach($toPropagate->pluck('id')->all());

        $pushedMove?->moveDestinations()->syncWithoutDetaching($toPropagate->pluck('id')->all());
    }

    public function buildChainedMoveAttributes(Rule $rule, Move $source, Carbon $scheduledAt): array
    {
        $quantity = float_compare($source->product_uom_qty, 0, precisionRounding: $source->uom->rounding) < 0
            ? $source->product_uom_qty
            : $source->quantity;

        return [
            'state'                   => MoveState::DRAFT,
            'reference'               => null,
            'product_uom_qty'         => $quantity,
            'product_qty'             => $source->uom->computeQuantity($quantity, $source->product->uom, true, 'HALF-UP'),
            'quantity'                => 0,
            'is_picked'               => false,
            'origin'                  => $source->origin ?? $source->operation->name ?? '/',
            'operation_id'            => null,
            'source_location_id'      => $source->destination_location_id,
            'destination_location_id' => $rule->destination_location_id,
            'final_location_id'       => $source->final_location_id,
            'rule_id'                 => $rule->id,
            'scheduled_at'            => $scheduledAt,
            'company_id'              => $rule->company_id
                ?: ($rule->warehouse?->company_id ?? $rule->operationType?->warehouse?->company_id),
            'operation_type_id'       => $rule->operation_type_id,
            'warehouse_id'            => $rule->warehouse_id,
            'procure_method'          => ProcureMethod::MAKE_TO_ORDER,
        ];
    }
}
