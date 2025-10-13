<?php

namespace Webkul\Purchase\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Account\Models\MoveLine;

class AccountMoveLine extends MoveLine
{
    /**
     * Create a new Eloquent model instance.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'purchase_order_line_id',
        ]);

        parent::__construct($attributes);
    }

    public function move()
    {
        return $this->belongsTo(AccountMove::class);
    }

    public function purchaseOrderLine(): BelongsTo
    {
        return $this->belongsTo(OrderLine::class, 'purchase_order_line_id');
    }
}
