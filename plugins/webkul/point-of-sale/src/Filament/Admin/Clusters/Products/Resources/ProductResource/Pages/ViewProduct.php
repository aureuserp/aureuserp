<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages\ViewProduct as BaseViewProduct;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource;

class ViewProduct extends BaseViewProduct
{
    protected static string $resource = ProductResource::class;
}
