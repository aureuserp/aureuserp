<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'quantities';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/package/relation-managers/products.table.columns.product')),
                TextColumn::make('lot.name')
                    ->label(__('inventories::filament/clusters/products/resources/package/relation-managers/products.table.columns.lot')),
                TextColumn::make('quantity')
                    ->label(__('inventories::filament/clusters/products/resources/package/relation-managers/products.table.columns.quantity')),
                TextColumn::make('product.uom.name')
                    ->label(__('inventories::filament/clusters/products/resources/package/relation-managers/products.table.columns.unit-of-measure')),
            ])
            ->paginated(false);
    }
}
