<?php

namespace Webkul\PointOfSale\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Throwable;
use Webkul\Inventory\Models\Operation;
use Webkul\PointOfSale\Models\Order;

class OrderOperationFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Order $order,
        public ?Operation $operation,
        public Throwable $exception,
    ) {}
}
