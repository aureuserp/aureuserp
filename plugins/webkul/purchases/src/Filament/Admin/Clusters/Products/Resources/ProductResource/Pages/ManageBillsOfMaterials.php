<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageBillsOfMaterials as BaseManageBillsOfMaterials;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource;

class ManageBillsOfMaterials extends BaseManageBillsOfMaterials
{
    protected static string $resource = ProductResource::class;
}
