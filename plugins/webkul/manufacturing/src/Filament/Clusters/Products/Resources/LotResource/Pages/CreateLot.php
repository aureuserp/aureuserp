<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages\CreateLot as BaseCreateLot;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource;

class CreateLot extends BaseCreateLot
{
    protected static string $resource = LotResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }
}
