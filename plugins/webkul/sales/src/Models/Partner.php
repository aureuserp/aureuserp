<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Invoice\Models\Partner as BasePartner;
use Webkul\Product\Models\PriceList;

class Partner extends BasePartner
{
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'price_list_id',
        ]);

        parent::__construct($attributes);
    }

    /**
     * The price list quotations for this customer start from.
     */
    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class, 'price_list_id');
    }
}
