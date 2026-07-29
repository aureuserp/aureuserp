<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Webkul\Invoice\Models\Invoice as BaseInvoice;

class Invoice extends BaseInvoice
{
    public function salesOrders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'sales_order_invoices', 'move_id', 'order_id');
    }
}
