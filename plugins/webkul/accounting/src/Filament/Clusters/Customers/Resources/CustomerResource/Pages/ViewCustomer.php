<?php

namespace Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource\Pages\ViewCustomer as BaseViewCustomer;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\CustomerResource;

class ViewCustomer extends BaseViewCustomer
{
    protected static string $resource = CustomerResource::class;
}
