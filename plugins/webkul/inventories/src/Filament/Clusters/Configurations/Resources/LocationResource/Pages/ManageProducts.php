<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages;

use Closure;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Webkul\Inventory\Contracts\ProvidesQuantityLocation;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry;
use Webkul\Support\Traits\HasRecordNavigationTabs;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageProducts extends ManageRelatedRecords implements ProvidesQuantityLocation
{
    use HasRecordNavigationTabs, HasTableViews;

    protected static string $resource = LocationResource::class;

    protected static ?string $relatedResource = ProductResource::class;

    protected static string $relationship = 'products';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected ?Closure $quantityScope = null;

    public function getQuantityLocationId(): ?int
    {
        return $this->getOwnerRecord()->getKey();
    }

    public function getPresetTableViews(): array
    {
        return array_merge([
            'goods_products' => PresetView::make(__('products::filament/resources/product/pages/list-products.tabs.goods'))
                ->icon('heroicon-s-squares-plus')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('type', ProductType::GOODS)),

            'services_products' => PresetView::make(__('products::filament/resources/product/pages/list-products.tabs.services'))
                ->icon('heroicon-s-sparkles')
                ->favorite()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('type', ProductType::SERVICE)),

            'favorites_products' => PresetView::make(__('products::filament/resources/product/pages/list-products.tabs.favorites'))
                ->icon('heroicon-s-star')
                ->favorite()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->where('is_favorite', true)),

            'archived_products' => PresetView::make(__('products::filament/resources/product/pages/list-products.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (EloquentBuilder $query) => $query->onlyTrashed()),
        ], ProductSchemaRegistry::renderPresetViews());
    }

    public function table(Table $table): Table
    {
        return $table->query(fn (): EloquentBuilder => $this->getProductsQuery());
    }

    protected function getProductsQuery(): EloquentBuilder
    {
        return ProductResource::getEloquentQuery()
            ->whereNull('parent_id')
            ->whereIn('id', fn (QueryBuilder $query) => $query
                ->select('template_id')
                ->fromSub($this->getStockedTemplateIdsQuery(), 'location_stock'));
    }

    protected function getStockedTemplateIdsQuery(): QueryBuilder
    {
        $quantityScope = $this->getQuantityScope();

        return ProductQuantity::query()
            ->join('products_products', 'products_products.id', '=', 'inventories_product_quantities.product_id')
            ->where(fn (EloquentBuilder $query) => $quantityScope($query))
            ->selectRaw('DISTINCT COALESCE(products_products.parent_id, products_products.id) AS template_id')
            ->toBase();
    }

    protected function getQuantityScope(): Closure
    {
        return $this->quantityScope ??= (new Product)
            ->setContext(['location_id' => $this->getQuantityLocationId()])
            ->getLocationFilters()[0];
    }
}
