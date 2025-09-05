<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\Products\Pages;

use Webkul\Product\Filament\Resources\Products\Pages\EditProduct as BaseEditProduct;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\Products\ProductResource;

class EditProduct extends BaseEditProduct
{
    protected static string $resource = ProductResource::class;
}
