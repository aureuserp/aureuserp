<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Account\Filament\Resources\RefundResource\Pages\ViewRefund as BaseViewRefund;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource;

class ViewRefund extends BaseViewRefund
{
    protected static string $resource = RefundResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
