<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Lots\LotResource;

class CreateLot extends CreateRecord
{
    protected static string $resource = LotResource::class;
}
