<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Invoice\Filament\Clusters\Customer\Resources\PartnerResource\Pages\ManageContacts as BaseManageContacts;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageContacts extends BaseManageContacts
{
    use HasRecordNavigationTabs;

    protected static string $resource = CustomerResource::class;
}
