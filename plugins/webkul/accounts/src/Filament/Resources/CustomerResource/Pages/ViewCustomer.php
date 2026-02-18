<?php

namespace Webkul\Account\Filament\Resources\CustomerResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Account\Filament\Resources\CustomerResource;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\ViewPartner;

class ViewCustomer extends ViewPartner
{
    protected static string $resource = CustomerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('accounts::filament/resources/customer/pages/view-customer.title');
    }
}
