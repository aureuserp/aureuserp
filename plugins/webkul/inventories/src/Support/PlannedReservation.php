<?php

namespace Webkul\Inventory\Support;

use Webkul\Inventory\Models\ProductQuantity;

class PlannedReservation
{
    public function __construct(
        public readonly ProductQuantity $stock,
        public readonly float $quantity,
    ) {}

    public function groupingKey(): string
    {
        return implode('_', [
            $this->stock->location_id,
            $this->stock->lot_id,
            $this->stock->package_id,
            $this->stock->partner_id,
        ]);
    }

    public function add(float $quantity): self
    {
        return new self($this->stock, $this->quantity + $quantity);
    }
}
