<?php

namespace Webkul\Manufacturing\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Webkul\Manufacturing\Models\Order;

class OrderDone
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Order $order
    ) {}
}
