<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductAttributeResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductAttributeResource;
use Webkul\Product\Filament\Resources\AttributeResource\Pages\ViewAttribute;

class ViewProductAttribute extends ViewAttribute
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
