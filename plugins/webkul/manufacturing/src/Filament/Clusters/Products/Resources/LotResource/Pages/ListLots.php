<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages\ListLots as BaseListLots;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource;

class ListLots extends BaseListLots
{
    protected static string $resource = LotResource::class;
}
