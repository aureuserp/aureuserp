<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\Products\Pages;

use Webkul\Invoice\Filament\Clusters\Customer\Resources\Products\ProductResource;
use Webkul\Product\Filament\Resources\Products\Pages\ManageAttributes as BaseManageAttributes;

class ManageAttributes extends BaseManageAttributes
{
    protected static string $resource = ProductResource::class;
}
