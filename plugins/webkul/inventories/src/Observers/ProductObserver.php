<?php

namespace Webkul\Inventory\Observers;

use Illuminate\Validation\ValidationException;
use Webkul\Inventory\Models\Move;
use Webkul\PluginManager\Package;
use Webkul\Product\Models\Product;

class ProductObserver
{
    public function updating(Product $product): void
    {
        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        if (! $product->isDirty('uom_id')) {
            return;
        }

        if (! Move::where('product_id', $product->id)->exists()) {
            return;
        }

        throw ValidationException::withMessages([
            'data.uom_id' => __('inventories::observers/product.uom-change'),
        ]);
    }
}
