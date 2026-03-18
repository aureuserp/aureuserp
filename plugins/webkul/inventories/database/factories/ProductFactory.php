<?php

namespace Webkul\Inventory\Database\Factories;

use Webkul\Inventory\Models\Product;
use Webkul\Product\Database\Factories\ProductFactory as BaseProductFactory;

/**
 * @extends BaseProductFactory<Product>
 */
class ProductFactory extends BaseProductFactory
{
    protected $model = Product::class;
}
