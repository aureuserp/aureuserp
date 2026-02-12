<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource\Pages\ViewProduct as BaseViewProduct;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource;

class ViewProduct extends BaseViewProduct
{
    protected static string $resource = ProductResource::class;
}
