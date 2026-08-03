<?php

namespace Webkul\Inventory\Services;

use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Support\ReservationLedger;

class MoveReserver
{
    public function reserve(Collection $moves, mixed $forceQty = false): void
    {
        if ($moves->isEmpty()) {
            return;
        }

        $ledger = new ReservationLedger;

        $alreadyReserved = $moves->mapWithKeys(fn (Move $move) => [$move->id => $move->quantity]);

        $targets = $forceQty
            ? $moves
            : $moves->filter(fn (Move $move) => ! $move->is_picked
                && in_array($move->state, [MoveState::CONFIRMED, MoveState::WAITING, MoveState::PARTIALLY_ASSIGNED]));

        foreach ($targets as $move) {
            $rounding = $move->product->uom->rounding;

            $missingInMoveUom = $forceQty ? $forceQty : $move->product_uom_qty - $alreadyReserved[$move->id];

            if (float_compare($missingInMoveUom, 0, precisionRounding: $rounding) <= 0) {
                $ledger->reserved->push($move->id);

                continue;
            }

            $missing = $move->uom->computeQuantity($missingInMoveUom, $move->product->uom, roundingMethod: 'HALF-UP');

            if ($move->bypassesReservation()) {
                $this->reserveWithoutStock($ledger, $move, $missing);
            } elseif (! $this->reserveFromStock($ledger, $move, $missing, $rounding, $forceQty)) {
                continue;
            }

            if ($move->product->tracking === ProductTracking::SERIAL) {
                $move->update(['next_serial_count' => $move->product_uom_qty]);
            }
        }

        $ledger->pendingLines->each(fn (array $attributes) => MoveLine::create($attributes));

        Move::whereIn('id', $ledger->partiallyReserved)->get()
            ->each(fn (Move $move) => $move->update(['state' => MoveState::PARTIALLY_ASSIGNED]));

        Move::whereIn('id', $ledger->reserved)->get()
            ->each(fn (Move $move) => $move->update(['state' => MoveState::ASSIGNED]));

        $moves->each(fn (Move $move) => app(PackageLevelSynchronizer::class)->syncFor($move->operation));

        app(PutawayPlanner::class)->assignLocations(
            Move::whereIn('id', $ledger->touched)->with('lines')->get()->flatMap->lines
        );
    }

    public function release(Collection $moves): bool
    {
        $releasable = $moves->filter(function (Move $move) {
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

        $lineIds = $releasable->flatMap->lines
            ->reject(fn (MoveLine $line) => $line->is_picked)
            ->pluck('id');

        MoveLine::whereIn('id', $lineIds)->get()->each->delete();

        $releasable->each(function (Move $move) {
            $move->computeQuantity();

            $move->computeState();

            $move->saveQuietly();
        });

        return true;
    }

    protected function reserveWithoutStock(ReservationLedger $ledger, Move $move, float $missing): void
    {
        if ($move->moveOrigins->isNotEmpty()) {
            $missing = $this->drawFromUpstream($ledger, $move, $missing);
        }

        $tracksSerials = $move->product->tracking === ProductTracking::SERIAL
            && ($move->operationType->use_create_lots || $move->operationType->use_existing_lots);

        if ($missing && $tracksSerials) {
            for ($unit = 0; $unit < (int) $missing; $unit++) {
                $ledger->pendingLines->push($move->buildLineAttributes(quantity: 1));
            }
        } elseif ($missing) {
            $this->topUpPlainLine($ledger, $move, $missing);
        }

        $ledger->reserved->push($move->id);

        $ledger->touched->push($move->id);
    }

    protected function drawFromUpstream(ReservationLedger $ledger, Move $move, float $missing): float
    {
        foreach ($move->upstreamAvailability($ledger->reserved, $ledger->partiallyReserved) as $bucket => $quantity) {
            [$locationId, $lotId, $packageId] = $this->splitBucket($bucket);

            $taken = min($missing, $quantity);

            $ledger->pendingLines->push($move->buildLineAttributes($taken) + [
                'source_location_id' => $locationId,
                'lot_id'             => $lotId,
                'lot_name'           => $lotId ? Lot::find($lotId)?->name : null,
                'package_id'         => $packageId,
            ]);

            $missing -= $taken;

            if (float_is_zero($missing, precisionRounding: $move->product->uom->rounding)) {
                break;
            }
        }

        return $missing;
    }

    protected function topUpPlainLine(ReservationLedger $ledger, Move $move, float $missing): void
    {
        $reusable = $move->lines->first(fn (MoveLine $line) => $line->uom_id === $move->uom_id
            && $line->source_location_id === $move->source_location_id
            && $line->destination_location_id === $move->destination_location_id
            && $line->operation_id === $move->operation_id
            && ! $line->is_picked
            && ! $line->lot_id
            && ! $line->result_package_id
            && ! $line->package_id);

        if (! $reusable) {
            $ledger->pendingLines->push($move->buildLineAttributes(quantity: $missing));

            return;
        }

        $reusable->update([
            'qty' => $reusable->qty + $move->product->uom->computeQuantity($missing, $move->uom, roundingMethod: 'HALF-UP'),
        ]);
    }

    protected function reserveFromStock(ReservationLedger $ledger, Move $move, float $missing, float $rounding, mixed $forceQty): bool
    {
        if (float_is_zero($move->product_uom_qty, precisionRounding: $move->uom->rounding) && ! $forceQty) {
            $ledger->reserved->push($move->id);

            return true;
        }

        return $move->moveOrigins->isEmpty()
            ? $this->reserveFromLocation($ledger, $move, $missing, $rounding)
            : $this->reserveFromUpstreamStock($ledger, $move, $rounding);
    }

    protected function reserveFromLocation(ReservationLedger $ledger, Move $move, float $need, float $rounding): bool
    {
        if ($move->procure_method === ProcureMethod::MAKE_TO_ORDER) {
            return false;
        }

        if (float_is_zero($need, precisionRounding: $rounding)) {
            $ledger->reserved->push($move->id);

            return true;
        }

        $taken = $move->reserveFrom(
            $need,
            $move->sourceLocation,
            package: $move->packageLevel?->package,
            strict: false
        );

        if (float_is_zero($taken, precisionRounding: $rounding)) {
            return false;
        }

        $ledger->touched->push($move->id);

        if (float_compare($need, $taken, precisionRounding: $rounding) === 0) {
            $ledger->reserved->push($move->id);
        } else {
            $ledger->partiallyReserved->push($move->id);
        }

        return true;
    }

    protected function reserveFromUpstreamStock(ReservationLedger $ledger, Move $move, float $rounding): bool
    {
        $available = $move->upstreamAvailability($ledger->reserved, $ledger->partiallyReserved);

        if ($available === []) {
            return false;
        }

        foreach ($move->lines->filter(fn (MoveLine $line) => $line->uom_qty) as $line) {
            $bucket = implode('_', [$line->source_location_id, $line->lot_id, $line->package_id]);

            if (isset($available[$bucket])) {
                $available[$bucket] -= $line->uom_qty;
            }
        }

        foreach ($available as $bucket => $quantity) {
            [$locationId, $lotId, $packageId] = $this->splitBucket($bucket);

            $need = $move->product_qty - $move->lines->sum('uom_qty');

            $taken = $move->reserveFrom(
                min($quantity, $need),
                $locationId ? Location::find($locationId) : null,
                $lotId ? Lot::find($lotId) : null,
                $packageId ? Package::find($packageId) : null
            );

            if (float_is_zero($taken, precisionRounding: $rounding)) {
                continue;
            }

            $ledger->touched->push($move->id);

            if (float_is_zero($need - $taken, precisionRounding: $rounding)) {
                $ledger->reserved->push($move->id);

                break;
            }

            $ledger->partiallyReserved->push($move->id);
        }

        return true;
    }

    protected function splitBucket(string $bucket): array
    {
        $parts = explode('_', $bucket);

        return [
            $parts[0] ?: null,
            $parts[1] ?: null,
            $parts[2] ?: null,
        ];
    }
}
