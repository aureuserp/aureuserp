<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageProducts extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = PackageResource::class;

    protected static string $relationship = 'quantities';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products/resources/package/pages/manage-products.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/package/pages/manage-products.table.columns.product')),
                TextColumn::make('lot.name')
                    ->label(__('inventories::filament/clusters/products/resources/package/pages/manage-products.table.columns.lot')),
                TextColumn::make('quantity')
                    ->label(__('inventories::filament/clusters/products/resources/package/pages/manage-products.table.columns.quantity')),
                TextColumn::make('product.uom.name')
                    ->label(__('inventories::filament/clusters/products/resources/package/pages/manage-products.table.columns.unit-of-measure')),
            ])
            ->paginated(false);
    }
}
