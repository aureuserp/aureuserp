<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Filament\Pages\SubNavigationPosition;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\ViewPartner as BaseViewCustomer;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;

class ViewCustomer extends BaseViewCustomer
{
    protected static string $resource = CustomerResource::class;

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }
}
