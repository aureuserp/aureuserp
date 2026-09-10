<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackagingResource;
use Webkul\Product\Filament\Resources\PackagingResource as BasePackagingResource;

class PackagingsTable
{
    public static function configure(Table $table): Table
    {
        $table = BasePackagingResource::table($table);

        $columns = $table->getColumns();

        $filters = $table->getFilters();

        $columns[] = TextColumn::make('packageType.name')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.table.columns.package-type'))
            ->numeric()
            ->sortable()
            ->visible(PackagingResource::getOperationSettings()->enable_packages);

        $filters[] = SelectFilter::make('packageType')
            ->label(__('inventories::filament/clusters/configurations/resources/packaging.table.filters.package-type'))
            ->relationship('packageType', 'name')
            ->searchable()
            ->preload()
            ->visible(PackagingResource::getOperationSettings()->enable_packages);

        $table->columns($columns);

        $table->filters($filters);

        return $table;
    }
}
