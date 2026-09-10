<?php

namespace Webkul\Inventory\Services;

use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Models\Move;

class MoveCanceller
{
    public function cancel(Collection $moves): bool
    {
        if ($moves->some(fn (Move $move) => $move->state === MoveState::DONE && ! $move->is_scraped)) {
            throw new \Exception(__('inventories::system.inventory-manager.cancel-move.already-done'));
        }

        $cancellable = $moves->filter(
            fn (Move $move) => $move->state !== MoveState::CANCELED
                && ! ($move->state === MoveState::DONE && $move->is_scraped)
        );

        $cancellable->each->update(['is_picked' => false]);

        app(MoveReserver::class)->release($cancellable);

        $cancellable->each->update(['state' => MoveState::CANCELED]);

        foreach ($cancellable as $move) {
            $this->propagate($move);
        }

        $cancellable->each(function (Move $move) {
            $move->update(['procure_method' => ProcureMethod::MAKE_TO_STOCK]);

            $move->moveOrigins()->detach();
        });

        return true;
    }

    protected function propagate(Move $move): void
    {
        $siblingStates = $move->moveDestinations
            ->flatMap
            ->moveOrigins
            ->diff(collect([$move]))
            ->pluck('state');

        if ($move->propagate_cancel) {
            if ($siblingStates->every(fn ($state) => $state === MoveState::CANCELED)) {
                $this->cancel(
                    $move->moveDestinations->filter(
                        fn (Move $destination) => $destination->state !== MoveState::DONE
                            && $destination->destination_location_id === $destination->moveDestinations->first()?->source_location_id
                    )
                );
            }

            return;
        }

        if (! $siblingStates->every(fn ($state) => in_array($state, [MoveState::DONE, MoveState::CANCELED]))) {
            return;
        }

        $move->moveDestinations->each(function (Move $destination) use ($move) {
            $destination->update(['procure_method' => ProcureMethod::MAKE_TO_STOCK]);

            $destination->moveOrigins()->detach($move->id);
        });
    }
}
