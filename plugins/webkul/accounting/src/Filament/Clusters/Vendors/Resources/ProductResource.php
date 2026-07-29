<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\ProductResource as BaseProductResource;
use Webkul\Accounting\Filament\Clusters\Vendors;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource\Pages\EditProduct;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ListProducts;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ManageBillsOfMaterials;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ManageVariants;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Accounting\Models\Product;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\PluginManager\Package;

class ProductResource extends BaseProductResource
{
    use HasCustomFields;

    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $isGloballySearchable = true;

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = Vendors::class;

    public static function getRecordSubNavigation(Page $page): array
    {
        $items = [
            ViewProduct::class,
            EditProduct::class,
            ManageAttributes::class,
            ManageVariants::class,
        ];

        if (Package::isPluginInstalled('manufacturing')) {
            $items[] = ManageBillsOfMaterials::class;
        }

        return $page->generateNavigationItems($items);
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

        if (Package::isPluginInstalled('manufacturing')) {
            $pages['boms'] = ManageBillsOfMaterials::route('/{record}/boms');
        }

        return $pages;
    }
}
