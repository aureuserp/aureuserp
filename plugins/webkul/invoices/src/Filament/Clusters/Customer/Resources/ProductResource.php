<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Invoice\Filament\Clusters\Customer;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource\Pages\EditProduct;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource\Pages\ListProducts;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource\Pages\ManageVariants;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Invoice\Models\Product;
use Webkul\Product\Filament\Resources\ProductResource as BaseProductResource;

class ProductResource extends BaseProductResource
{
    protected static ?string $model = Product::class;

    protected static ?string $cluster = Customer::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/products.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers/resources/products.navigation.title');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProduct::class,
            EditProduct::class,
            ManageAttributes::class,
            ManageVariants::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListProducts::route('/'),
            'create'     => CreateProduct::route('/create'),
            'view'       => ViewProduct::route('/{record}'),
            'edit'       => EditProduct::route('/{record}/edit'),
            'attributes' => ManageAttributes::route('/{record}/attributes'),
            'variants'   => ManageVariants::route('/{record}/variants'),
        ];
    }
}
