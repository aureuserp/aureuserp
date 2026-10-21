<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages\ListProducts as BaseListProducts;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource;

class ListProducts extends BaseListProducts
{
    protected static string $resource = ProductResource::class;

    public function getPresetTableViews(): array
    {
        $presetViews = parent::getPresetTableViews();

        foreach ($presetViews as $key => $presetView) {
            $presetView->setAsDefault($key === 'pos_products');
        }

        return $presetViews;
    }
}
