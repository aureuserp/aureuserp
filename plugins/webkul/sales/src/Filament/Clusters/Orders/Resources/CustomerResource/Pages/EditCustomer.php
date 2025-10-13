<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Illuminate\Contracts\Support\Htmlable;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\EditPartner as BaseEditCustomer;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditCustomer extends BaseEditCustomer
{
    use HasRecordNavigationTabs;

    protected static string $resource = CustomerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('sales::filament/clusters/orders/resources/customer/pages/edit-customer.title');
    }
}
