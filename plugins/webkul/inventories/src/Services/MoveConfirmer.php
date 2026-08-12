<?php

namespace Webkul\Inventory\Services;

use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Support\ProcurementRequest;

class MoveConfirmer
{
    public function confirm(Collection $moves, bool $merge = true, ?Collection $mergeInto = null): Collection
    {
        if ($moves->isEmpty()) {
            return collect();
        }

        $pendingOperationGroups = $moves
            ->filter(fn (Move $move) => $move->state == MoveState::DRAFT && $move->needsOperation())
            ->groupBy(fn (Move $move) => implode('_', $move->operationGroupingKey()));

        $this->raiseProcurements($moves);

        $pendingOperationGroups->each(fn (Collection $group) => app(OperationAssembler::class)->assignMovesToOperations($group));

        if ($merge) {
            $moves = app(MoveMerger::class)->merge($moves, $mergeInto)
                ->map(fn (Move $move) => Move::find($move->id))
                ->filter();
        }

        $returnMoves = $moves->filter(
            fn (Move $move) => float_compare($move->product_uom_qty, 0, precisionRounding: $move->uom->rounding) < 0
        );

        $pushableReturns = $returnMoves->filter(
            fn (Move $move) => $move->final_location_id && $move->destination_location_id !== $move->final_location_id
        );

        $pushedMoves = $pushableReturns->isNotEmpty()
            ? app(PushRuleRunner::class)->run($pushableReturns)
            : collect();

        $returnMoves->each(fn (Move $move) => $this->flipDirection($move));

        app(OperationAssembler::class)->assignMovesToOperations($returnMoves);

        app(MoveReserver::class)->reserve($moves->filter(fn (Move $move) => $this->isReadyToReserve($move)));

        $this->confirmPushedMoves($pushedMoves);

        return $moves;
    }

    protected function raiseProcurements(Collection $moves): void
    {
        $needsProcurement = collect();

        $toConfirm = collect();

        $toWait = collect();

        foreach ($moves as $move) {
            if ($move->state != MoveState::DRAFT) {
                continue;
            }

            if ($move->moveOrigins->isNotEmpty()) {
                $toWait->push($move);
            } elseif ($move->procure_method === ProcureMethod::MAKE_TO_ORDER) {
                $toWait->push($move);

                $needsProcurement->push($move);
            } elseif ($move->rule?->procure_method === ProcureMethod::MTS_ELSE_MTO) {
                $needsProcurement->push($move);

                $toConfirm->push($move);
            } else {
                $toConfirm->push($move);
            }
        }

        $runner = app(ProcurementRunner::class);

        $quantities = $runner->shortfallQuantities($needsProcurement);

        $runner->run(
            $needsProcurement->zip($quantities)->map(
                fn (Collection $pair) => $this->requestFor($pair->first(), $pair->last())
            )
        );

        $toConfirm->each(fn (Move $move) => $move->update(['state' => MoveState::CONFIRMED]));

        $toWait->each(fn (Move $move) => $move->update(['state' => MoveState::WAITING]));

        $toConfirm->merge($toWait)
            ->filter(fn (Move $move) => $move->operationType?->reservation_method === ReservationMethod::AT_CONFIRM)
            ->each(fn (Move $move) => $move->update(['reservation_date' => now()]));
    }

    protected function requestFor(Move $move, float $quantity): ProcurementRequest
    {
        return new ProcurementRequest(
            product: $move->product,
            quantity: $quantity,
            uom: $move->uom,
            location: $move->sourceLocation,
            name: $move->rule?->name ?? '/',
            origin: $move->procurementOrigin(),
            company: $move->company,
            options: $move->buildProcurementOptions(),
        );
    }

    protected function flipDirection(Move $move): void
    {
        [$move->source_location_id, $move->destination_location_id, $move->final_location_id] = [
            $move->destination_location_id,
            $move->source_location_id,
            $move->source_location_id,
        ];

        $originIds = [];

        $destinationIds = [];

        foreach ($move->moveOrigins->merge($move->moveDestinations) as $related) {
            $from = $related->source_location_id;

            $to = $related->destination_location_id;

            if (float_compare($related->product_uom_qty, 0, precisionRounding: $related->uom->rounding) < 0) {
                [$from, $to] = [$to, $from];
            }

            if ($to === $move->source_location_id) {
                $originIds[] = $related->id;
            } elseif ($move->destination_location_id === $from) {
                $destinationIds[] = $related->id;
            }
        }

        $move->moveOrigins()->sync($originIds);

        $move->moveDestinations()->sync($destinationIds);

        $move->product_uom_qty *= -1;

        if ($move->operationType->return_operation_type_id) {
            $move->operation_type_id = $move->operationType->return_operation_type_id;
        }

        $move->procure_method = ProcureMethod::MAKE_TO_STOCK;

        $move->save();
    }

    protected function isReadyToReserve(Move $move): bool
    {
        if (! in_array($move->state, [MoveState::CONFIRMED, MoveState::PARTIALLY_ASSIGNED])) {
            return false;
        }

        return $move->bypassesReservation()
            || $move->operationType->reservation_method === ReservationMethod::AT_CONFIRM
            || ($move->reservation_date && $move->reservation_date <= now()->toDateString());
    }

    protected function confirmPushedMoves(Collection $pushedMoves): void
    {
        if ($pushedMoves->isEmpty()) {
            return;
        }

        $negative = $pushedMoves->filter(
            fn (Move $move) => float_compare($move->product_uom_qty, 0, precisionRounding: $move->uom->rounding) < 0
        );

        $this->confirm($pushedMoves->diff($negative));

        $this->confirm(
            $negative,
            mergeInto: $negative->flatMap->moveOrigins->flatMap->moveDestinations
        );
    }
}
