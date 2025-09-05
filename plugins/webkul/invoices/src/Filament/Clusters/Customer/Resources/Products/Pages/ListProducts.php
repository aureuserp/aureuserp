<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Products\Pages;

use Webkul\Invoice\Filament\Clusters\Customer\Resources\Products\ProductResource;
use Webkul\Product\Filament\Resources\Products\Pages\ListProducts as BaseListProducts;

class ListProducts extends BaseListProducts
{
    protected static string $resource = ProductResource::class;
}
