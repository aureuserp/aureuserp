<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Webkul\Sale\Models\Category;

class TopCategoriesWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return _('sales::filament/widgets/top-categories.heading');
    }

    public function table(Table $table): table
    {
        $query = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(5);

        return $table
            ->defaultPaginationPageOption(5)
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('sales::filament/widgets/top-categories.table-columns.category'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label(__('sales::filament/widgets/top-categories.table-columns.category_full_name')),

                Tables\Columns\TextColumn::make('products_count')
                    ->label(__('sales::filament/widgets/top-categories.table-columns.product_count'))
                    ->sortable(),
            ]);
    }
}
