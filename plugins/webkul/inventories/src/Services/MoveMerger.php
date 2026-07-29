<?php

namespace Webkul\Inventory\Services;

use BackedEnum;
use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Operation;

class MoveMerger
{
    protected const IDENTITY_FIELDS = [
        'product_id',
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

    protected const PRICE_PRECISION = 2;

    public function merge(Collection $moves, ?Collection $mergeInto = null): Collection
    {
        $moves->each(fn (Move $move) => $move->load('operation'));

        $returnMoves = $moves->filter(
            fn (Move $move) => float_compare($move->product_qty, 0.0, precisionRounding: $move->uom->rounding) < 0
        )->each(fn (Move $move) => $move->operation_id = null);

        $obsolete = collect();

        $merged = collect();

        $toCancel = collect();

        $survivorsByReturnKey = collect();

        foreach ($this->candidateSets($moves, $mergeInto) as $candidates) {
            $candidates = $candidates
                ->filter(fn (Move $move) => ! in_array($move->state, [MoveState::DRAFT, MoveState::DONE, MoveState::CANCELED]))
                ->diff($returnMoves)
                ->sortBy('id')
                ->values();

            foreach ($candidates->groupBy(fn (Move $move) => $this->identityKey($move)) as $group) {
                if ($group->count() > 1) {
                    $merged->push($this->collapse($group, (bool) $mergeInto, $obsolete));
                }

                $key = $this->returnKey($group->first());

                $survivorsByReturnKey->put(
                    $key,
                    $survivorsByReturnKey->get($key, collect())->push($group->first())
                );
            }
        }

        foreach ($returnMoves as $returnMove) {
            $this->offsetAgainstReturn($returnMove, $survivorsByReturnKey, $merged, $obsolete, $toCancel);
        }

        $this->discard($obsolete);

        if ($toCancel->isNotEmpty()) {
            $this->cancellation()->cancel($toCancel->filter(fn (Move $move) => ! $move->is_picked));
        }

        return $moves->merge($merged)->reject(fn (Move $move) => $obsolete->contains('id', $move->id));
    }

    public function mergedAttributes(Collection $moves, bool $keepFirstQuantity = false): array
    {
        $allDirect = $moves->pluck('operation')->every(
            fn (?Operation $operation) => $operation?->move_type === MoveType::DIRECT
        );

        return [
            'product_uom_qty'   => $keepFirstQuantity ? $moves->first()->product_uom_qty : $moves->sum('product_uom_qty'),
            'product_qty'       => $keepFirstQuantity ? $moves->first()->product_qty : $moves->sum('product_qty'),
            'date'              => $allDirect ? $moves->min('date') : $moves->max('date'),
            'state'             => $this->stateResolver()->relevantState($moves),
            'origin'            => $moves->pluck('origin')->filter()->unique()->implode('/'),
            'move_destinations' => $moves->flatMap->moveDestinations,
            'move_origins'      => $moves->flatMap->moveOrigins,
        ];
    }

    protected function candidateSets(Collection $moves, ?Collection $mergeInto): array
    {
        if ($mergeInto) {
            return [$mergeInto->merge($moves)];
        }

        return $moves
            ->pluck('operation')
            ->filter()
            ->unique('id')
            ->mapWithKeys(fn (Operation $operation) => [$operation->id => $operation->moves])
            ->all();
    }

    protected function collapse(Collection $group, bool $keepFirstQuantity, Collection $obsolete): Move
    {
        $survivor = $group->first();

        $group->flatMap->lines->each(fn (MoveLine $line) => $line->update(['move_id' => $survivor->id]));

        $attributes = $this->mergedAttributes($group, $keepFirstQuantity);

        $destinations = $attributes['move_destinations'];

        $origins = $attributes['move_origins'];

        unset($attributes['move_destinations'], $attributes['move_origins']);

        $survivor->update($attributes);

        $survivor->moveDestinations()->sync($destinations->pluck('id')->all());

        $survivor->moveOrigins()->sync($origins->pluck('id')->all());

        $obsolete->push(...$group->skip(1));

        return $survivor;
    }

    protected function offsetAgainstReturn(
        Move $returnMove,
        Collection $survivorsByReturnKey,
        Collection $merged,
        Collection $obsolete,
        Collection $toCancel
    ): void {
        foreach ($survivorsByReturnKey->get($this->returnKey($returnMove), collect()) as $outbound) {
            if (float_compare($outbound->price_unit, $returnMove->price_unit, precisionDigits: static::PRICE_PRECISION) !== 0) {
                continue;
            }

            $combinedValue = $outbound->product_qty * $outbound->price_unit + $returnMove->product_qty * $returnMove->price_unit;

            $returnFitsInside = float_compare(
                $outbound->product_uom_qty,
                abs($returnMove->product_uom_qty),
                precisionRounding: $outbound->uom->rounding
            ) >= 0;

            if (! $returnFitsInside) {
                $returnMove->product_qty += $outbound->product_qty;

                $returnMove->product_uom_qty += $outbound->product_uom_qty;

                $returnMove->price_unit = round($combinedValue / $returnMove->product_qty, static::PRICE_PRECISION);

                $outbound->product_uom_qty = 0;

                $outbound->save();

                $toCancel->push($outbound);

                continue;
            }

            $outbound->product_uom_qty += $returnMove->product_uom_qty;

            $outbound->product_qty += $returnMove->product_qty;

            $destinationIds = $returnMove->moveDestinations
                ->filter(fn (Move $move) => $move->source_location_id === $outbound->destination_location_id)
                ->pluck('id')
                ->all();

            $originIds = $returnMove->moveOrigins
                ->filter(fn (Move $move) => $move->destination_location_id === $outbound->source_location_id)
                ->pluck('id')
                ->all();

            $outbound->update([
                'price_unit' => $outbound->product_qty
                    ? round($combinedValue / $outbound->product_qty, static::PRICE_PRECISION)
                    : 0,
            ]);

            $outbound->moveDestinations()->syncWithoutDetaching($destinationIds);

            $outbound->moveOrigins()->syncWithoutDetaching($originIds);

            $merged->push($outbound);

            $obsolete->push($returnMove);

            if (float_is_zero($outbound->product_uom_qty, precisionRounding: $outbound->uom->rounding)) {
                $toCancel->push($outbound);
            }

            break;
        }
    }

    protected function discard(Collection $obsolete): void
    {
        if ($obsolete->isEmpty()) {
            return;
        }

        $obsolete->each->unsetRelation('lines');

        $this->cancellation()->cancel($obsolete);

        foreach ($obsolete as $move) {
            $move->lines()->get()->each(fn (MoveLine $line) => $line->delete());

            $move->delete();
        }
    }

    protected function identityKey(Move $move): string
    {
        return $this->keyFrom($move, static::IDENTITY_FIELDS);
    }

    protected function returnKey(Move $move): string
    {
        return $this->keyFrom($move, array_values(array_diff(static::IDENTITY_FIELDS, ['description_picking', 'price_unit'])));
    }

    protected function keyFrom(Move $move, array $fields): string
    {
        return collect($fields)
            ->map(fn (string $field) => $move->{$field} instanceof BackedEnum
                ? $move->{$field}->value
                : (string) $move->{$field})
            ->implode('_');
    }

    protected function cancellation(): MoveCanceller
    {
        return app(MoveCanceller::class);
    }

    protected function stateResolver(): MoveStateResolver
    {
        return app(MoveStateResolver::class);
    }
}
