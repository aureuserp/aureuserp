<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource\Pages\ManageContacts as BaseManageContacts;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\CustomerResource;

class ManageContacts extends BaseManageContacts
{
    protected static string $resource = CustomerResource::class;
}
