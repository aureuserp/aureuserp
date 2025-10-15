<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Models\Category;

class TopCategoriesWidget extends BaseWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    /**
     * 🔹 Table query for top categories.
     */
    protected function getTableQuery(): Builder
    {
        return Category::query()
            ->withCount('products')
            ->orderByDesc('products_count')
            ->limit(5);
    }

    /**
     * 🔹 Table columns.
     */
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/pages/sales-dashboard.widgets.top-categories.column.category')),
            Tables\Columns\TextColumn::make('full_name')
                ->label(__('sales::filament/pages/sales-dashboard.widgets.top-categories.column.category_full_name')),

            Tables\Columns\TextColumn::make('products_count')
                ->label(__('sales::filament/pages/sales-dashboard.widgets.top-categories.column.product_count'))
                ->sortable(),
        ];
    }

    protected function getHeading(): ?string
    {
        return __('sales::filament/pages/sales-dashboard.widgets.top-categories.heading');
    }
}
