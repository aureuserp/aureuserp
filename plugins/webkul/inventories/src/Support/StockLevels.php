<?php

namespace Webkul\Inventory\Support;

class StockLevels
{
    public function __construct(
        public readonly float $onHand = 0.0,
        public readonly float $free = 0.0,
        public readonly float $incoming = 0.0,
        public readonly float $outgoing = 0.0,
        public readonly float $forecast = 0.0,
    ) {}

    public static function empty(): self
    {
        return new self;
    }

    public function plus(self $other): self
    {
        return new self(
            onHand: $this->onHand + $other->onHand,
            free: $this->free + $other->free,
            incoming: $this->incoming + $other->incoming,
            outgoing: $this->outgoing + $other->outgoing,
            forecast: $this->forecast + $other->forecast,
        );
    }

    public function rounded(float $rounding): self
    {
        return new self(
            onHand: float_round($this->onHand, precisionRounding: $rounding),
            free: float_round($this->free, precisionRounding: $rounding),
            incoming: float_round($this->incoming, precisionRounding: $rounding),
            outgoing: float_round($this->outgoing, precisionRounding: $rounding),
            forecast: float_round($this->forecast, precisionRounding: $rounding),
        );
    }
}
