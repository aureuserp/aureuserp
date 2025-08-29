<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\Products\Pages;

use Webkul\Product\Filament\Resources\Products\Pages\ListProducts as BaseListProducts;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\Products\ProductResource;

class ListProducts extends BaseListProducts
{
    protected static string $resource = ProductResource::class;
}
