<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource;
use Webkul\Account\Filament\Resources\CustomerResource\Pages\ViewCustomer as BaseViewCustomer;

class ViewCustomer extends BaseViewCustomer
{
    protected static string $resource = CustomerResource::class;
}
