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
        if (! Package::isPluginInstalled('inventories')
            || ! $product->isDirty('uom_id')) {
            return;
        }

        if (Move::query()->where('product_id', $product->getKey())->exists()) {
            throw ValidationException::withMessages([
                'uom_id' => __('You cannot change the unit of measure as there are already stock moves for this product. If you want to change the unit of measure, archive this product and create a new one.'),
            ]);
        }
    }
}
