<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources;

use Filament\Resources\Pages\Page;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource as BaseProductResource;
use Webkul\PluginManager\Package;
use Webkul\Sale\Filament\Clusters\Products;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\EditProduct;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\ListProducts;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageMoves;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageQuantities;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageVariants;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageVendors;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Sale\Models\Product;

class ProductResource extends BaseProductResource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Products::class;

    public static function getRecordSubNavigation(Page $page): array
    {
        $items = [
            ViewProduct::class,
            EditProduct::class,
            ManageAttributes::class,
            ManageVariants::class,
        ];

        if (Package::isPluginInstalled('purchases')) {
            $items[] = ManageVendors::class;
        }

        if (Package::isPluginInstalled('inventories')) {
            $items[] = ManageQuantities::class;
            $items[] = ManageMoves::class;
        }

        return $page->generateNavigationItems($items);
    }

    public static function table(Table $table): Table
    {
        $table = parent::table($table);

        $filtered = collect($table->getFilters()['queryBuilder']->getConstraints())
            ->reject(fn ($constraint) => $constraint->getName() == 'responsible')
            ->all();

        $table = $table->filters([
            QueryBuilder::make()
                ->constraints($filtered),
        ]);

        return $table;
    }

    public static function getPages(): array
    {
        $pages = [
            'index'      => ListProducts::route('/'),
            'create'     => CreateProduct::route('/create'),
            'view'       => ViewProduct::route('/{record}'),
            'edit'       => EditProduct::route('/{record}/edit'),
            'attributes' => ManageAttributes::route('/{record}/attributes'),
            'variants'   => ManageVariants::route('/{record}/variants'),
        ];

        if (Package::isPluginInstalled('purchases')) {
            $pages['vendors'] = ManageVendors::route('/{record}/vendors');
        }

        if (Package::isPluginInstalled('inventories')) {
            $pages['quantities'] = ManageQuantities::route('/{record}/quantities');
            $pages['moves'] = ManageMoves::route('/{record}/moves');
        }

        return $pages;
    }
}
