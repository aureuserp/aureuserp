<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources\Products\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Products\Pages\ListProducts as BaseListProducts;
use Webkul\Sale\Filament\Clusters\Products\Resources\Products\ProductResource;

class ListProducts extends BaseListProducts
{
    protected static string $resource = ProductResource::class;
}
