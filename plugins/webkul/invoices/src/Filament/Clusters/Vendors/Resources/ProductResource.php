<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Resources\Pages\Page;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\ProductResource as BaseProductResource;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\EditProduct;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ListProducts;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ManageMoves;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ManageQuantities;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ManageVariants;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ManageVendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Invoice\Models\Product;
use Webkul\PluginManager\Package;

class ProductResource extends BaseProductResource
{
    use HasCustomFields;

    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $isGloballySearchable = true;

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Vendors::class;

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/product.navigation.title');
    }

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
