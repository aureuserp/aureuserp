<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages\ManageBillsOfMaterials as BaseManageBillsOfMaterials;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource;

class ManageBillsOfMaterials extends BaseManageBillsOfMaterials
{
    protected static string $resource = ProductResource::class;
}
