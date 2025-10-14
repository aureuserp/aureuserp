<?php

namespace Webkul\Purchase\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Webkul\Account\Models\Move;

class AccountMove extends Move
{
    public function lines()
    {
        return $this->hasMany(AccountMoveLine::class, 'move_id');
    }

    public function purchaseOrders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'purchases_order_account_moves', 'move_id', 'order_id');
    }
}
