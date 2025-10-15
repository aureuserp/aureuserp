<?php

namespace Webkul\Account\Filament\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\CreateProduct as BaseCreateProduct;

class CreateProduct extends BaseCreateProduct
{
    protected static string $resource = ProductResource::class;
}
