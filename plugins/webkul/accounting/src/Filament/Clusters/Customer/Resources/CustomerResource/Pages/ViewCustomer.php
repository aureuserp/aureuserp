<?php

namespace Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Accounting\Filament\Clusters\Customer\Resources\CustomerResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ViewVendor as BaseViewCustomer;

class ViewCustomer extends BaseViewCustomer
{
    protected static string $resource = CustomerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Customer');
    }
}
