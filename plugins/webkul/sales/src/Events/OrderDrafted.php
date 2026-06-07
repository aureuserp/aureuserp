<?php

namespace Webkul\Sale\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Webkul\Sale\Models\Order;

class OrderDrafted
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Order $order
    ) {
    }
}
