<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Products\Pages;

use Webkul\Invoice\Filament\Clusters\Customer\Resources\Products\ProductResource;
use Webkul\Product\Filament\Resources\Products\Pages\CreateProduct as BaseCreateProduct;

class CreateProduct extends BaseCreateProduct
{
    protected static string $resource = ProductResource::class;
}
