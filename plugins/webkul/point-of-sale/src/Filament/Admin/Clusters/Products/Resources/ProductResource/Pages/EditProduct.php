<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages\EditProduct as BaseEditProduct;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource;

class EditProduct extends BaseEditProduct
{
    protected static string $resource = ProductResource::class;
}
