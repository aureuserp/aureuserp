<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\Products\Pages;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Products\ProductResource;
use Webkul\Product\Filament\Resources\Products\Pages\ListProducts as BaseListProducts;
use Webkul\TableViews\Filament\Components\PresetView;

class ListProducts extends BaseListProducts
{
    protected static string $resource = ProductResource::class;

    public function getPresetTableViews(): array
    {
        return array_merge(parent::getPresetTableViews(), [
            'storable_products' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/list-products.tabs.inventory-management'))
                ->icon('heroicon-s-clipboard-document-list')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_storable', true)),
        ]);
    }
}
