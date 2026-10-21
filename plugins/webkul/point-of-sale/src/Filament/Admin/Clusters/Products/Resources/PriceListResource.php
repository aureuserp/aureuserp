<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\PriceListResource\Pages\CreatePriceList;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\PriceListResource\Pages\EditPriceList;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\PriceListResource\Pages\ListPriceLists;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\PriceListResource\Pages\ViewPriceList;
use Webkul\PointOfSale\Models\Config;
use Webkul\Product\Filament\Resources\PriceListResource as BasePriceListResource;
use Webkul\Product\Settings\ProductSettings;

class PriceListResource extends BasePriceListResource
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Products::class;

    public static function shouldRegisterNavigation(): bool
    {
        if (! parent::shouldRegisterNavigation()) {
            return false;
        }

        return app(ProductSettings::class)->enable_price_lists
            || Config::query()->where('enable_price_list', true)->exists();
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/products/resources/price-list.navigation.title');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPriceList::class,
            EditPriceList::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPriceLists::route('/'),
            'create' => CreatePriceList::route('/create'),
            'view'   => ViewPriceList::route('/{record}'),
            'edit'   => EditPriceList::route('/{record}/edit'),
        ];
    }
}
