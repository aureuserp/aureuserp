<?php

namespace Webkul\Account\Filament\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\ViewProduct as BaseViewProduct;

class ViewProduct extends BaseViewProduct
{
    protected static string $resource = ProductResource::class;
}
