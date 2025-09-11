<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Models\Category;

class TopCategoriesWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return __('sales::filament/widgets/top-categories.heading');
    }

    /**
     * 🔹 Build and return the table.
     */
    public function table(Table $table): Table
    {
        return $table
            ->query($this->baseQuery())
            ->defaultPaginationPageOption(5)
            ->columns($this->getTableColumns());
    }

    /**
     * 🔹 Base query for top categories.
     */
    protected function baseQuery(): Builder
    {
        return Category::query()
            ->withCount('products')
            ->orderByDesc('products_count')
            ->limit(5);
    }

    /**
     * 🔹 Define table columns.
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
