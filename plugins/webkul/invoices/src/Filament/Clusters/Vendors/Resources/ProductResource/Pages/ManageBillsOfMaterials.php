<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages;

use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageBillsOfMaterials as BaseManageBillsOfMaterials;

class ManageBillsOfMaterials extends BaseManageBillsOfMaterials
{
    protected static string $resource = ProductResource::class;
}
