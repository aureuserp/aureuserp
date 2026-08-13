<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource\Pages\ListProducts as BaseListProducts;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource;

class ListProducts extends BaseListProducts
{
    protected static string $resource = ProductResource::class;
}
