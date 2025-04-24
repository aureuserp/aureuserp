<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Products\Resources;

use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Product\Filament\Resources\CustomProductResource as BaseProductResource;
use Webkul\Purchase\Filament\Admin\Clusters\Products;
use Webkul\Purchase\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages;
use Webkul\Purchase\Models\Product;

class ProductResource extends BaseProductResource
{
    use HasCustomFields;

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Products::class;

    protected static ?int $navigationSort = 1;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/products/resources/product.navigation.title');
    }

    public static function form(Form $form): Form
    {
        $form = BaseProductResource::form($form);

        $components = $form->getComponents();

        $form->components($components);

        return $form;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        $infolist = BaseProductResource::infolist($infolist);

        $components = $infolist->getComponents();

        $infolist->components($components);

        return $infolist;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewProduct::class,
            Pages\EditProduct::class,
            Pages\ManageAttributes::class,
            Pages\ManageVariants::class,
            Pages\ManageVendors::class,
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
            'vendors'    => Pages\ManageVendors::route('/{record}/vendors'),
        ];
    }
}
