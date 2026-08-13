<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource\Pages;

use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages\EditLot as BaseEditLot;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource;

class EditLot extends BaseEditLot
{
    protected static string $resource = LotResource::class;
}
