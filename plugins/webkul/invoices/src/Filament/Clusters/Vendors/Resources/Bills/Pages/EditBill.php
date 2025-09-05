<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\Bills\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Account\Filament\Resources\Bills\Pages\EditBill as BaseEditBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\Bills\BillResource;

class EditBill extends BaseEditBill
{
    protected static string $resource = BillResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
