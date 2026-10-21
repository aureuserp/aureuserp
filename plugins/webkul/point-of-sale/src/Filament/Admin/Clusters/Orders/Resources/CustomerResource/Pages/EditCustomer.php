<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource\Pages\EditCustomer as BaseEditCustomer;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource;

class EditCustomer extends BaseEditCustomer
{
    protected static string $resource = CustomerResource::class;
}
