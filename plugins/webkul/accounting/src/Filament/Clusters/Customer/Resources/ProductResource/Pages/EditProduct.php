<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\ProductResource\Pages;

use Webkul\Accounting\Filament\Clusters\Customer\Resources\ProductResource;
use Webkul\Account\Filament\Resources\ProductResource\Pages\EditProduct as BaseEditProduct;

class EditProduct extends BaseEditProduct
{
    protected static string $resource = ProductResource::class;
}
