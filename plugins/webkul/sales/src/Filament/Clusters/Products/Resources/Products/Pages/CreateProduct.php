<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources\Products\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Products\Pages\CreateProduct as BaseCreateProduct;
use Webkul\Sale\Filament\Clusters\Products\Resources\Products\ProductResource;

class CreateProduct extends BaseCreateProduct
{
    protected static string $resource = ProductResource::class;
}
