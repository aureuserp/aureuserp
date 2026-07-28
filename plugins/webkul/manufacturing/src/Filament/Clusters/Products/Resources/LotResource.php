<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource as BaseLotResource;
use Webkul\Manufacturing\Filament\Clusters\Products;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource\Pages\CreateLot;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource\Pages\EditLot;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource\Pages\ListLots;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource\Pages\ManageQuantities;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource\Pages\ViewLot;
use Webkul\Manufacturing\Models\Lot;

class LotResource extends BaseLotResource
{
    protected static ?string $model = Lot::class;

    protected static ?string $cluster = Products::class;

    protected static ?int $navigationSort = 3;

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewLot::class,
            EditLot::class,
            ManageQuantities::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListLots::route('/'),
            'create'     => CreateLot::route('/create'),
            'view'       => ViewLot::route('/{record}'),
            'edit'       => EditLot::route('/{record}/edit'),
            'quantities' => ManageQuantities::route('/{record}/quantities'),
        ];
    }
}
