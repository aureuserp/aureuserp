<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Account\Filament\Resources\BillResource\Pages\EditBill as BaseEditBill;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;

class EditBill extends BaseEditBill
{
    protected static string $resource = BillResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
