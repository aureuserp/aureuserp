<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Account\Filament\Resources\RefundResource\Pages\EditRefund as BaseEditRefund;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource;

class EditRefund extends BaseEditRefund
{
    protected static string $resource = RefundResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
