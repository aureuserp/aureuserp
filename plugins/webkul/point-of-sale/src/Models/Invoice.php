<?php

namespace Webkul\PointOfSale\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Invoice\Models\Invoice as BaseInvoice;

class Invoice extends BaseInvoice
{
    public function posOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'invoice_origin', 'name');
    }
}
