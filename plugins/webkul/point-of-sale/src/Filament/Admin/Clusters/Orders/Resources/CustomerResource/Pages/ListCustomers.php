<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource\Pages\ListCustomers as BaseListCustomers;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource;

class ListCustomers extends BaseListCustomers
{
    protected static string $resource = CustomerResource::class;
}
