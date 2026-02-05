<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\CustomerResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\CustomerResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ViewVendor as BaseViewCustomer;

class ViewCustomer extends BaseViewCustomer
{
    protected static string $resource = CustomerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('Customer');
    }
}
