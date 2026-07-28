<?php

namespace Webkul\Inventory\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Webkul\Inventory\Models\Delivery;
use Webkul\Inventory\Models\Dropship;
use Webkul\Inventory\Models\InternalTransfer;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\Receipt;

class OperationConfirmed
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Operation|Receipt|InternalTransfer|Delivery|Dropship $operation
    ) {}
}
