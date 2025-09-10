<?php

namespace Webkul\Contact\Filament\Resources\Partners\Pages;

use Webkul\Contact\Filament\Resources\Partners\PartnerResource;
use Webkul\Partner\Filament\Resources\Partners\Pages\ManageAddresses as BaseManageAddresses;

class ManageAddresses extends BaseManageAddresses
{
    protected static string $resource = PartnerResource::class;
}
