<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributeResource\Pages;

use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductAttributeResource;
use Webkul\Product\Filament\Resources\AttributeResource\Pages\CreateAttribute;

class CreateProductAttribute extends CreateAttribute
{
    protected static string $resource = ProductAttributeResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
