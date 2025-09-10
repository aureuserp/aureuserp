<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\Customers\Pages;

use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Partner\Filament\Resources\Partners\Pages\ViewPartner as BaseViewCustomer;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Customers\CustomerResource;

class ViewCustomer extends BaseViewCustomer
{
    protected static string $resource = CustomerResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
