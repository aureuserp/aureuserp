<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages\ManageQuantities as BaseManageQuantities;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource;

class ManageQuantities extends BaseManageQuantities
{
    protected static string $resource = LotResource::class;
}
