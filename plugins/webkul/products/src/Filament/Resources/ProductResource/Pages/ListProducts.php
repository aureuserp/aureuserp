<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\ProductResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListProducts extends ListRecords
{
    use HasTableViews;

    protected static string $resource = ProductResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereNull('parent_id');
            });
    }

    public function getPresetTableViews(): array
    {
        return [
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
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('products::filament/resources/product/pages/list-products.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
