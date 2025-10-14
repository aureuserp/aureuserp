<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages\EditProductAttribute as BaseEditProductAttribute;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductAttributeResource;

class EditProductAttribute extends BaseEditProductAttribute
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
