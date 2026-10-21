<?php

namespace Webkul\PointOfSale\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Webkul\PointOfSale\Models\CashMovement;

class CashMovementRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(public CashMovement $cashMovement) {}
}
