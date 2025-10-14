<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Models\Category;

class TopCategoriesWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected function getHeading(): ?string
    {
        return __('sales::filament/widgets/top-categories.heading');
    }

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
                ->label(__('sales::filament/widgets/top-categories.table-columns.category'))
                ->searchable(),

            Tables\Columns\TextColumn::make('full_name')
                ->label(__('sales::filament/widgets/top-categories.table-columns.category_full_name')),

            Tables\Columns\TextColumn::make('products_count')
                ->label(__('sales::filament/widgets/top-categories.table-columns.product_count'))
                ->sortable(),
        ];
    }
}
