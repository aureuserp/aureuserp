<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Inventory\Models\Delivery as BaseDelivery;

class Delivery extends BaseDelivery
{
    public function posOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'origin', 'name');
    }
}
