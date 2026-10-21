<?php

namespace Webkul\PointOfSale\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Webkul\PointOfSale\Models\Order;

class OrderCanceled
{
    use Dispatchable, SerializesModels;

    public function __construct(public Order $order) {}
}
