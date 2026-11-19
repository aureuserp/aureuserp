<?php

namespace Webkul\Asset\Filament\Resources\ProductResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Asset\Filament\Resources\ProductResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListProducts extends ListRecords
{
    use HasTableViews;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'active_products' => PresetView::make(__('assets::filament/resources/product.views.active'))
                ->icon('heroicon-s-check-circle')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn ($query) => $query->where('is_active', true)),

            'archived_products' => PresetView::make(__('assets::filament/resources/product.views.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn ($query) => $query->onlyTrashed()),
        ];
    }
}
