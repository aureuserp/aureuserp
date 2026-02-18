<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource\Pages\CreateProduct as BaseCreateProduct;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\ProductResource;

class CreateProduct extends BaseCreateProduct
{
    protected static string $resource = ProductResource::class;
}
