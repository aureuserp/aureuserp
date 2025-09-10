<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Products\Pages;

use Webkul\Invoice\Filament\Clusters\Customer\Resources\Products\ProductResource;
use Webkul\Product\Filament\Resources\Products\Pages\EditProduct as BaseEditProduct;

class EditProduct extends BaseEditProduct
{
    protected static string $resource = ProductResource::class;

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/products.sub_navigation.edit-product');
    }
}
