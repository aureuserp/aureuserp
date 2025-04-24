<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources;

use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Webkul\Invoice\Filament\Clusters\Customer;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource\Pages;
use Webkul\Invoice\Models\Product;
use Webkul\Product\Filament\Resources\CustomProductResource as BaseProductResource;

class ProductResource extends BaseProductResource
{
    protected static ?string $model = Product::class;

    protected static ?string $cluster = Customer::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
            Pages\ViewProduct::class,
            Pages\EditProduct::class,
            Pages\ManageAttributes::class,
            Pages\ManageVariants::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => Pages\ListProducts::route('/'),
            'create'     => Pages\CreateProduct::route('/create'),
            'view'       => Pages\ViewProduct::route('/{record}'),
            'edit'       => Pages\EditProduct::route('/{record}/edit'),
            'attributes' => Pages\ManageAttributes::route('/{record}/attributes'),
            'variants'   => Pages\ManageVariants::route('/{record}/variants'),
        ];
    }
}
