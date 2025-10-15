<?php

namespace Webkul\Account\Filament\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\ListProducts as BaseListProducts;

class ListProducts extends BaseListProducts
{
    protected static string $resource = ProductResource::class;
}
