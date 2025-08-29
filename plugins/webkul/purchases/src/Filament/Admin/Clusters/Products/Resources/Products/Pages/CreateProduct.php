<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\Products\Pages;

use Webkul\Product\Filament\Resources\Products\Pages\CreateProduct as BaseCreateProduct;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\Products\ProductResource;

class CreateProduct extends BaseCreateProduct
{
    protected static string $resource = ProductResource::class;
}
