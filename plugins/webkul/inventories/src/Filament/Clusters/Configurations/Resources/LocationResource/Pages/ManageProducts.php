<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Models\Location;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry;
use Webkul\Support\Traits\HasRecordNavigationTabs;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

/**
 * Lists the products stocked in a location using the inventory product
 * resource's own table, so this page shows the same columns, filters,
 * grouping and actions as the product list, limited to the location.
 */
class ManageProducts extends ManageRelatedRecords
{
    use HasRecordNavigationTabs, HasTableViews;

    protected static string $resource = LocationResource::class;

    protected static ?string $relatedResource = ProductResource::class;

    protected static string $relationship = 'products';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    /**
     * The same tabs the product list offers, so this page behaves like it.
     */
    public function getPresetTableViews(): array
    {
        return array_merge([
            'goods_products' => PresetView::make(__('products::filament/resources/product/pages/list-products.tabs.goods'))
                ->icon('heroicon-s-squares-plus')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ProductType::GOODS)),

            'services_products' => PresetView::make(__('products::filament/resources/product/pages/list-products.tabs.services'))
                ->icon('heroicon-s-sparkles')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', ProductType::SERVICE)),

            'favorites_products' => PresetView::make(__('products::filament/resources/product/pages/list-products.tabs.favorites'))
                ->icon('heroicon-s-star')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_favorite', true)),

            'archived_products' => PresetView::make(__('products::filament/resources/product/pages/list-products.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ], ProductSchemaRegistry::renderPresetViews());
    }

    public function table(Table $table): Table
    {
        // The related resource configures the columns, filters and actions
        // after this method runs; only the query is ours to define.
        return $table->query(fn (): Builder => $this->getProductsQuery());
    }

    /**
     * Product templates holding stock in this location, either directly or
     * through one of their variants.
     */
    protected function getProductsQuery(): Builder
    {
        $locationIds = $this->getScopedLocationIds();

        return ProductResource::getEloquentQuery()
            ->whereNull('parent_id')
            ->where(fn (Builder $query) => $query
                ->whereHas('quantities', fn (Builder $quantities) => $quantities->whereIn('location_id', $locationIds))
                ->orWhereHas('variants.quantities', fn (Builder $quantities) => $quantities->whereIn('location_id', $locationIds)));
    }

    /**
     * The location and every location nested under it, matching how the
     * inventory plugin aggregates quantities elsewhere.
     *
     * @return array<int, int>
     */
    protected function getScopedLocationIds(): array
    {
        $location = $this->getOwnerRecord();

        if (blank($location->parent_path)) {
            return [$location->getKey()];
        }

        return Location::query()
            ->where('parent_path', 'like', $location->parent_path.'%')
            ->pluck('id')
            ->all();
    }
}
