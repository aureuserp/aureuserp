<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\Products\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\Products\ProductResource;
use Webkul\Product\Filament\Resources\Products\Pages\CreateProduct as BaseCreateProduct;

class CreateProduct extends BaseCreateProduct
{
    protected static string $resource = ProductResource::class;
}
