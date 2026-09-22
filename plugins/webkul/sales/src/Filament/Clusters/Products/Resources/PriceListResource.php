<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Product\Filament\Resources\PriceListResource as BasePriceListResource;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Sale\Filament\Clusters\Products;
use Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource\Pages\CreatePriceList;
use Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource\Pages\EditPriceList;
use Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource\Pages\ListPriceLists;
use Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource\Pages\ViewPriceList;

class PriceListResource extends BasePriceListResource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 6;

    protected static ?string $cluster = Products::class;

    public static function shouldRegisterNavigation(): bool
    {
        return parent::shouldRegisterNavigation()
            && app(ProductSettings::class)->enable_price_lists;
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
