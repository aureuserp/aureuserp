<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Tables;

use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        $filtered = collect($table->getFilters()['queryBuilder']->getConstraints())
            ->reject(fn ($constraint) => $constraint->getName() == 'responsible')
            ->all();

        return $table->filters([
            QueryBuilder::make()
                ->constraints($filtered),
        ]);
    }
}
