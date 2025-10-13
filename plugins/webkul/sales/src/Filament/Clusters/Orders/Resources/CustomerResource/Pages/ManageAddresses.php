<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages\ManageAddresses as BaseManageAddresses;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageAddresses extends BaseManageAddresses
{
    use HasRecordNavigationTabs;

    protected static string $resource = CustomerResource::class;
}
