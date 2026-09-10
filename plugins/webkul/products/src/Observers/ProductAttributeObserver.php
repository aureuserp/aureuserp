<?php

namespace Webkul\Product\Observers;

use Webkul\Product\Exceptions\ProductInUseException;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttribute;

class ProductAttributeObserver
{
    public function creating(ProductAttribute $productAttribute): void
    {
        $product = Product::find($productAttribute->product_id);

        if ($product && ! $product->is_configurable && $product->isInUse()) {
            throw ProductInUseException::make($product, 'attributes');
        }
    }
}
