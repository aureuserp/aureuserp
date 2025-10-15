<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\ProductResource\Pages;

use Webkul\Accounting\Filament\Clusters\Customer\Resources\ProductResource;
use Webkul\Account\Filament\Resources\ProductResource\Pages\CreateProduct as BaseCreateProduct;

class CreateProduct extends BaseCreateProduct
{
    protected static string $resource = ProductResource::class;
}
