<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource\Pages\EditProduct as BaseEditProduct;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource;

class EditProduct extends BaseEditProduct
{
    protected static string $resource = ProductResource::class;
}
