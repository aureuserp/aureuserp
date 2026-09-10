<?php

namespace Webkul\Inventory\Services;

use BackedEnum;
use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Models\Move;

class MoveStateResolver
{
    protected const PRIORITY = [
        MoveState::ASSIGNED->value           => 4,
        MoveState::WAITING->value            => 3,
        MoveState::PARTIALLY_ASSIGNED->value => 2,
        MoveState::CONFIRMED->value          => 1,
    ];

    public function relevantState(Collection $moves): BackedEnum
    {
        $pending = $moves
            ->filter(fn (Move $move) => ! in_array($move->state, [MoveState::CANCELED, MoveState::DONE])
                && ! ($move->state === MoveState::ASSIGNED && ! $move->product_uom_qty))
            ->sortBy([
                fn (Move $a, Move $b) => $this->priorityOf($a) <=> $this->priorityOf($b),
                fn (Move $a, Move $b) => $a->product_uom_qty <=> $b->product_uom_qty,
            ])
            ->values();

        if ($pending->isEmpty()) {
            return MoveState::ASSIGNED;
        }

        $mostBlocking = $pending->first();

        if ($mostBlocking->operation?->move_type === MoveType::ONE) {
            return $this->stateForAllOrNothing($pending, $mostBlocking);
        }

        $partiallyCovered = $mostBlocking->state !== MoveState::ASSIGNED
            && $pending->some(fn (Move $move) => in_array($move->state, [MoveState::ASSIGNED, MoveState::PARTIALLY_ASSIGNED]));

        if ($partiallyCovered) {
            return MoveState::PARTIALLY_ASSIGNED;
        }

        $leastBlocking = $pending->last();

        if ($leastBlocking->state === MoveState::CONFIRMED && $leastBlocking->product_uom_qty == 0) {
            return MoveState::ASSIGNED;
        }

        return $leastBlocking->state ?? MoveState::DRAFT;
    }

    protected function stateForAllOrNothing(Collection $pending, Move $mostBlocking): BackedEnum
    {
        if ($pending->every(fn (Move $move) => ! $move->product_uom_qty)) {
            return MoveState::ASSIGNED;
        }

        return in_array($mostBlocking->state, [MoveState::CONFIRMED, MoveState::PARTIALLY_ASSIGNED])
            ? MoveState::CONFIRMED
            : ($mostBlocking->state ?? MoveState::DRAFT);
    }

    protected function priorityOf(Move $move): int
    {
        return static::PRIORITY[$move->state->value] ?? 0;
    }
}
