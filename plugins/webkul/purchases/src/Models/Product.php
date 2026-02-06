<?php

namespace Webkul\Purchase\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Invoice\Models\Product as BaseProduct;

class Product extends BaseProduct
{
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([]);

        $this->mergeCasts([]);

        parent::__construct($attributes);
    }

    public function supplierInformation(): HasMany
    {
        if ($this->is_configurable) {
            return $this->hasMany(ProductSupplier::class)
                ->orWhereIn('product_id', $this->variants()->pluck('id'));
        } else {
            return $this->hasMany(ProductSupplier::class);
        }
    }
}
