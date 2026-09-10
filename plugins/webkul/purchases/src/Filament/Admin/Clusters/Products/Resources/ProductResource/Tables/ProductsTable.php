<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource\Tables;

use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;

class ProductsTable
{
    /**
     * Receives the already-resolved parent table. The caller must build it with
     * parent::table() so late static binding stays on the calling resource --
     * the root ProductResource::table() resolves custom columns and filters from static::class.
     */
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
