<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource\Pages\CreateProduct as BaseCreateProduct;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource;

class CreateProduct extends BaseCreateProduct
{
    protected static string $resource = ProductResource::class;
}
