<?php

namespace Webkul\Purchase\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Webkul\Invoice\Models\Bill as BaseBill;

class Bill extends BaseBill
{
    public function purchaseOrders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'purchases_order_account_moves', 'move_id', 'order_id');
    }
}
