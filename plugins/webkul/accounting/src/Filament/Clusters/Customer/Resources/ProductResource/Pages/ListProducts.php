<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\ProductResource\Pages;

use Webkul\Accounting\Filament\Clusters\Customer\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\ListProducts as BaseListProducts;

class ListProducts extends BaseListProducts
{
    protected static string $resource = ProductResource::class;
}
