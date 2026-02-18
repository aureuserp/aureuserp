<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages;

use Webkul\Account\Filament\Resources\ProductResource\Pages\ManageAttributes as BaseManageAttributes;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource;

class ManageAttributes extends BaseManageAttributes
{
    protected static string $resource = ProductResource::class;
}
