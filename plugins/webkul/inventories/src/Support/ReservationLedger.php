<?php

namespace Webkul\Inventory\Support;

use Illuminate\Support\Collection;

class ReservationLedger
{
    public Collection $reserved;

    public Collection $partiallyReserved;

    public Collection $touched;

    public Collection $pendingLines;

    public function __construct()
    {
        $this->reserved = collect();

        $this->partiallyReserved = collect();

        $this->touched = collect();

        $this->pendingLines = collect();
    }
}
