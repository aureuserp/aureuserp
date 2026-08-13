<?php

namespace Webkul\Inventory\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class StockQueryScopes
{
    public function __construct(
        protected Closure $quantities,
        protected Closure $incomingMoves,
        protected Closure $outgoingMoves,
    ) {}

    public static function matchingNothing(): self
    {
        $none = fn (Builder $query) => $query->whereRaw('0 = 1');

        return new self($none, $none, $none);
    }

    public function quantities(Builder $query): Builder
    {
        return ($this->quantities)($query);
    }

    public function incomingMoves(Builder $query): Builder
    {
        return ($this->incomingMoves)($query);
    }

    public function outgoingMoves(Builder $query): Builder
    {
        return ($this->outgoingMoves)($query);
    }
}
